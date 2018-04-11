<?php
namespace app\index\controller;


use think\Cache;
use think\Request;
use Page\Page;
use think\Controller;
use app\data\model\DBarticle;



class Index  extends Common
{
    public function index(){
        $request=Request::instance();
        $page=$request->param('page','');
        $index='alist'.$page;
        //缓存判断
        if(Cache::has($index)){

            $assign=Cache::get($index);


        }else{

            $count=DBarticle::count();
             $page=new Page($count,2,5);
             $article=DBarticle::all(function($query)use($page){
                $query->order('id desc')->limit($page->limit());
            })->toArray();

            foreach($article as &$v){
              $one=DBarticle::get($v['id']);
              $attr=$one->attr->toArray();
              $v['attr']=$attr;
            }
            $assign=[
               'article'=>$article,
               'page'=>$page,
               'current'=>1,
            ];
             //将数据写入缓存,并保存
             Cache::set($index,$assign,60*60*24);

        }

        $this->assign($assign);
        return $this->fetch();
    }

}
