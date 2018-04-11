<?php
/**
 * @Author: Marte
 * @Date:   2018-03-13 12:13:14
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-07 12:48:53
 */
namespace  app\admin\controller;
use app\data\model\DBcategory;
use app\data\model\DBarticle;
use app\data\model\DBattr;
use app\data\model\DBblogattr;
use Page\Page;
use think\request;
use Models\M3Result;


class Blog extends Common{
    //文章列表
    public function index(){
        $count=DBarticle::count();
        $page=new Page($count,2,5);
        $article=DBarticle::all(function($query)use($page){
             $query->limit($page->limit());
        })->toArray();

        foreach ($article as &$v) {
           $one=DBarticle::get($v['id']);
           $cate=$one->DBcategory->toArray();
           $v['cname']=$cate['cname'];
        }

        $assign=[
           'article'=>$article,
           'page'=>$page,
           'count'=>$count
        ];
        $this->assign($assign);
        return $this->fetch();
    }
    //添加文章
    public function insert(){
        $request=request::instance();
        //保存添加
        if($request->param('title')){
           $m3_result=new M3Result;
           $article=new DBarticle;
           $article->title=$request->param('title');
           $article->content=$request->post('content');
           $article->author=$request->post('author');
           $article->thumb=$request->post('thumb');
           $article->intro=$request->post('abstract');
           $article->cid=$request->param('cid');
           $article->update_at=date("Y-m-d H:i:s",time());
           if($article->save()){
               $id=$article->id;
               //添加文章属性
               $attr=$_POST['attr'];
               $blogattr=new DBblogattr;
               $list=[];
               foreach ($attr as $v) {
                  $list[]=['bid'=>$id,'aid'=>$v];
               }
               if($blogattr->saveAll($list)){
                  $m3_result->status=1;
                  $m3_result->message='添加成功';
                  return $m3_result->toJson();
               }else{
                  $m3_result->status=2;
                  $m3_result->message='文章属性添加失败';
                  return $m3_result->toJson();
               }

           }else{
               $m3_result->status=2;
               $m3_result->message='文章添加失败';
               return $m3_result->toJson();
           }
        }
        //文章添加页面显示
        $allCate=DBcategory::all()->toArray();
        $cate=getChildMes($allCate);
        $attr=DBattr::all()->toArray();
        $assign=[
          'cate'=>$cate,
          'attr'=>$attr
        ];
        $this->assign($assign);
        return $this->fetch();
    }
    //文章编辑
    public function edit(){
        $request=request::instance();

        //保存编辑
        if($request->param('title')){
           $m3_result=new M3Result;
           $id=$request->param('id');
           $article=DBarticle::get($id);

           $article->title=$request->param('title');
           $article->content=$request->param('content');
           $article->author=$request->param('author');
           if($request->post('thumb')!=null)$article->thumb=$request->param('thumb');
           $article->intro=$request->param('abstract');
           $article->cid=$request->param('cid');
           $article->update_at=date("Y-m-d H:i:s",time());

           if($article->save()){
               //删除旧属性
               DBblogattr::destroy(['bid'=>$id]);
               //添加新属性
               $attr=$_POST['attr'];
               $blogattr=new DBblogattr;
               $list=[];
               foreach ($attr as $v) {
                  $list[]=['bid'=>$id,'aid'=>$v];
               }
               if($blogattr->saveAll($list)){
                 $m3_result->status=3;
                 $m3_result->message='修改成功';
                 return $m3_result->toJson();
               }else{
                 $m3_result->status=4;
                 $m3_result->message='修改文章属性失败';
                 return $m3_result->toJson();
               }
           }else{
               $m3_result->status=4;
               $m3_result->message='修改文章失败';
               return $m3_result->toJson();
           }
        }

        //需要修改的文章信息显示
        if($id=$request->param('id')){

            $article=DBarticle::get($id);
            $allCate=DBcategory::all()->toArray();
            $cate=getChildMes($allCate);
            $attr=DBattr::all()->toArray();
            $checkAttr=DBblogattr::all(['bid'=>$id])->toArray();

            //添加已选属性
            foreach ($attr as &$v) {
               foreach ($checkAttr as $ch) {
                   if($v['id']==$ch['aid']){
                      $v['check']=1;
                      continue;
                   }
               }
            }

            $assign=[
              'article'=>$article,
              'cate'=>$cate,
              'attr'=>$attr
            ];
            $this->assign($assign);
            return $this->fetch();
        }

    }
    //删除文章
    public function del(){
        $request=request::instance();
        $m3_result=new M3Result;

        if(DBarticle::destroy($request->param('id'))){
               $m3_result->status=5;
               $m3_result->message='删除成功';
               return $m3_result->toJson();
        }else{
               $m3_result->status=6;
               $m3_result->message='删除失败';
               return $m3_result->toJson();
        }
    }
    //是否发布
    public function release(){
        $request=request::instance();
        $m3_result=new M3Result;

        $article=DBarticle::get($request->param('id'));
        if($article['publish']==0){
            $article->publish=1;
            if($article->save()){
               $m3_result->status=7;
               $m3_result->str='已发布';
               $m3_result->xcode='&#xe6ba;';
               $m3_result->message='发布成功';
               $m3_result->title='取消发布';
               return $m3_result->toJson();
            }
        }else{
            $article->publish=0;
            if($article->save()){
               $m3_result->status=8;
               $m3_result->xcode='&#xe603;';
               $m3_result->str='未发布';
               $m3_result->message='取消发布';
               $m3_result->title='发布';
               return $m3_result->toJson();
            }
        }
    }

    //是否置顶
    public function istop(){
        $request=request::instance();
        $m3_result=new M3Result;

        $article=DBarticle::get($request->param('id'));
        if($article['istop']==0){
            $article->istop=1;
            if($article->save()){
               $m3_result->status=9;
               $m3_result->str='已发布';
               $m3_result->xcode='&#xe6de;';
               $m3_result->message='置顶成功';
               $m3_result->title='取消置顶';
               return $m3_result->toJson();
            }
        }else{
            $article->istop=0;
            if($article->save()){
               $m3_result->status=10;
               $m3_result->xcode='&#xe6dc;';
               $m3_result->str='未发布';
               $m3_result->message='取消置顶';
               $m3_result->title='置顶';
               return $m3_result->toJson();
            }
        }
    }
}