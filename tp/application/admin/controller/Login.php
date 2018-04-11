<?php
/**
 * @Author: Marte
 * @Date:   2018-03-08 20:07:25
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-06 13:41:56
 */
namespace app\admin\controller;
use think\Controller;
use think\Request;
use think\Session;
use Verify\Verify;
use app\data\model\DBuser;
use app\service\validate\Valuser;

class Login extends Controller{
    //登录页面
    public function index(){

        return $this->fetch();
    }
    //登录验证
    public function login(){
        //接收请求数据
        $request=Request::instance();
        $uname=$request->param('uname');
        $upwd=md5($request->param('upwd'));
        $code=$request->param('code');



        $data = [
            'uname'=>$uname,
            'upwd'=>$upwd,
            'code'=>$code,
            '__token__'=>$request->param('__token__')
        ];

        $validate = new Valuser;

        if(!$validate->check($data)){
            $this->error($validate->getError());
        }
        //验证码验证
        $verify=new Verify();
        if(!$verify->check($code)){
            $this->error('验证码不正确，请重新输入');
        }
        //用户密码验证
        if($user=DBuser::get(['uname'=>$uname,'upwd'=>$upwd])){
             //判断是否锁定
             if($user->lock==1)$this->error('用户已锁定，请联系管理员','Admin/Login/index');
             //更新登录时间
             $uid=$user->id;
             $now=time();
             $ip=$_SERVER['REMOTE_ADDR'];
             $user->logintime=$now;
             $user->loginip=$ip;
             $user->save();


            //将登录信息保存的user作用域的session中
            Session::prefix('user');
            Session::set('uid',$uid,'user');
            Session::set('uname',$uname,'user');
            Session::set('logintime',$now,'user');
            Session::set('loginip',$ip,'user');


            $this->redirect("admin/Index/index");
        }else{
              $this->error('用户或密码错误，请重新输入!');
        }

    }
     //退出登录
    public function loginout(){
        Session::clear('user');
        $this->redirect('admin/login/index');
    }

     public function cs(){
        $str='我是谁';
        $str=substr($str,0,3);
        echo $str;
        die;
        return $this->fetch();
    }
}
