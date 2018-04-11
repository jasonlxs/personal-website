<?php
/**
 * @Author: Marte
 * @Date:   2017-09-18 21:24:27
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-07 12:48:02
 */
namespace app\admin\controller;

use app\data\model\DBpcate;
use think\Request;
use Models\M3Result;

class Proclass extends Common
{
    //产品分类列表
    public function index(){

       $pcate=DBPcate::all()->toArray();
       $assign=[
         'pcate'=>$pcate
       ];
       $this->assign($assign);
       return $this->fetch();
    }

    //产品分类菜单
    public function menu(){
       $request=Request::instance();

       $assign=[
         'cname'=>$request->param('cname',''),
         'id'=>$request->param('id',''),
       ];
       $this->assign($assign);
       return $this->fetch();
    }

    //新增产品分类
    public function insert(){
        $request=Request::instance();

        //保存新增的信息
        if($request->param('cname')!=null){

           $m3_result=new M3Result;
           $pcate=new DBpcate;
           $pcate->cname=$request->param('cname');
           $pcate->cate_no=$request->param('cate_no');
           $pcate->pid=$request->param('pid');
           $pcate->preview=$request->param('preview');
           $pcate->update_at=date("Y-m-d H:i:s",time());

           if($pcate->save()){
               $m3_result->status=1;
               $m3_result->message='保存成功';
               return $m3_result->toJson();
           }else{
               $m3_result->status=2;
               $m3_result->message='保存失败';
               return $m3_result->toJson();
           }
        }


        $assign=[
            'pid'=>$request->param('id')
        ];
        $this->assign($assign);
        return $this->fetch();
    }



   //修改产品分类
   public function edit(){
        $request=Request::instance();

        //保存修改信息
        if($request->param('cname')!=null){

           $m3_result=new M3Result;
           $pcate=DBpcate::get($request->param('id'));
           $pcate->cname=$request->param('cname');
           $pcate->cate_no=$request->param('cate_no');
           $pcate->pid=$request->param('pid');
           if($request->param('preview')!=null)$pcate->preview=$request->param('preview');
           $pcate->update_at=date("Y-m-d H:i:s",time());

           if($pcate->save()){
               $m3_result->status=3;
               $m3_result->message='修改成功';
               return $m3_result->toJson();
           }else{
               $m3_result->status=4;
               $m3_result->message='修改失败';
               return $m3_result->toJson();
           }
        }


        //界面数据显示
        if($cid=$request->param('id')){
           $pcate=DBpcate::get($cid);
        }else{
            return '非法操作';
        }
        $assign=[
           'pcate'=>$pcate
        ];
        $this->assign($assign);
        return $this->fetch();
   }


   //删除产品分类
   public function del(){
       $request=request::instance();
          if($id=$request->param('id')){
            $m3_result=new M3Result;
            $allCate=DBpcate::all()->toArray();
            $child=getChildMes($allCate,$id);
            if($child){
               $m3_result->status=6;
               $m3_result->message='该分类存在子分类，请先删除其父级分类!';
               return  $m3_result->toJson();
            }else{
               if(DBpcate::destroy($id)){
                 $m3_result->status=5;
                 $m3_result->message='删除成功!';
                 return  $m3_result->toJson();
               }else{
                 $m3_result->status=7;
                 $m3_result->message='删除失败!';
                 return  $m3_result->toJson();
               }
            }
          }
   }

}
