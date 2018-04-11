<?php
/**
 * @Author: Marte
 * @Date:   2018-04-02 12:10:40
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-02 19:46:53
 */
namespace app\index\controller;

use Page\Page;
use think\Request;
use app\data\model\DBarticle;

class Search extends Common{

    public function index(){
        $request=Request::instance();

        if($search=$request->param('search')){
            $count=DBarticle::where('title','like',"%{$search}%")->count();
            $page=new Page($count,2,5);
            $searchArticle=DBarticle::all(function($query)use($search,$page){
                $query->where('title','like',"%{$search}%")->field('id,title,thumb,author,create_at')->limit($page->limit());
            })->toArray();

        }else{
            die('非法操作');
        }

        $assign=[
            'searchArticle'=>$searchArticle,
            'page'=>$page,
            'search'=>$search
        ];
        $this->assign($assign);
        return $this->fetch();
    }
}