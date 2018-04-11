<?php
/**
 * @Author: Marte
 * @Date:   2018-03-17 09:10:51
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-01 20:50:04
 */
namespace app\admin\controller;
use think\Request;
use think\Paginator;
use Models\M3Result;
use Page\Page;
use app\data\model\DBarticle;
use app\data\model\DBcomment;

class Comment extends Common{

    //评论列表
    public function index(){
      //查询文章数据
      $article=DBarticle::all(function($quest){
          $quest->field('id,title');
      })->toArray();

      $assign=[
         'article'=>$article
      ];
      $this->assign($assign);
      return $this->fetch();
    }

    //评论列表iframe
    public function lists(){

        $request=Request::instance();

        //统计评论总数
        $count=DBcomment::count();
        $pcount=DBcomment::where('pid',0)->count();
        $page=new Page($pcount,1,5);



        if($id=$request->param('id')){
            $pcount=DBcomment::where(['aid'=>$id,'pid'=>0])->count();
            $page=new Page($pcount,1,5);
            $comment=DBcomment::all(function($query)use($id,$page){
                 $query->where(['aid'=>$id,'pid'=>0])->limit($page->limit());
            })->toArray();
            $all_comment=DBcomment::all(['aid'=>$id])->toArray();

        }else{
            $pcount=DBcomment::where('pid',0)->count();
            $page=new Page($pcount,1,5);

            $comment=DBcomment::all(function($query)use($page){
                 $query->where(['pid'=>0])->limit($page->limit());
            })->toArray();
            $all_comment=DBcomment::all()->toArray();
        }

        foreach($comment as &$v){
            $arr=getChildMes($all_comment,$v['id'],1);
            $v['child']=$arr;
        }

        $title=$request->param('id')!=null?$request->param('id'):'显示所有评论';

        $assign=[
         'comment'=>$comment,
         'title'=>$title,
         'page'=>$page,
         'count'=>$count
        ];
        $this->assign($assign);
        return $this->fetch();
    }

    //评论编辑
    public function edit(){
        $request=Request::instance();

        //评论保存
        if($content=$request->post('content')){
           $m3_result=new M3Result;
           $id=$request->post('cid');
           $com=DBcomment::get($id);
           $com->content=$content;
           if($com->save()){
               $m3_result->status=3;
               $m3_result->message='保存成功';
               return $m3_result->toJson();
           }else{
               $m3_result->status=4;
               $m3_result->message='保存失败';
               return $m3_result->toJson();
           }
        }

        //评论内容显示
        if($id=$request->param('id')){
           $comment=DBcomment::get($id);
        }
        $article=DBarticle::get($comment['aid']);
        $comment['title']=$article['title'];
        $assign=[
          'comment'=>$comment
        ];
        $this->assign($assign);
        return $this->fetch();
    }


    //评论删除
    public function del(){
       $request=Request::instance();

       if($id=$request->param('id')){
         $m3_result=new M3Result;
         $all_comment=DBcomment::all()->toArray();
         $child=getChildMes($all_comment,$id);
         if($child){
            $m3_result->status=6;
            $m3_result->message='该评论存在子评论，请先删除子分类!';
            return $m3_result->toJson();
         }else{
            if(DBcomment::destroy($id)){
               $m3_result->status=5;
               $m3_result->message='删除成功';
               return $m3_result->toJson();
            }else{
                $m3_result->status=7;
                $m3_result->message='删除失败';
                return $m3_result->toJson();
            }
         }
       }
    }

}