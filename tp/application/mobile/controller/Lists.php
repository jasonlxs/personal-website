<?php
namespace app\mobile\controller;


use app\data\model\DBarticle;
/**
 * 博客列表
 */
class Lists
{
    public function index()
    {

         $article=new DBarticle;
         $alist=$article::all(function($query){
				$query->field('id,title,create_at,thumb')->limit(4)->order('id', 'asc');
         });
         foreach ($alist as &$v) {
         	$v['thumb']=str_replace('\\', "/", $v['thumb']);
         	$v['thumb']='http://'.$_SERVER['HTTP_HOST'].'/static/uploads/'.$v['thumb'];
         }
         return json_encode($alist);
    }
}
