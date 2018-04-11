<?php
/**
 * @Author: Marte
 * @Date:   2018-03-13 12:32:04
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-01 20:49:47
 */
namespace app\admin\controller;
use think\request;
use Models\M3Result;
use app\data\model\DBcategory;


class Category extends Common{

    //分类列表
    public function index(){

        $cate=DBcategory::all()->toArray();

        $assign=[
         'cate'=>$cate
        ];
        $this->assign($assign);
        return $this->fetch();
    }


    //分类添加
    public function insert(){
        $request=request::instance();

        if($request->post('cname')){
           $m3_result=new M3Result;
           $cate=new DBcategory;
           $cate->cname=$request->post('cname');
           $cate->description=$request->post('description');
           $cate->pid=$request->post('pid');
           $cate->update_at=date("Y-m-d H:i:s",time());
           if($cate->save()){
               $m3_result->status=1;
               $m3_result->message='添加成功!';
               return  $m3_result->toJson();
           }else{
               $m3_result->status=2;
               $m3_result->message='添加失败!';
               return  $m3_result->toJson();
           }
        }

        //添加页面展示 附带pid参数确定添加类型
        $assign=[
           'pid'=>$request->param('id')
        ];
        $this->assign($assign);
        return $this->fetch();
    }
    //修改分类
    public function edit(){
        $request=request::instance();
        //保存修改
        if($request->post('cname')){
           $m3_result=new M3Result;
           $cate=DBcategory::get($request->post('id'));
           $cate->cname=$request->post('cname');
           $cate->description=$request->post('description');
           $cate->update_at=date("Y-m-d H:i:s",time());
           if($cate->save()){
               $m3_result->status=3;
               $m3_result->message='修改成功!';
               return  $m3_result->toJson();
           }else{
               $m3_result->status=4;
               $m3_result->message='修改失败!';
               return  $m3_result->toJson();
           }
        }

        //原始数据
        $cate=DBcategory::get($request->param('id'));
        $assign=[
           'cate'=>$cate,
        ];
        $this->assign($assign);
        return $this->fetch();
    }
    //删除分类
    public function del(){
           $request=request::instance();
          if($id=$request->param('id')){
            $m3_result=new M3Result;
            $allCate=DBcategory::all()->toArray();
            $child=getChildMes($allCate,$id);
            if($child){
               $m3_result->status=6;
               $m3_result->message='该分类存在子分类，请先删除其父级分类!';
               return  $m3_result->toJson();
            }else{
               if(DBcategory::destroy($id)){
                 $m3_result->status=5;
                 $m3_result->message='删除成功!';
                 return  $m3_result->toJson();
               }else{
                 $m3_result->status=5;
                 $m3_result->message='删除成功!';
                 return  $m3_result->toJson();
               }
            }

          }
    }
    //分类的操作菜单
    public function menu(){
         $request=request::instance();
         $assign=[
           'id'=>$request->param('id',''),
           'cname'=>$request->param('cname','')
        ];
         $this->assign($assign);
         return $this->fetch();
    }
}