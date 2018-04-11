<?php
/**
 * @Author: Marte
 * @Date:   2018-04-07 14:32:03
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-07 16:41:16
 */
namespace app\admin\controller;

use app\data\model\DBsection;

use Models\M3Result;
use think\Request;

class Section extends Common{


    //栏目列表
    public function index(){


        $section=DBsection::all()->toArray();

        $assign=[
           'section'=>$section,
        ];
        $this->assign($assign);
        return $this->fetch();
    }

    //栏目添加
    public function insert(){
         $request=Request::instance();
         if($request->post('name')){
            $m3_result=new M3Result;
            $section=new DBsection;
            $section->name=$request->param('name');
            $section->url=$request->param('url');
            if($section->save()){
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

    //栏目修改
    public function edit(){
        $request=Request::instance();
        //保存修改信息
        if($request->post('name')){
            $m3_result=new M3Result;
            $id=$request->param('id');
            $section=DBsection::get($id);
            $section->name=$request->param('name');
            $section->url=$request->param('url');
            if($section->save()){
              $m3_result->status=3;
              $m3_result->message='修改成功';
              return $m3_result->toJson();
            }else{
                $m3_result->status=4;
                $m3_result->message='修改失败';
                return $m3_result->toJson();
            }
        }

        //获取原始信息
        if($id=$request->param('id')){

            $section=DBsection::get($id);
            $assign=[
               'section'=>$section,
            ];
            $this->assign($assign);
            return $this->fetch();
        }

    }
    //栏目删除
    public function del(){
        $request=Request::instance();
        if($id=$request->param('id')){
            $m3_result=new M3Result;
            if(DBsection::destroy($id)){
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


