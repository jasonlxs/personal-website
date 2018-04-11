<?php
/**
 * @Author: Marte
 * @Date:   2018-03-29 14:09:33
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-07 15:39:07
 */
namespace app\index\controller;


use think\Request;
use think\Controller;
use think\Session;
use think\Cache;
use Models\M3Result;
use app\data\model\DBarticle;
use app\data\model\DBcomment;
use app\data\model\DBalike;
use app\data\model\DBclike;

class Detail  extends Common
{

    //文章详细内容
    public function index(){

        $request=Request::instance();
        if($id=$request->param('id')){
            //增加点击量
            DBarticle::where('id',$id)->setInc('click');

            //上一篇
            $pre=DBarticle::where('id','<',$id)->order('id desc')->field('id,title')->find();
            $pre=$pre==null?'':$pre->toArray();

            //下一篇
            $next=DBarticle::where('id','>',$id)->order('id asc')->field('id,title')->find();
            $next=$next==null?'':$next->toArray();

            //使用查询文章数据
            if(Cache::has('article'.$id)){
                $article=Cache::get('article'.$id);
            }else{
                $article=DBarticle::with('DBcategory')->find($id);
                $article=$article==null?'':$article->toArray();
                $article['content']=html_entity_decode($article['content']);
                Cache::set('article'.$id,$article,60*60*24);
            }


            //确定登录用户是否点赞文章
            $uname=Session::get('mname','member');
            $uid=Session::get('mid','member');
            $islike=0;
            if( $uname && $uid ) {
                if(DBalike::get(['aid'=>$id,'uid'=>$uid])) $islike=1;

            }
            //查询评论和用户
            $allcomment=DBcomment::all(function($query)use($id){
                 $query->where('aid',$id)->order('id desc');
            })->toArray();

            $comment=[];
            foreach ($allcomment as &$v) {
                $one=DBcomment::get($v['id']);
                $two=$one->member->toArray();

                $uname=$two['phone']==null?$two['email']:$two['phone'];
                $v['member']=$two['nickname']==null?$uname:$two['nickname'];
                //确定评论是否点赞和确定属于用户的评论
                if( $uname && $uid ) {
                    if( DBclike::get( ['uid'=>$uid,'comment_id'=>$v['id'] ] ) ){
                       $v['islike']=1;
                    }
                    if($v['mid']==$uid){
                       $v['color']='red';
                    }
                }
                //将一级评论独立存放
                if($v['pid']==0){
                    $comment[]=$v;
                }
            }
            //组合评论数据
            foreach ($comment as &$v) {
               $child=getChildMes($allcomment,$v['id'],1);
               $v['child']=$child;
            }

        }else{
            $this->error('非法操作');
        }
       //show($article);die;
        $assign=[
           'article'=>$article,
           'pre'=>$pre,
           'next'=>$next,
           'comment'=>$comment,
           'islike'=>$islike,
           'current'=>1
        ];
        $this->assign($assign);
        /*静态文件
        $datefile=date('Ymd',time());
        $dirname=RUNTIME_PATH."static".DS.$datefile;
        if(!is_dir($dirname)){
            mkdir($dirname);
        }
        ob_start();
        echo $this->fetch();
        $content=ob_get_contents();
        file_put_contents($dirname.DS.uuid().'.html',$content);
        ob_end_clean();
        */

        return $this->fetch();
    }
    //文章点赞 需要两个参数：文章ID和用户ID
    public function islike(){
        $request=Request::instance();
        $m3_result=new M3Result;
        $uid=Session::get('mid','member');

        if($aid=$request->param('id')){
             //文章点赞操作**********************
             //如果已有点赞，就取消点赞
             if($aold=DBalike::get(['aid'=>$aid,'uid'=>$uid])){
                  DBalike::destroy($aold['id']);
                  $m3_result->status=2;
                  $m3_result->str="点赞";
                  $m3_result->message="取消点赞成功";
                  return $m3_result->toJson();
             }
             //没有点赞，就新增点赞
             $like=new DBalike;
             $like->aid=$aid;
             $like->uid=$uid;
             if($like->save()){
                  $m3_result->status=1;
                  $m3_result->str="取消点赞";
                  $m3_result->message="点赞成功";
                  return $m3_result->toJson();
             }else{
                  $m3_result->status=3;
                  $m3_result->message="失败";
                  return $m3_result->toJson();
             }

        }else{
            //评论点赞操作**********************
            $comment_id=$request->param('cid');
            //如果已有点赞，就取消点赞
             if($cold=DBclike::get(['comment_id'=>$comment_id,'uid'=>$uid])){
                  DBclike::destroy($cold['id']);
                  $m3_result->status=2;
                  $m3_result->str="点赞";
                  $m3_result->message="取消点赞成功";
                  return $m3_result->toJson();
             }
             //没有点赞，就新增点赞
             $like=new DBclike;
             $like->comment_id=$comment_id;
             $like->uid=$uid;
             if($like->save()){
                  $m3_result->status=1;
                  $m3_result->str="取消点赞";
                  $m3_result->message="点赞成功";
                  return $m3_result->toJson();
             }else{
                  $m3_result->status=3;
                  $m3_result->message="失败";
                  return $m3_result->toJson();
             }
        }
    }

    //验证是否登录
    public function islogin(){
         if(null!=Session::get('check','member') && Session::get('check','member')!=$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']){
             //删除内存中的内容
             Session::clear();
             //重置session_id
             Session::regenerate();
             return '非法用户';
         }
         if(!Session::get('mname','member') || !Session::get('mid','member'))return '0';
         return '1';
    }

    //保存评论   只要保存评论，就需要detail页面重新请求数据
    public function addComment(){
        $request=Request::instance();
        if($aid=$request->param('id')){
             $m3_result=new M3Result;
             $mid=session('mid');
             $content=$request->param('content');
             $pid=$request->param('pid',0);
             $comment=new DBcomment;
             $comment->content=$content;
             $comment->aid=$aid;
             $comment->mid=$mid;
             $comment->pid=$pid;
             $comment->update_at=date('Y-m-d',time());
             if($comment->save()){
                    $m3_result->status=4;
                    $m3_result->message='评论添加成功';
                    return $m3_result->toJson();
             }else{
                    $m3_result->status=5;
                    $m3_result->message='评论添加失败';
                    return $m3_result->toJson();
             }
        }else{
            return '非法操作';
        }
    }

}
