<?php
/**
 * @Author: Marte
 * @Date:   2018-04-01 21:34:01
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-07 20:51:57
 */
namespace app\service\controller;

use think\Request;
use SMS\SendTemplateSMS;
use Models\M3Result;
use app\data\model\DBtempp;
use app\data\model\DBmember;

class Sendcode{
    //发送手机验证码
    public function sendPhoneCode(){
         $request=Request::instance();
         //接收手机号参数
         $m3_result=new M3Result;
         $phone=$request->param('phone');
         if($phone==''){
            $m3_result->status=0;
            $m3_result->message='手机号不能为空';
            return  $m3_result->toJson();
         }
         //判断该手机号是否已经注册
          $tempPhone=DBmember::get(['phone'=>$phone]);
         if($tempPhone!=null){
                $m3_result->status=3;
                $m3_result->message='手机号已被注册';
                return  $m3_result->toJson();
         }
         //发送短信
         $sendTemplateSMS=new SendTemplateSMS;
         $code='';
         $charset="1234567890";
         $len=strlen($charset)-1;
         for($i=0;$i<6;$i++){
             $code.=$charset[mt_rand(0,$len)];
         }
         $m3_result=$sendTemplateSMS->sendTemplateSMS($phone,[$code,60],1);

         if($m3_result->status==1){
             //保存到数据库
             $tempPhone=new DBtempp;
             $tempPhone->phone=$phone;
             $tempPhone->code=$code;
             $tempPhone->deadline=time()+60*60;
             $tempPhone->save();
         }

         return  $m3_result->toJson();
    }
}