<?php
/**
 * @Author: Marte
 * @Date:   2017-09-11 21:55:34
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-07 11:35:59
 */
namespace app\mobile\controller;

use think\Request;
use think\Session;
use Models\M3Result;
use app\data\model\DBcart;
use app\data\model\DBproduct;

Class Cart{

    //返回购物车数据
    public function index(){

        $request=Request::instance();
        $m3_result=new M3Result;
        $member_id=Session::get('mid','member');
        $cart=new DBcart;
        $cart_pro=$cart::with('product')->where('member_id', $member_id)->select()->toArray();

        $m3_result->status=1;
        $m3_result->message="更新购物车失败";
        $m3_result->cart_pro=$cart_pro;
        return $m3_result->toJson();

    }
    public function syncCart(){
        $request=Request::instance();
        $m3_result=new M3Result;
        //有特殊符号需要进行html实体解码
        $mcart=html_entity_decode($request->param('cart'));
        $mcart=json_decode($mcart,true);

        $member_id=Session::get('mid','member');
        $carts=DBcart::all(['member_id'=>$member_id])->toArray();
        $update=[];
        //原有购物车数据合并
        foreach($mcart as $k=>$v){
            foreach($carts as &$var){
                if($k==$var['product_id']){
                  $var['count']=max($var['count'],$v);
                  unset($mcart[$k]);
                  continue;
                }
            }
        }
        $cart=new DBcart;
        //更新数据
        if(!$cart->saveAll($carts)){
            $m3_result->status=4;
            $m3_result->message="更新购物车失败";
            return $m3_result->toJson();
        }
        //判断是否有新数据

        if($mcart!=null){

          foreach($mcart as $k=>$v){
               $update[]=[
                    'member_id'=>$member_id,
                    'product_id'=>$k,
                    'count'=>$v,
                    'update_at'=>date('Y-m-d H:i:s',time())
                ];
            }

            if($cart->saveAll($update)){
                $m3_result->status=3;
                $m3_result->message="同步成功";
                return $m3_result->toJson();
            }else{
                $m3_result->status=2;
                $m3_result->message="同步失败";
                return $m3_result->toJson();
            }

        }else{
              $m3_result->status=3;
              $m3_result->message="同步成功";
              return $m3_result->toJson();
        }
    }
    //加入购物车  需要判断库存
    public function addCart(){
         $product_id=input('pid');
         $member=input('mid',session('uid'));
         // show($product_id);die;
         $cart=new DBcart;
         if($perCart=DBcart::get(['member_id'=>$member,'product_id'=>$product_id])){
            //更新添加
             $perCart->count+=1;
             $perCart->update_at=date('Y-m-d H:i:s',time());
             $perCart->save();
         }else{
            //新增
             $cart->member_id=$member;
             $cart->product_id=$product_id;
             $cart->count=1;
             $cart->create_at=date('Y-m-d H:i:s',time());
             $cart->update_at=date('Y-m-d H:i:s',time());
             $cart->save();
         }
    }


    //合计购物车数量
    public function total(){
        $member_id=Session::get('mid','member');

        $cart=new DBcart;
        $carts=$cart->where('member_id',$member_id)->select();
        $carts=collection($carts)->toArray();
        $num=0;
        foreach($carts as $v){
           $num=$num+$v['count'];
        }
        return $num;
    }


    //返回付款的购物车项目或生成订单
    public function cart_pay(){
        $request=Request::instance();
        if($id=$request->param('id')){
            $m3_result=new M3Result;
            $id=explode(',',$id);
            $cart=new DBcart;
            $cart_pay=$cart::with('Product')->where('id','in',$id)->select()->toArray();
            $total=0;
            foreach ($cart_pay as $v) {
                $total+=$v['count']*$v['product']['price'];
            }
            //返回数据
            $m3_result->status=1;
            $m3_result->message='购物车统计成功';
            $m3_result->total=$total;
            $m3_result->cart=$cart_pay;
            return $m3_result->toJson();
        }
    }
    //调整购物产品数量
    public function modCart(){
         $request=Request::instance();
         $m3_result=new M3Result;

         $mcart=html_entity_decode($request->param('cart'));
         $mcart=json_decode($mcart,true);
         $member_id=Session::get('mid','member');

         foreach ($mcart as $k=>$v) {
             $id=$k;
             $count=$v;
         }

         //判断库存数据
        $cart=DBcart::get($id)->toArray();
        $product=DBproduct::get($cart['product_id'])->toArray();
        if($count==$cart['count']){
           $m3_result->status=4;
           $m3_result->message='数据一样,无需修改';
           return  $m3_result->toJson();
        }
        if($count<=$product['inventory']){
           $cart=DBcart::get($id);
           $cart->count=$count;
           if($cart->save()){
               $m3_result->status=1;
               $m3_result->message='产品数量调整成功';
               return  $m3_result->toJson();
           }else{
               $m3_result->status=2;
               $m3_result->message='产品数量调整失败';
               return  $m3_result->toJson();
           }
        }else{
           $m3_result->status=3;
           $m3_result->message='所设置数量超过库存';
           return  $m3_result->toJson();
        }
    }
}