<?php
/**
 * @Author: Marte
 * @Date:   2017-09-18 21:27:41
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-01 20:57:55
 */
namespace app\admin\controller;


use app\data\model\DBorder;
use app\data\model\DBmember;


class Proorder extends Common
{
    public function index(){
         $member=new DBmember;
         $order=$member::with('order')->select()->toArray();
         $assign=[
           'order'=>$order
         ];
         // show($order);die;
         $this->assign($assign);
         return $this->fetch();
    }
}
