<?php

return [
    'limit_per_request' => 20,
    'functions' => [
        'send_sms' => [
            'url'=>'http://api.smsway.com.cn/api/send',
            'query'=>['apiAccount','secretKey','content','mobiles','subPort','sendTime']
        ],
        'get_sms_status' => [
            'url'=>'https://www.meteorsis.com/misweb/f_getsmsdstatus.aspx',
            'query'=>['username','password','smsdid']
        ],
        'get_account_balance' => [
            'url'=>'https://www.meteorsis.com/misweb/f_getuserbalance.aspx',
            'query'=>['username','password'],
        ],
        'get_server_query' => [
            'url'=>'https://www.meteorsis.com/misweb/f_getserverqueue.aspx',
            'query'=>['username','password'],
        ],
        'callback' => [
            'url'=>'/receivedr.php',
            'query'=>['smsdid','recipient','senderid','content','dos','smsdstatus','smsderrorcode','charged'],
        ],
    ],
    'api_err_code' => [
        0 => '成功',
        1 => '登录密码错误',
        2 => '企业ID或登录名错误',
        3 => '余额不足',
        4 => '用户归属服务器错误',
        5 => '帐户停用或不存在',
        6 => '内容为空',
        7 => '号码为空',
        8 => '号码超过最大限制数',
        9 => '内容包含关键字',
        10=> '时间格式错误',
        11=> '非法操作导致ip被锁',
        12=> '访问过快',
        13=> '批量一对一参数格式错误',
        14=> '批量一对一出现重复信息(号码，内容同时重复)',
        15=> '签名未报备',
        16=> '单位时间内该号码超过最大发送限制',
        17=> '签名必须在【4，10】字符间',
        18=> '内容涉嫌营销内容',
        19=> '模板不支持所发送的号码类型,请检查模板所支持的运营商类型',
        20=> '企业产品未配置',
        21=> '全部号码校验不通过',
        22=> 'Json参数错误',
        23=> '号码对应区域无定价方案,无法扣费',
        24=> '所有号码均不支持发送【①部分号码校验不通过;②部分号码对应区域无定价方案,无法扣费】',
        25=> '没有可发送的运营商通道',
        26=> '短信标题为空',
        27=> '没有分配有效通道分组',
        82=> '视频短信变量非法',
        83=> '视频短信模板变量个数最多支持10个',
        84=> '视频短信模板不存在',
        85=> '视频短信压缩包素材个数不得超过40个',
        86=> '参数content必须为zip文件流经base64编码后的字符串,且文件字符集格式为UTF-8',
        87=> '视频模板zip文件内容为空',
        88=> '视频短信模板过大，总大小超过1.9M',
        89=> '模板标题字数太长，最长20个字',
        90=> '不属于自己的视频模板',
        91=> '视频短信模板素材文件格式只支持【txt,jpg,gif,mp3,mp4,3gp】',
        92=> '视频短信模板内容中签名与参数sign签名不一致',
        93=> '模板内容中存在双签名',
        94=> '签名不可为空',
        95=> '模板未过审',
        96=> '文件获取失败',
        97=> '发送总计价为零',
        98=> '服务调用超时',
        99=> '系统内部错误',
    ],
    'service_country' => [
        '*' //'zh-cn','zh-hk','zh-mo','zh-tw',
    ],
    'max_words' => [
        'title' => [11,16],     //英文+數字, 數字
        'content' => [459,201], //英文+數字+符號, Unicode-16
    ],
];