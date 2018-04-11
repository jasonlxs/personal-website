<?php
/**
 * @Author: Marte
 * @Date:   2017-12-06 16:19:18
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-06 13:08:53
 */
namespace app\mobile\controller;

use think\Request;
use Models\M3Result;
use app\data\model\DBproduct;
use app\data\model\DBpimage;

class Bkdetail{

   public function index(){
       $request=Request::instance();
       if($id=$request->param('id')){
             $m3_result=new M3Result;


             $product=new DBproduct;
             $product=$product->where('id',$id)->find()->toArray();

             $product['summary']=wx_parse($product['summary']);


             $pimage=DBpimage::all(['product_id'=>$id]);
             $pimage=$pimage->toArray();
             $pimages=[];
             foreach($pimage as $v){

                    $v['image_path']='http://'.$_SERVER['HTTP_HOST'].'/static/uploads/'.$v['image_path'];
                    $pimages[]=$v['image_path'];
             }

             $m3_result->status=1;
             $m3_result->message='返回数据成功';
             $m3_result->product=$product;
             $m3_result->pimages=$pimages;
             return $m3_result->toJson();
            }
        }

}