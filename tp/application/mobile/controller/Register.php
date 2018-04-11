<?php
/**
 * @Author: Marte
 * @Date:   2017-08-11 10:09:33
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-01 23:29:57
 */
namespace app\mobile\controller;

use \think\Request;
use app\data\model\DBmember;
use app\data\model\DBtempp;
use SMS\SendTemplateSMS;
use Models\M3Result;
use Verify\Verify;
use Email\Smtp;
//http://localhost/TP5/public/mobile/register/index?uname=lxsptlxs@163.com&&upwd=8237330&&code=d&&session_id=9v8urq0fcbfju9f5q00rvm9og2
class Register{

    //注册
    public function index(){
      $request=Request::instance();

      $m3_result=new M3Result;

      if($request->post()!=null && $request->param('code')!=null){
        //数据过滤
        $uname=$request->param('uname');
        $upwd=md5('lxs'.$request->param('upwd'));
        $code=$request->param('code');

         //验证注册
        if(strpos($uname,'@')){
             //邮箱注册
             $verify=new Verify;
             if($verify->check($code)){
                //验证邮箱是否已被注册
                if(DBmember::get(['email'=>$uname])){
                    $m3_result->status=11;
                    $m3_result->message='邮箱已被注册,请重新输入';
                    return $m3_result->toJson();
                }
                //保存注册信息
                $member=new DBmember;
                $member->email=$uname;
                $member->upwd=$upwd;
                $member->update_at=time();
                $member->save();
                //对新用户ID进行base64编码
                $uid=base64_encode($member->uid);
                //配置url地址字符串
                $protocol = empty($_SERVER['HTTP_X_CLIENT_PROTO']) ? 'http://' : $_SERVER['HTTP_X_CLIENT_PROTO'] . '://';
                $str=url();
                $str=substr($str,0,strripos($str,'/'));
                $url=$protocol.$_SERVER['HTTP_HOST'].$str."/checkMail/uid/".$uid;

                //发送验证邮件
                //******************** 配置信息 ********************************
                $smtpserver = "smtp.163.com";//SMTP服务器
                $smtpserverport =25;//SMTP服务器端口
                $smtpuser = "15820243257@163.com";//SMTP服务器的用户帐号，注：部分邮箱只需@前面的用户名
                $smtppass = "8237330lxs";//SMTP服务器的用户密码

                $smtpusermail = "15820243257@163.com";//SMTP服务器的用户邮箱
                $smtpemailto = $uname;//发送给谁
                $mailtitle = 'jasonlxs博客店用户验证激活';//邮件主题
                $mailcontent = "请点击此链接激活用户:<i><a href='".$url."' target='_blank'>主人,点点我就激活了</a></i><br/>如果无法点击或无效，请复制该地址到浏览器并访问".$url;//邮件内容
                $mailtype = "HTML";//邮件格式（HTML/TXT）,TXT为文本邮件
                //************************ 发送邮件 ****************************
                $smtp = new Smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);//这里面的一个true是表示使用身份验证,否则不使用身份验证.
                $smtp->debug = false;//是否显示发送的调试信息
                $state = $smtp->sendmail($smtpemailto, $smtpusermail, $mailtitle, $mailcontent, $mailtype);

                 if($state==""){
                    $m3_result->status=10;
                    $m3_result->message='验证邮箱发送失败,请重试';
                    return $m3_result->toJson();
                 }else{
                    $m3_result->status=9;
                    $m3_result->message='注册成功,请登录邮箱激活账户';
                    return $m3_result->toJson();
                 }

             }else{

                 $m3_result->status=8;
                 $m3_result->message='验证码不正确';
                 return $m3_result->toJson();
             }
          }else{
             //手机号注册
             $temp=DBtempp::get(function($query)use($uname){
                 $query->where('phone', $uname)->order('id DESC')->limit(1);
               });
             $temp=$temp->toArray();
             if($temp){
                 if($temp['code']==$code && time()<strtotime($temp['deadline'])){
                     $member=new DBmember;
                     $member->phone=$uname;
                     $member->upwd=$upwd;
                     $member->update_at=time();
                     $member->is_active=1;
                     $member->save();
                     $m3_result->status=7;
                     $m3_result->message='注册成功';
                     return $m3_result->toJson();
                 }else{
                    $m3_result->status=6;
                    $m3_result->message='手机验证码不正确或过期';
                    return $m3_result->toJson();
                 }
             }else{
                 $m3_result->status=5;
                 $m3_result->message='您未发送手机验证码，请重新发送';
                 return $m3_result->toJson();
             }
          }

      }else{
         $m3_result->status=4;
         $m3_result->message='验证码不能为空';
         return $m3_result->toJson();
      }
}

   //验证邮箱
    public function checkMail(){
        $uid=base64_decode(input('uid'));
        $member=DBmember::get(['uid'=>$uid]);
        $member->isActive=1;
        $member->save();
        return '邮件激活成功';
    }
}