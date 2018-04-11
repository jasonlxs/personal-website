<?php
/**
 * @Author: Marte
 * @Date:   2017-09-18 21:28:27
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-07 14:27:29
 */
namespace app\admin\controller;


use app\data\model\DBcart;
use app\data\model\DBproduct;
use app\data\model\DBmember;


class Procart extends Common
{
    public function index(){

        $allCart=DBcart::all()->toArray();
        if($allCart!=null){
             //以用户为基准查询出购物车有数据的用户
              $cart=new DBcart;
              $member_id=$cart->distinct('member_id')->field('member_id')->select()->toArray();

              $product=DBproduct::all()->toArray();

              $member=DBmember::all()->toArray();


              //将产品ID换成名称
              foreach($allCart as &$ca){
                 foreach ($product as $value) {
                     if($ca['product_id']==$value['id']){
                        $ca['name']=$value['name'];
                     }
                 }
              }

              //将会员ID换成会员名
              foreach ($member_id as &$me) {
                  foreach($member as $mem){
                     if($me['member_id']==$mem['id']){
                         $me['name']=$mem['phone']==null?$mem['email']:$mem['phone'];
                     }
                  }
              }

              //组合产品名和数量
              foreach($member_id as &$v){
                    $str='';
                   foreach($allCart as $va){
                        if($v['member_id']==$va['member_id']){
                             $str.="《".$va['name'].'》*'.$va['count'].',';
                        }
                   }
                   $str=rtrim($str,',');
                   $v['product']=$str;
                }


        }else{
          $member_id='';
        }




        $assign=[
          'mem_pro'=>$member_id
        ];
        $this->assign($assign);
        return view();
    }
}
