<?php

/**
 * Created by JetBrains PhpStorm.
 * function:短信发送类
 * User: GaoJiyong
 * Date: 2014-02-21 下午5:19
 *
 */
class SmsMandao
{

    const SMS_TIME_LENGTH = 60;


    //新接口
    public  function  send($phone, $content)
    {
        $flag = 0;
        //要post的数据
        $argv = array(
            'sn' => 'SDK-BBX-010-19992', ////替换成您自己的序列号
            'pwd' => strtoupper(md5('SDK-BBX-010-19992' . '35f6bcb=')), //此处密码需要加密 加密方式为 md5(sn+password) 32位大写
            'mobile' => $phone,//手机号 多个用英文的逗号隔开 post理论没有长度限制.推荐群发一次小于等于10000个手机号
            'content' => iconv("UTF-8", "gb2312//IGNORE", $content),//短信内容
            'ext' => '',
            'stime' => '',//定时时间 格式为2011-6-29 11:09:21
            'rrid' => ''
        );
        //构造要post的字符串
        $params = "";
        foreach ($argv as $key => $value) {
            if ($flag != 0) {
                $params .= "&";
                $flag = 1;
            }
            $params .= $key . "=";
            $params .= urlencode($value);
            $flag = 1;
        }
        $length = strlen($params);
        //创建socket连接
        $fp = fsockopen("sdk2.entinfo.cn", 8060, $errno, $errstr, 10) or exit($errstr . "--->" . $errno);
        //构造post请求的头
        $header = "POST /webservice.asmx/mt HTTP/1.1\r\n";
        $header .= "Host:sdk2.entinfo.cn\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . $length . "\r\n";
        $header .= "Connection: Close\r\n\r\n";
        //添加post的字符串
        $header .= $params . "\r\n";
        //发送post的数据
        fputs($fp, $header);
        $inheader = 1;
        while (!feof($fp)) {
            $line = fgets($fp, 1024); //去除请求包的头只显示页面的返回数据
            if ($inheader && ($line == "\n" || $line == "\r\n")) {
                $inheader = 0;
            }
            if ($inheader == 0) {
                // echo $line;
            }
        }
//        return $line;
        //<string xmlns="http://tempuri.org/">-5</string>
        $line = str_replace("<string xmlns=\"http://tempuri.org/\">", "", $line);
        $line = str_replace("</string>", "", $line);
        $result = explode("-", $line);
        // echo $line."-------------";
        if (count($result) > 1)
            return false;
//            echo '发送失败返回值为:'.$line.'。请查看webservice返回值对照表';
        else
            return 100;
//            echo '发送成功 返回值为:'.$line;
    }





}