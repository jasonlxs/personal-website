<?php
/**
 * @Author: Marte
 * @Date:   2017-12-05 16:36:08
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-07 12:20:07
 */
namespace app\mobile\controller;


use think\Request;
use think\Session;
use think\Db;
use Models\M3Result;
use app\data\model\DBcart;
use app\data\model\DBorder;
use app\data\model\DBorderitem;
use app\data\model\DBaddress;

class Ordermake{


    //生成订单
     public function index(){
              $request=Request::instance();
              if($id=$request->param('id')){
                    $m3_result=new M3Result;
                    $uid=Session::get('mid','member')==null?1:Session::get('mid','member');
                    //判断是否有收货地址
                    if(!DBaddress::get(['member_id'=>$uid,'isdefault'=>1])){
                       $m3_result->status=5;
                       $m3_result->message='请增加并设置收货地址';
                       return $m3_result->toJson();
                    }

                    //判断库存
                    $id=explode(',',$id);
                    $cart=new DBcart;
                    $cart_pay=$cart::with('Product')->where('id','in',$id)->select()->toArray();

                    $exceed='';
                    foreach ($cart_pay as $v) {
                        if($v['count']>$v['product']['inventory']){
                            $exceed.=$v['product']['name'].',';
                        }
                    }
                    if($exceed!=null){
                          $m3_result->status=6;
                          $m3_result->message=$exceed.'超过库存,请返回修改';
                          return $m3_result->toJson();
                    }else{
                      //计算订单总价并构成数据数组

                      $reduce=[];
                      $total=0;
                      $item=[];
                      foreach ($cart_pay as $v) {
                        //减库存
                        $reduce[]=[
                          'id'=>$v['product']['id'],
                          'inventory'=>$v['product']['inventory']-$v['count']
                        ];
                        //计算订单总价
                        $total+=$v['count']*$v['product']['price'];

                      }
                    }

                    //生成订单编号 年月日 时间戳 微秒 随机2位数
                    $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
                    $orderSn =  $yCode[intval(date('Y')) - 2011] .
                                strtoupper(dechex(date('m'))) .
                                date('d') .
                                substr(time(), -5) .
                                substr(microtime(), 2, 5) .
                                sprintf('%02d', rand(0, 99));

                    //获取默认收货地址ID
                    $addr=DBaddress::get(['member_id'=>$uid,'isdefault'=>1])->toArray();


                    //组合订单数据
                    $orderMes=[
                      'member_id'=>$uid,
                      'order_no'=>$orderSn,
                      'total_price'=>$total,
                      'update_at'=>date('Y-m-d H:i:s',time()),
                      'addr_id'=>$addr['id']
                    ];

                    //生成订单  开启事务
                    Db::startTrans();
                    try{
                          //减少库存
                          foreach($reduce as $re){
                            Db::execute("update lxs_product set inventory=:inventory where id=:id",$re);
                          }
                          //删除购物车数据
                          Db::table('lxs_cart')->delete($id);
                          //生成订单
                          Db::name('order')->insert($orderMes);
                          $oid = Db::name('order')->getLastInsID();
                          //生成订单产品数据

                           //组合订单产品数据信息
                           foreach($cart_pay as $ca){
                              $item[]=[
                                  'order_id'=>$oid,
                                  'product_id'=>$v['product']['id'],
                                  'count'=>$v['count']
                              ];
                           }
                          Db::name('orderitem')->insertAll($item);
                          // 提交事务
                          Db::commit();

                          //成功之后的操作
                          $m3_result->status=1;
                          $m3_result->message='订单生成成功';
                          $m3_result->oid=$oid;
                          return $m3_result->toJson();
                      }catch (\Exception $e) {
                          // 回滚事务
                          Db::rollback();
                          //失败之后的操作
                          $m3_result->status=2;
                          //$m3_result->message=$e->getMessage();
                          $m3_result->message='订单生成失败';
                          return $m3_result->toJson();
                      }
                  }

                    /*删除购物车数据
                    if(!DBCart::destroy($id)){
                       $m3_result->status=4;
                       $m3_result->message='删除购物车数据失败请重试！';
                       return $m3_result->toJson();
                    }
                    //生成订单编号 年月日 时间戳 微秒 随机2位数
                    $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
                    $orderSn =  $yCode[intval(date('Y')) - 2011] .
                                strtoupper(dechex(date('m'))) .
                                date('d') .
                                substr(time(), -5) .
                                substr(microtime(), 2, 5) .
                                sprintf('%02d', rand(0, 99));

                    //获取默认收货地址ID
                    $addr=DBaddress::get(['member_id'=>$uid,'isdefault'=>1])->toArray();

                    //订单数据
                    $order_no=$orderSn;
                    $order=new DBorder;
                    $order->member_id=$uid;
                    $order->order_no=$order_no;
                    $order->total_price=$total;
                    $order->update_at=date('Y-m-d H:i:s',time());
                    $order->addr_id=$addr['id'];
                    $item=[];
                    if($order->save()){
                      //生成订单
                       $oid=$order->id;
                       $status=DBorder::get($oid)->status;



                       //将订单中的产品信息保存
                       foreach($cart_pay as $v){
                          $item[]=[
                                'order_id'=>$oid,
                                'product_id'=>$v['product']['id'],
                                'count'=>$v['count']
                                ];
                       }
                       $orderitem=new DBorderitem;
                       if($orderitem->saveAll($item)){
                            //返回数据
                            $m3_result->status=1;
                            $m3_result->message='订单生成成功';
                            $m3_result->oid=$oid;
                            return $m3_result->toJson();
                       }else{
                          $m3_result->status=3;
                          $m3_result->message='订单快照生成失败';
                          return $m3_result->toJson();
                       }
                    }else{
                         $m3_result->status=2;
                         $m3_result->message='订单生成失败';
                         return $m3_result->toJson();
                    }
                  */

            }
    //订单列表  需要判断订单是否失效
     public function Orderlist(){

          $m3_result=new M3Result;
          $member_id=Session::get('mid','member');

          $orderlist=DBorder::all(function($query)use($member_id){
              $query->where('member_id',$member_id)->field('id,order_no,total_price,status,create_at');
          })->toArray();
          if($orderlist){
             foreach($orderlist as &$v){
                 if($v['status']==0){
                     $v['status']='未付款';
                 }
                 if($v['status']==1){
                    $v['status']='已付款';
                 }
                  if($v['status']==2){
                    $v['status']='已失效';
                 }
             }
          }
          $m3_result->status=1;
          $m3_result->message='获取订单数据成功';
          $m3_result->orderlist=$orderlist;
          return $m3_result->toJson();
     }
     //订单详情  判断订单是否失效
     public function orderMes(){
         $request=Request::instance();
         $m3_result=new M3Result;
         $id=$request->param('id');
         $order=DBorder::get($id)->toArray();
         $orderitem=new DBorderitem;
         $orderitems=$orderitem::with('Product')->where('order_id',$order['id'])->field('product_id,count')->select()->toArray();

         //调整路径斜杠
         foreach($orderitems as &$v){
             $v['product']['preview']=str_replace('\\', "/",$v['product']['preview']);
         }
         //判断订单状态
          if($order['status']==0)$order['status']='未付款';
          if($order['status']==1)$order['status']='已付款';
          if($order['status']==2)$order['status']='已失效';

         $m3_result->status=1;
         $m3_result->message='查看订单详情';
         $m3_result->order_no=$order['order_no'];
         $m3_result->orderSta=$order['status'];
         $m3_result->length=count($orderitems);
         $m3_result->total=$order['total_price'];
         $m3_result->cart=$orderitems;
         return $m3_result->toJson();
     }

}



