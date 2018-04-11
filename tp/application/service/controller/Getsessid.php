<?php
/**
 * @Author: Marte
 * @Date:   2018-04-01 23:33:21
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-01 23:36:25
 */
namespace app\service\controller;

use think\Session;


class Getsessid{

    //重新获取一个session_id
    public function index(){

        return Session::regenerate();
    }
}