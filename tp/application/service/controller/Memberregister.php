<?php
/**
 * @Author: Marte
 * @Date:   2018-04-01 22:10:55
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-07 21:13:07
 */
namespace app\service\controller;

use think\Request;
use think\Session;
use Models\M3Result;
use Verify\Verify;
use Email\Smtp;
use app\data\model\DBmember;
use app\data\model\DBtempp;
use app\service\validate\Valuser;
//http://localhost/TP5/public/mobile/register/index?uname=lxsptlxs@163.com&&upwd=8237330&&code=d&&session_id=9v8urq0fcbfju9f5q00rvm9og2
class Memberregister{

    public function register(){

        $request=Request::instance();
        $m3_result=new M3Result;

        if($request->post()!=null){

            $uname=$request->param('uname');
            $upwd=md5('lxs'.$request->param('upwd'));
            $code=$request->param('code');
            $token=$request->param('__token__');

           //值验证
           $data=[
             'uname'=>$uname,
             'upwd'=>$upwd,
             'code'=>$code,
           ];

           $validate = new Valuser;

           if(!$validate->check($data)){
                $m3_result->status=8;
                $m3_result->message=$validate->getError();
                return $m3_result->toJson();
            }

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

                         //表单验证防跨域
                        if($token!=Session::get('__token__')){
                            $m3_result->status=8;
                            $m3_result->message='非法注册';
                            return $m3_result->toJson();
                        }

                        //保存注册信息
                        $member=new DBmember;
                        $member->email=$uname;
                        $member->upwd=$upwd;
                        $member->update_at=date("Y-m-d H:i:s",time());
                        $member->save();
                        //对新用户ID进行base64编码
                        $uid=base64_encode($member->id);
                        //配置url地址字符串
                        $protocol = empty($_SERVER['HTTP_X_CLIENT_PROTO']) ? 'http://' : $_SERVER['HTTP_X_CLIENT_PROTO'] . '://';
                        $str=url();
                        $str=substr($str,0,strripos($str,'/'));
                        $url=$protocol.$_SERVER['HTTP_HOST'].$str."/checkMail/uid/".$uid;

                        //发送验证邮件
                        //******************** 配置信息 ********************************
                        $smtpserver = "smtp.163.com";//SMTP服务器
                        $smtpserverport =25;//SMTP服务器端口
                        $smtpuser = "m15820243257@163.com";//SMTP服务器的用户帐号，注：部分邮箱只需@前面的用户名
                        $smtppass = "8237330@lxs";//SMTP服务器的用户密码

                        $smtpusermail = "m15820243257@163.com";//SMTP服务器的用户邮箱
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
                     //邮箱注册结束
              }else{
                 //手机号注册开始
                 $temp=DBtempp::get(function($query)use($uname){
                     $query->where('phone', $uname)->order('id DESC')->limit(1);
                   });

                 if($temp){

                     if($temp['code']==$code && time()<$temp['deadline']){
                        //表单验证防跨域
                         if($token!=Session::get('__token__')){
                                $m3_result->status=8;
                                $m3_result->message='非法注册';
                                return $m3_result->toJson();
                         }
                         $member=new DBmember;
                         $member->phone=$uname;
                         $member->upwd=$upwd;
                         $member->update_at=date("Y-m-d H:i:s",time());
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
              //手机号注册结束
        }
    }

    //验证邮箱
    public function checkMail(){

        $request=Request::instance();
        $uid=base64_decode($request->param('uid'));
        $member=DBmember::get(['id'=>$uid]);
        $member->is_active=1;
        $member->save();
        return '邮件激活成功';
    }
}