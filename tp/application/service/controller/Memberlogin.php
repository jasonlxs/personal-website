<?php
/**
 * @Author: Marte
 * @Date:   2018-04-01 21:42:48
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-06 11:30:41
 */
namespace app\service\controller;

use think\Request;
use think\Session;
use Models\M3Result;
use Verify\Verify;
use app\data\model\DBmember;
use app\service\validate\Valuser;

class Memberlogin{
   //登录验证
    public function login(){

         $request=Request::instance();

         if($request->post()!=null){
           $m3_result=new M3Result;
           $verify=new Verify;

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

           //验证验证码
           if(!$verify->check($code)){
                 $m3_result->status=8;
                 $m3_result->message='验证码不正确,请重新输入';
                 return $m3_result->toJson();
           }


           //验证账号或密码
           if(strpos($uname,'@')){
               if(!$member=DBmember::get(['email'=>$uname,'upwd'=>$upwd])){
                     $m3_result->status=12;
                     $m3_result->message='账号或密码不正确,请重新输入';
                     return $m3_result->toJson();
                   }
           }else{
                 if(!$member=DBmember::get(['phone'=>$uname,'upwd'=>$upwd])){
                     $m3_result->status=12;
                     $m3_result->message='账号或密码不正确,请重新输入';
                     return $m3_result->toJson();
               }
           }
           $member=$member->toArray();
           //验证是否激活
           if(!$member['is_active']){
                $m3_result->status=13;
                $m3_result->message='账号未激活,请登录邮箱进行激活';
                return $m3_result->toJson();
           }

           //表单验证
           if($token!=Session::get('__token__')){
                $m3_result->status=8;
                $m3_result->message='非法登录！';
                return $m3_result->toJson();
           }

            Session::prefix('member');
            Session::set('membertime',time(),'member');
            Session::set('mid',$member['id'],'member');
            Session::set('mname',$uname,'member');
            //增强验证信息
            Session::set('check',$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'],'member');

            $m3_result->status=14;
            $m3_result->message='登录成功';
            $m3_result->id=$member['id'];
            $m3_result->uname=$uname;

            return  $m3_result->toJson();
        }
    }


     public function loginout(){

        $request=Request::instance();
        $m3_result=new M3Result;

        $prefix=$request->param('prefix','');
        Session::clear($prefix);
        $m3_result->status=1;
        $m3_result->message='安全退出';
        return $m3_result->toJson();
    }
}