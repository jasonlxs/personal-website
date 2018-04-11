<?php
/**
 * @Author: Marte
 * @Date:   2018-03-10 14:55:06
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-01 20:58:19
 */
namespace app\admin\controller;
use app\data\model\DBrule;
use think\request;
use models\M3Result;

class Rules extends Common{

    //权限列表
    public function index(){

        $rules=DBrule::all(['pid'=>0])->toArray();
        $all=DBrule::all()->toArray();
        foreach ($rules as &$v) {
          $child=getChildMes($all,$v['id']);
          $v['child']=$child;
        }

        $assign=[
          'rules'=>$rules,
        ];
        $this->assign($assign);
        return $this->fetch();
    }

    //添加权限
    public function insert(){
        //添加权限的逻辑
        $request=Request::instance();
        if($request->post('adminName')){
           $rule=new DBrule;
           $m3_result=new M3Result;
           $rule->title=$request->post('adminName');
           $rule->name=$request->post('password');
           $rule->status=$request->post('sex');
           $rule->condition=$request->post('condition');
           $rule->pid=$request->post('pid');
           if($rule->save()){
               $m3_result->status=1;
               $m3_result->message='添加成功!';
               return  $m3_result->toJson();
           }else{
               $m3_result->status=2;
               $m3_result->message='添加失败,请重试!';
               return  $m3_result->toJson();
           }
        }
        //空白的权限添加页面
        $assign=[
          'pid'=>$request->param('id'),
        ];
        $this->assign($assign);
        return $this->fetch();
    }
    //编辑权限
    public function edit(){
         $request=Request::instance();
         $m3_result=new M3Result;

         //修改权限
         if($request->param('adminName')){
               $id=$request->post('id');
               $rule=DBrule::get($id);
               $m3_result=new M3Result;
               $rule->title=$request->post('adminName');
               $rule->name=$request->post('password');
               $rule->status=$request->post('sex');
               $rule->condition=$request->post('condition');
               if($rule->save()){
                   $m3_result->status=3;
                   $m3_result->message='修改成功!';
                   return  $m3_result->toJson();
               }else{
                   $m3_result->status=4;
                   $m3_result->message='修改失败,请重试!';
                   return  $m3_result->toJson();
               }
         }
         //权限信息展示页面
         if($id=$request->param('id')){
             if(!$rule=DBrule::get($id))$this->error('数据出错，请联系管理员',"admin/rules/index");
         }else{
            $this->error('非法操作',"admin/rules/index");
         }
         $assign=[
          'rule'=>$rule,
        ];
        $this->assign($assign);
        return $this->fetch();
    }

    //删除权限
    public function del(){
        $request=Request::instance();
        $m3_result=new M3Result;
        if($id=$request->param('id')){
           if(DBrule::destroy($id)){
               $m3_result->status=5;
               $m3_result->message='删除成功!';
               return  $m3_result->toJson();
           }else{
               $m3_result->status=6;
               $m3_result->message='删除失败!';
               return  $m3_result->toJson();
           }
        }else{
           $this->error('非法操作');
        }
    }
}