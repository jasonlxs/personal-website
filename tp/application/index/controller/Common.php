<?php
/**
 * @Author: Marte
 * @Date:   2018-03-31 19:48:44
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-07 16:48:11
 */
namespace app\index\controller;

use app\data\model\DBarticle;
use think\Controller;
use think\Session;
use app\data\model\DBsection;

class Common extends Controller{
    //所有继承该类的新类都将运行该方法
    public function _initialize(){
        $this->loginMes();
        $this->comData();
    }
    //返回登录信息
    public function loginMes(){

        if(null!=Session::get('check','member') && Session::get('check','member')!=$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']){
             //删除内存中的内容
             session_unset();
             //重置session_id
             session_regenerate_id();
             return '非法用户';
        }
        if(Session::get('mname','member') && Session::get('mid','member')){
            $assign=[
               'uname'=>Session::get('mname','member'),
               'uid'=>Session::get('mid','member')
            ];
        }else{
            $assign=[
                'uid'=>'',
                'uname'=>''
            ];
        }

        $this->assign($assign);
    }

    public function comData(){

         $article=DBarticle::all(function($query){
             $query->order('id desc')->field('id,title')->limit(3);
         })->toArray();

         $section=DBsection::all()->toArray();

         $assign=[
            'newArticle'=>$article,
            'section'=>$section
         ];

         $this->assign($assign);
    }
}