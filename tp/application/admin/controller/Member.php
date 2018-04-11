<?php
/**
 * @Author: Marte
 * @Date:   2018-03-21 21:07:19
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-02 12:08:29
 */
namespace  app\admin\controller;

use think\Request;
use Models\M3Result;
use app\data\model\DBmember;


class Member extends Common{

    //会员列表
    public function index(){

       $member=DBmember::all(function($query){
            $query->field('id,nickname,phone,email,is_active,create_at,update_at');
       })->toArray();
       $assign=[
          'member'=>$member
       ];
       $this->assign($assign);
       return $this->fetch();
    }

    //添加会员
    public function insert(){

       return $this->fetch();
    }


    //编辑会员
    public function edit(){
       $request=request::instance();

       //保存修改信息
       if($nickname=$request->param('username')){
           $m3_result=new M3Result;

           $member=DBmember::get($request->param('mid'));
           $member->nickname=$nickname;
           $member->phone=$request->param('mobile');
           $member->email=$request->param('email');
           if($member->save()){
              $m3_result->status=3;
              $m3_result->message='修改成功';
              return $m3_result->toJson();
           }else{
              $m3_result->status=4;
              $m3_result->message='修改失败';
              return $m3_result->toJson();
           }
       }

       //原信息现实和
       if($id=$request->param('id')){
           $member=DBmember::get($id);
       }
       $assign=[
          'member'=>$member
       ];
       $this->assign($assign);
       return $this->fetch();
    }


    //修改会员密码
    public function editpwd(){
       $request=request::instance();

       if($upwd=$request->param('teacher-new-password')){
           $m3_result=new M3Result;
           $id=$request->param('mid');
           $member=DBmember::get($id);
           $member->upwd=md5('lxs'.$upwd);
           if($member->save()){
                $m3_result->status=5;
                $m3_result->message='修改成功';
                return $m3_result->toJson();
           }else{
                $m3_result->status=6;
                $m3_result->message='修改失败';
                return $m3_result->toJson();
           }
       }

       $assign=[
          'id'=>$request->param('id')
       ];
       $this->assign($assign);
       return $this->fetch();
    }

    //激活停用会员
    public function isActive(){
       $request=request::instance();

       if($id=$request->param('id')){
           $m3_result=new M3Result;
           $member=DBmember::get($id);
           $member->is_active=$member['is_active']==1?0:1;
           if($member->save()){
              $m3_result->status=7;
              $m3_result->message=$member->is_active==1?'激活成功':'停用成功';
              $m3_result->str=$member->is_active==1?'已激活':'未激活';
              $m3_result->code=$member->is_active==1?'&#xe631;':'&#xe6e1;';
              return $m3_result->toJson();
           }else{
              $m3_result->status=8;
              $m3_result->message='修改失败';
              return $m3_result->toJson();
           }
       }
    }
}

