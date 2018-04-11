<?php
/**
 * @Author: Marte
 * @Date:   2017-12-05 19:07:08
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-06 12:21:46
 */
namespace app\mobile\controller;

use Models\M3Result;
use app\data\model\DBpcate;
use think\Hook;

class Bkcate{

    public function index(){

         Hook::add('cs','app\mobile\behaviors\testBehavior');
         hook::listen('cs');



        $m3_result=new M3Result;

        //主类别查找
        $pcate=DBpcate::all(['pid'=>0])->toArray();

        $cate=[];
        foreach($pcate as $v){
          $cate[]=$v['cname'];
        }
        //缓存主类别列表
        session('pcate',$cate);


        //获取第一类别的子类别
        $first=array_shift($pcate);


        //获取非主分类
        $ncate=DBpcate::all(function($query){
           $query->where('pid','<>',0);
        })->toArray();

        $child=getChildMes($ncate,$first['id'],1);

        //返回数据
        $m3_result->status=1;
        $m3_result->message='分类数据查询成功';
        $m3_result->cate=$cate;
        $m3_result->child=$child;
        return $m3_result->toJson();
    }


    //根据主分类查询子分类
    public function change(){
         if(is_numeric($id=input('id'))){
            $pcate=session('pcate');
            $str='';
            foreach($pcate as $k=>$v){
               if($k==$id){
                 $str=$v;
               }
            }
            $first=DBpcate::get(['cname'=>$str]);
            $ncate=DBpcate::all(function($query){
                $query->where('pid','<>',0);
            });
            $ncate=$ncate->toArray();
            $child=getChildMes($ncate,$first['id'],1);
            return json_encode($child);
         }

    }

    public function cs(){
        show(input('id'));
    }
}