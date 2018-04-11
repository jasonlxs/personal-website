<?php
/**
 * @Author: Marte
 * @Date:   2018-03-29 17:50:18
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-01 20:49:36
 */
namespace  app\admin\controller;

use think\request;
use Models\M3Result;
use app\data\model\DBattr;
use app\data\model\DBblogattr;

class Attr extends Common{

    //属性列表
    public function index(){
        $attr=DBattr::all()->toArray();
        $count=DBattr::count();
        $assign=[
           'attr'=>$attr,
           'count'=>$count
        ];
        $this->assign($assign);
        return $this->fetch();
    }
    //添加属性
    public function insert(){
        $request=request::instance();
        if($name=$request->param('name')){
           $m3_result=new M3Result;
           $attr=new DBattr;
           $attr->name=$name;
           $attr->color=$request->param('color');
           if($attr->save()){
             $m3_result->status=1;
             $m3_result->message='添加成功';
             return $m3_result->toJson();
           }else{
             $m3_result->status=2;
             $m3_result->message='添加失败';
             return $m3_result->toJson();
           }
        }
        return $this->fetch();
    }

    //修改属性
    public function edit(){
        $request=request::instance();


        //保存修改
        if($name=$request->param('name')){
           $m3_result=new M3Result;
           $id=$request->param('id');
           $attr=DBattr::get($id);
           $attr->name=$name;
           $attr->color=$request->param('color');
           if($attr->save()){
             $m3_result->status=3;
             $m3_result->message='修改成功';
             return $m3_result->toJson();
           }else{
             $m3_result->status=4;
             $m3_result->message='修改失败';
             return $m3_result->toJson();
           }
        }

        //提取修改信息
       if($id=$request->param('id')){
         $attr=DBattr::get($id);

         $assign=[
            'attr'=>$attr
            ];
          $this->assign($assign);
          return $this->fetch();
       }

    }

    //删除属性
    public function del(){
       $request=request::instance();
       //echo ($request->param('id'));die;
      if($id=$request->param('id')){
         $m3_result=new M3Result;
         //判断是否被引用
         if(DBarticleattr::all(['aid'=>$id])->toArray()){
             $m3_result->status=7;
             $m3_result->message='属性被文章引用，无法删除';
             return $m3_result->toJson();
         }else{
           if(DBattr::destroy($id)){
             $m3_result->status=5;
             $m3_result->message='删除成功';
             return $m3_result->toJson();
           }else{
             $m3_result->status=6;
             $m3_result->message='删除失败';
             return $m3_result->toJson();
           }
         }
       }
    }
}