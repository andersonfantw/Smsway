<?php
namespace Cuby\Smsway;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Traits\ForwardsCalls;
use Illuminate\Support\Facades\Validator;

use Carbon\Carbon;
use CubyBase\Common\Phone;
use CubyBase\SMS\SMSMessageable;
use CubyBase\Events\SystemWarningEvent;
use CubyBase\Events\SystemNoticeEvent;

class SmswayMessage extends Notification implements ShouldQueue
{
    use ForwardsCalls;
    use Queueable;
    use SMSMessageable {
        isValidDate as protected traitIsValidDate;
    }

    protected $limit_per_request;
    protected $arrServiceCountry;
    protected $maxWords;

    private $valid = true;

    public $content;
    public $recipients = [];
    public $encode = 0;
    public $dos;
    public $senderid = '';

    //驗證訊息
    private $valid_messages = [
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
    private $param_def_table;

    public static function create($content = '')
    {
        return new static($content);
    }

    public function __construct($content = '')
    {
        $this->limit_per_request = config('Smsway.limit_per_request');
        $this->senderid = config('services.Smsway.senderid');
        $this->maxWords = config('Smsway.max_words');

        // 驗證條件
        $this->param_def_table = [
            'sendTime' => [
                'name'=>'短訊發送日期和時間',
                'desc'=>'格式是 YYYY-MM-DD hh:mm。如果想即時傳送短訊，請填上“now”。',
                'validate'=>[
                    'required',
                    'date',
                ],
            ],
            'mobile' => [
                'name'=>'短訊接收者',
                'desc'=>'國家碼 + 流動電話號碼。每一次 HTTP API 只限傳送 20 個電話號碼。',
                'validate'=>'',
            ],
            'content' => [
                'name'=>'短訊內容 ',
                'desc'=>'英文短訊只限英文、數目字及標點符號；統一碼短訊以Unicode-16 編碼，適用所有語言，包括繁體、簡體、英文、數目字及標點符號等等。對於外地及香港短訊，例如香港、澳門等等，英文短訊字數長度最多是 459 個字母；而統一碼短訊字數長度只限於 201 個字母',
                'validate'=>'',
            ],
        ];

        if (!empty($content)) $this->content($content);
    }

    /**
     * Handle dynamic method calls into the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->forwardCallTo($this, $method, $parameters);
    }

    /**
     * Handle dynamic static method calls into the method.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public static function __callStatic(string $method, array $parameters)
    {

        return (new static)->$method(...$parameters);
    }

    protected function isValid()
    {
        // param_def_table
        // $v =  Validator::make(
        //     [
        //        'dos' => $this->dos,
        //        'senderid' => $this->senderid,
        //        'recipient' => implode(',',$this->recipients),
        //        'content' => $this->content,
        //        'langeng' => $this->encode,
        //     ],
        //     array_map(function($v){
        //         return $v['validate'];
        //     }, $this->param_def_table),
        //     $this->valid_messages
        // );
        // return $this->valid && $v->passes();
        return $this->valid;
    }

    protected function content($content)
    {
        // 以預設英文驗證長度
        $this->encode = 0;
        if(strlen($content)>$this->maxWords['content'][$this->encode])
        {
            // 簡訊長度超過限制
            event(new SystemNoticeEvent(
                __CLASS__.'::'.__METHOD__,
                sprintf('%s字數超過限制',$this->param_def_table['content']['name']),
                sprintf('發送的%s字數超過限制。%s', 
                    $this->param_def_table['content']['name'],
                    $this->param_def_table['content']['desc']
                ),
                $content
            ));
        }

        $this->content = trim($content);

        return $this;
    }

    protected function title($title){
        $this->senderid = $title;

        preg_match_all('/([\w~!@:;<>#\,\.\$%\^&*\(\)\-\=\+\*\\\]+)/',$title,$matches);
        if(implode('',$matches[0])!=$title){
            // 不合法的字元
            event(new SystemNoticeEvent(
                __CLASS__.'::'.__METHOD__,
                sprintf('%s有不合法的字元',$this->param_def_table['senderid']['name']),
                '輸入的符號不包含單引號及雙引號',
                $title
            ));
        }else{
            preg_match_all('/(\d+)/',$title,$matches);
            if(strlen($title) > $this->maxWords['title'][(implode('',$matches[0])==$title)])
            {
                event(new SystemNoticeEvent(
                    __CLASS__.'::'.__METHOD__,
                    sprintf('%s超過限制字數',$this->param_def_table['senderid']['name']),
                    $this->param_def_table['senderid']['desc'],
                    $title
                ));
            }
        }

        return $this;
    }

    protected function recipient($recipient): SmswayMessage
    {
        if(count($this->recipients) >= $this->limit_per_request)
        {
            event(new SystemWarningEvent(
                __CLASS__.'::'.__METHOD__,
                sprintf('%s數量超過限制',$this->param_def_table['recipient']['name']),
                sprintf('一次最多傳送%s筆簡訊。%s', 
                    $this->limit_per_request,
                    $this->param_def_table['recipient']['desc']
                ),
                ''
            ));
        }
        if(Phone::create($recipient)->isValid()){
            $this->recipients[] = $recipient;
        }else{
            event(new SystemWarningEvent(
                __CLASS__.'::'.__METHOD__,
                sprintf('無效的%s',$this->param_def_table['recipient']['name']),
                '不存在的國碼或不正確的電話位數。',
                $recipient
            ));
        }

        return $this;
    }

    protected function at($at='now')
    {
        if($at!='now' && !$this->isValidDate($at))
        {
            event(new SystemWarningEvent(
                __CLASS__.'::'.__METHOD__,
                sprintf('無效的%s',$this->param_def_table['dos']['name']),
                $this->param_def_table['dos']['desc'],
                $at
            ));
        }else{
            if($at=='now'){
                $this->dos = $at;
            }else{
                if(Carbon::parse($at)->lte(Carbon::now()))
                {
                    // 發送的時間不能在過去的日期
                    event(new SystemWarningEvent(
                        __CLASS__.'::'.__METHOD__,
                        sprintf('不合法的%s',$this->param_def_table['dos']['name']),
                        '發送的時間不能在過去的日期',
                        $at
                    ));
                }
                $this->dos = Carbon::parse($at)->format('Y-m-d H:i');
            }
        }

        return $this;
    }

    protected function toJson()
    {
        return json_encode($this);
    }

    protected function economic()
    {
        $this->senderid ='RANDOMID';

        return $this;
    }

    protected function unicode()
    {
        if($this->encode == 1) return $this;

        if(strlen($this->content)>$this->maxWords['content'][$this->encode])
        {
            // 簡訊長度超過限制
            event(new SystemNoticeEvent(
                __CLASS__.'::'.__METHOD__,
                sprintf('%s長度超過限制',$this->param_def_table['content']['name']),
                $this->this->param_def_table['content']['desc'],
                $this->content
            ));
        }else{
            $this->encode = 1;
            $_content = $this->Text2Unicode($this->content);
            $this->content = $_content;
        }
        return $this;
    }

    private function isValidDate($date){
        try{
            $_date = Carbon::parse($date);
            return true;
        }catch(\Exception $e){
            return false;
        }
        //if(!$this->traitIsValidDate($date)) $this->valid = false;
    }
}
