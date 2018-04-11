<?php
/**
 * @Author: Marte
 * @Date:   2018-03-08 20:12:41
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-07 12:30:34
 */
namespace app\admin\controller;
use think\Request;
use think\Controller;
use think\Session;
use app\data\model\DBrule;
use Auth\Auth;

class Common extends Controller{

    public function _initialize(){
          //未登录重定向
          if(!Session::get('uname','user'))$this->redirect('Admin/Login/index');

          /*权限验证*/
          $requset=Request::instance();
          $model=strtolower($requset->module()).'/'.strtolower($requset->controller()).'/'.strtolower($requset->action());

          $auth=new Auth;
          if(Session::get('uname','user')!='admin' && !$auth->check($model,Session::get('uid','user'))){
              echo '您没有权限!';die;
          }
    }
}