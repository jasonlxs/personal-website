<?php
/**
 * @Author: Marte
 * @Date:   2018-01-07 21:15:29
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-01 23:12:56
 */
namespace app\mobile\controller;


use app\data\model\DBmember;
use think\Request;


class Bkperson{

    public function index(){
       $request=Request::instance();
       $uname=$request->param('uname');


       if(strpos($uname,'@')){
           $person=DBmember::get(function($query) use ($uname){
              $query->where('email',$uname)->field('nickname,email,phone');
           })->toArray();
       }else{
            $person=DBmember::get(function($query) use ($uname){
              $query->where('phone',$uname)->field('nickname,email,phone');
            })->toArray();
       }

       return json_encode($person);

    }

}