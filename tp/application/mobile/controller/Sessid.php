<?php
/**
 * @Author: Marte
 * @Date:   2017-08-15 10:40:29
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-01-08 17:18:00
 */
namespace app\mobile\controller;
/**
 * 随机生成SessionID
 */
class Sessid{

    public function getId(){
        session_start();



        return session_id();
        die;
        $wx_code=input('code');


        //登录凭证请求地址
        $url="https://api.weixin.qq.com/sns/jscode2session";
        //提交参数
        $postData=[
             'appid'=>'wx937d4f66aedcacda',
             'secret'=>'c4933b40fcad49adc4df77bf874c9cca',
             'js_code'=>$wx_code,
             'grant_type'=>'authorization_code'
           ];

        $URL=parse_url($url);
        if(!isset($URL['port'])){
            $URL['port'] = 443;
        }

        $postData=http_build_query($postData);
        $request="POST ".$URL['path']." HTTP/1.1\r\n";
        $request.="Host:".$URL['host']."\r\n";
        $request.="Content-type:application/json\r\n";
        $request.="Content-length:".strlen($postData)."\r\n\r\n";
        $request.=$postData;

        try{
            $fp = fsockopen('ssl://'.$URL['host'], $URL['port']);
            fwrite($fp, $request);
            $res = fread($fp, 1024);
        }catch(Exception $e){
            fclose($fp);
            return false;
        }
        fclose($fp);

        $res=explode("\r\n\r\n",$res);
        $restr=json_decode($res[1],true);

        if(isset($restr['errcode'])){
                $m3_result->status=$restr['errcode'];
                $m3_result->message=$restr['errmsg'];
                return $m3_result->toJson();
            }

        $key=session_id();

        $value=$restr['openid'].$restr['session_key'];
        session($key,$value);

        return $key;
    }
}
