<?php
/**
 * @Author: Marte
 * @Date:   2017-12-06 11:54:58
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-06 13:06:19
 */
namespace app\mobile\controller;

use think\Request;
use Models\M3Result;
use app\data\model\DBproduct;

class bklist{

   public function index(){
         $request=Request::instance();
         if(is_numeric($id=$request->param('id'))){

            $product=DBproduct::all(['cate_id'=>$id])->toArray();
            foreach($product as &$v){
                $v['summary']=wx_parse($v['summary']);
            }
            return JSON_encode($product);

         }

   }


}