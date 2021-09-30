<?php
namespace Cuby\Smsway;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

use Cuby\Smsway\Events\SmswayGetAccountBalanceEvent;
use Cuby\Smsway\Events\SmswayGetServerQueryEvent;
use Cuby\Smsway\Events\SmswayGetSMSStatusEvent;
use Cuby\Smsway\Events\SmswaySendSMSEvent;
use CubyBase\Events\SystemCriticalEvent;
use CubyBase\Events\SystemErrorEvent;
use CubyBase\Events\SystemWarningEvent;
use CubyBase\SMS\SMSInterface;
use CubyBase\SMS\SMSAbstract;
use Cuby\Smsway\SmswayMessage;

class SmswayService extends SMSAbstract implements SMSInterface
{
    //驗證訊息
    protected $valid_messages = [
        'required'=>':attribute 欄位為必填',
        'boolean'=>'超出預期的輸入值輸入值',
        'date'=>'不正確的日期格式',
        'regex'=>'非法的:attribute',
        'max'=>'字數超過限制的長度:max',
        'numeric'=>'必須為數字',
        'between'=>'超過預期的金額',
        'senderid.regex'=>'非法的短訊顯示名稱',
        'smsdid.regex'=>'超出預期的字串',
    ];
    protected $param_def_table;

    private $functions;
    private $api_err_code;
    private $sms_status;
    private $sms_status_err_code;
    private $arrServiceCountry;
    private $maxWords;
    function __construct(){
        $this->functions = config('Smsway.functions');
        $this->api_err_code = config('Smsway.api_err_code');
        $this->sms_status = config('Smsway.sms_status');
        $this->sms_status_err_code = config('Smsway.sms_status_err_code');
        $this->arrServiceCountry = config('Smsway.service_country');
        $this->maxWords = config('Smsway.max_words');

        $this->base_param_table = [
            'username' => config('services.Smsway.key'),
            'password' => config('services.Smsway.secret'),
        ];

        // 驗證條件
        $this->param_def_table = [
            'dos' => [
                'name'=>'短訊發送日期和時間',
                'desc'=>'格式是 YYYY-MM-DD hh:mm。如果想即時傳送短訊，請填上“now”',
                'validate'=>[
                    'required',
                    'date',
                ],
            ],
            'senderid' => [
                'name'=>'短訊顯示名稱',
                'desc'=>'11 個位的英文及數字組合或 16 個位的數',
                'validate'=>[
                    'required',
                    'regex:/^((\w{1,11})|(\d{1,16}))$/g',
                ],
            ],
            'recipient' => [
                'name'=>'短訊接收者',
                'desc'=>'國家碼 + 流動電話號碼',
                'validate'=>'',
            ],
            'content' => [
                'name'=>'短訊內容 ',
                'desc'=>'短訊首 5 個字母，以 Unicode-16 編碼',
                'validate'=>'',
            ],
            'username' => [
                'name'=>'登入名稱',
                'desc'=>'字數長度只限於 20 個字母',
                'validate'=>[
                    'required',
                    'max:20',
                ],
            ],
            'password' => [
                'name'=>'登入密碼',
                'desc'=>'字數長度只限於 20 個字母',
                'validate'=>[
                    'required',
                    'max:20',
                ],
            ],
            'smsdid' => [
                'name'=>'短訊識別號',
                'desc'=>'6 至 9 個位的數字',
                'validate'=>[
                    'required',
                    'regex:/^\d{6,9}$/',
                ],
            ],
            'smsdstatus' => [
                'name'=>'短訊狀態',
                'desc'=>'詳情可参考章節“7.2 短訊狀態”',
                'validate'=>[
                    'required',
                    function($attribute, $value, $fail){
                        if(!array_key_exists($value,$this->sms_status)) $fail($attribute,'超出預期的回傳值');
                    },
                ],
            ],
            'smsderrorcode' => [
                'name'=>'短訊狀態錯誤碼',
                'desc'=>'詳情可参考章節“7.3 短訊狀態錯誤碼”',
                'validate'=>[
                    'required',
                    function($attribute, $value, $fail){
                        if(!array_key_exists($value,$this->sms_status_err_code)) $fail($attribute,'超出預期的回傳值');
                    },
                ],
            ],
            'charged ' => [
                'name'=>'傳送短訊的收費',
                'desc'=>'以港元為單位',
                'validate'=>[
                    'required',
                    'numeric',
                    'between:0.01,2',
                ],
            ],
        ];
    }

    /**
     * 3.1
     * @param \Cuby\Smsway\SmswayMessage $message
     * @return array
     */
    public function send(SmswayMessage $message): array
    {
        $this->base_param_table['mobiles'] = implode(',',$message->recipients);
        $this->base_param_table['sendTime'] = $message->dos;
        $this->base_param_table['content'] = $message->content;

        $result = $this->queryServer('send_sms');
        if(array_key_exists('data',$result)){
            event(new SmswaySendSMSEvent('SmswayService',$result['data']));
        }
        
        return $result;
    }

    /**
     * 3.2
     * @param String $sms_did
     */
    public function getSmsStatus(String $sms_did)
    {
        $this->base_param_table['smsdid'] = $sms_did;
        $result = $this->queryServer('get_sms_status');

        $status_text = array_key_exists($result['STATUS'],$this->sms_status)?$this->sms_status[$result['STATUS']]['status'].'。'.$this->sms_status[$result['STATUS']]['desc']:'';
        $errorcode_text = array_key_exists($result['ERRORCODE'],$this->sms_status_err_code)?$this->sms_status_err_code[$result['ERRORCODE']]:'';
        if(array_key_exists('STATUS',$result)){
            event(new SmswayGetSMSStatusEvent('SmswayService',$sms_did,$result['STATUS'], $status_text, $result['ERRORCODE'], $errorcode_text));
        }
        return $result;
    }
    /**
     * 3.3
     */
    public function getAccountBalance(){
        $result = $this->queryServer('get_account_balance');
        if(array_key_exists('BALANCE',$result)){
            event(new SmswayGetAccountBalanceEvent('SmswayService',$result['BALANCE']));
        }
        return $result;
    }
    /**
     * 3.4
     */
    public function getServerQuery(){
        $result = $this->queryServer('get_server_query');
        if(array_key_exists('QUEUE',$result)){
            event(new SmswayGetServerQueryEvent('SmswayService',$result['QUEUE']));
        }
        return $result;
    }

    public function queryServer(String $url_name): array
    {
        Http::timeout(10)->post([
            
        ]);
        $reponse = Http::timeout(10)->get(
            $this->functions[$url_name]['url'],
            $this->validator_inputs($this->base_param_table, $this->functions[$url_name]['query'])              
        );
        return $this->parseReturn($reponse->body());
    }

    /**
     * abstract public function
     * @param String $response
     * @return array
     */
    public function parseReturn(String $response): array
    {
        $arr = [];
        $a = explode(';',$response);
        foreach($a as $r){
            $a1 = explode(':', $r);
            $arr[$a1[0]] = $a1[1];
        }
        if(array_key_exists('STATUS',$arr)){
            //$sms_status[$arr['STATUS']]
            if(in_array($arr['STATUS'],[1])){
                event(new SystemErrorEvent(
                    __CLASS__,
                    $this->sms_status[$arr['STATUS']]['status'],
                    $this->sms_status[$arr['STATUS']]['desc'],
                    ''
                ));
            }
            if(in_array($arr['STATUS'],[19,20,21])){
                event(new SystemWarningEvent(
                    __CLASS__,
                    $this->sms_status[$arr['STATUS']]['status'],
                    $this->sms_status[$arr['STATUS']]['desc'],
                    ''
                ));
            }
        }
        if(array_key_exists('ERRORCODE',$arr)){
            if($arr['ERRORCODE']!='300'){
                //$sms_status_err_code[$arr['ERRORCODE']]
                if(in_array($arr['ERRORCODE'],[308])){
                    event(new SystemCriticalEvent(
                        __CLASS__,
                        $this->sms_status_err_code[$arr['ERRORCODE']],
                        $this->sms_status_err_code[$arr['ERRORCODE']],
                        ''
                    ));
                }
                if(in_array($arr['ERRORCODE'],[301,302,303,304,305,306,307,309,312,315,326])){
                    event(new SystemWarningEvent(
                        __CLASS__,
                        $this->sms_status_err_code[$arr['ERRORCODE']],
                        $this->sms_status_err_code[$arr['ERRORCODE']],
                        ''
                    ));
                }
            }
        }
        if(array_key_exists('ERROR',$arr)){
            //$api_err_code
            if(in_array($arr['ERROR'],[1,2,3,16])){
                event(new SystemCriticalEvent(
                    __CLASS__,
                    $this->api_err_code[$arr['ERROR']],
                    $this->api_err_code[$arr['ERROR']],
                    ''
                ));
            }
            if(in_array($arr['ERROR'],[20,240,241,242,243,244,245,246,248,249])){
                event(new SystemErrorEvent(
                    __CLASS__,
                    $this->api_err_code[$arr['ERROR']],
                    $this->api_err_code[$arr['ERROR']],
                    ''
                ));
            }
            if(in_array($arr['ERROR'],[5,6,8,9,17,18])){
                event(new SystemWarningEvent(
                    __CLASS__,
                    $this->api_err_code[$arr['ERROR']],
                    $this->api_err_code[$arr['ERROR']],
                    ''
                ));
            }
        }
        return $arr;
    }

    /**
     * 接收sms傳送報告
     * @param Request $request
     * @param null $param_names
     */
    public function callback(Request $request, $param_names=null){
        parent::callback($request,$this->functions['callback']['query']);
    }

}
