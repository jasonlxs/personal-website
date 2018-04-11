<?php
/**
 * @Author: Marte
 * @Date:   2017-09-18 21:24:49
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-07 13:29:32
 */
namespace app\admin\controller;

use think\Request;
use Models\M3Result;
use Page\Page;
use app\data\model\DBproduct;
use app\data\model\DBpcate;
use app\data\model\DBpimage;
use app\data\model\DBpcontent;

class Prolist extends Common
{

    /*产品列表*/
    public function index(){
        $request=request::instance();
        $pcate=DBPcate::all()->toArray();


        if($id=$request->param('id')){
          $count=DBProduct::count();
          $page=new Page($count,1,5);
          $product=DBProduct::all(function($query)use($page,$id){
              $query->where('cate_id',$id)->limit($page->limit());
          })->toArray();
        }else{

          $count=DBProduct::count();
          $page=new Page($count,1,5);
          $product=DBProduct::all(function($query)use($page){
             $query->limit($page->limit());
          })->toArray();

        }

        $assign=[
          'product'=>$product,
          'pcate'=>$pcate,
          'select'=>$request->param('cname','所有'),
          'page'=>$page
        ];
        $this->assign($assign);
        return $this->fetch();
    }



    //新增产品
    public function insert(){
        $request=request::instance();
        if($name=$request->param('name')){

           $m3_result=new M3Result;
           $product=new DBProduct;
           $product->name=$name;
           $product->cate_id=$request->param('cate_id');
           $product->summary=$request->param('summary');
           $product->price=$request->param('price');
           $product->inventory=$request->param('inventory');
           $product->preview=$request->param('preview');
           $product->update_at=date("Y-m-d H:i:s",time());


           if($product->save()){
               $product_id=$product->id;
               //详细内容保存
               $pcontent=new DBpcontent;
               $pcontent->content=$request->param('content');
               $pcontent->product_id=$product_id;
               $pcontent->update_at=date("Y-m-d H:i:s",time());
               //图片保存
               $Pimages=new DBPimage;
               $lists=[
                 ['image_no'=>0,'image_path'=>$request->param('preview1',''),'product_id'=>$product_id,'update_at'=>date('Y-m-d H:i:s',time())],
                 ['image_no'=>0,'image_path'=>$request->param('preview2',''),'product_id'=>$product_id,'update_at'=>date('Y-m-d H:i:s',time())],
                 ['image_no'=>0,'image_path'=>$request->param('preview3',''),'product_id'=>$product_id,'update_at'=>date('Y-m-d H:i:s',time())]
               ];

               //保存添加
                if($Pimages->saveAll($lists) && $pcontent->save()){
                        $m3_result->status=1;
                        $m3_result->message='添加成功';
                        return  $m3_result->toJson();
                }else{
                        $m3_result->status=2;
                        $m3_result->message='添加失败';
                        return  $m3_result->toJson();
                }
           }
        }


        //界面数据显示
        $pcate=DBPcate::all()->toArray();
        $cate=getChildMes($pcate,0,1);

         $assign=[
            'cate'=>$cate
        ];
        $this->assign($assign);
        return $this->fetch();
    }


    //修改产品信息
    public function edit(){

         $request=request::instance();

         //产品信息修改
         if($name=$request->param('name')){

              $m3_result=new M3Result;
              $id=$request->param('id');
              $product=DBProduct::get($id);
              $product->name=$name;
              $product->cate_id=$request->param('cate_id');
              $product->summary=$request->param('summary');
              $product->price=$request->param('price');
              $product->inventory=$request->param('inventory');
              $product->preview=$request->param('preview');
              $product->update_at=date("Y-m-d H:i:s",time());

              if($product->save()){
                     //删除旧记录
                     DBPimage::destroy(['product_id'=>$id]);

                     //插入新图片记录
                     $Pimages=new DBPimage;
                     $lists=[
                         ["image_no"=>0,'image_path'=>$request->param('preview1',''),'product_id'=>$id,'update_at'=>date('Y-m-d H:i:s',time())],
                         ["image_no"=>0,'image_path'=>$request->param('preview2',''),'product_id'=>$id,'update_at'=>date('Y-m-d H:i:s',time())],
                         ["image_no"=>0,'image_path'=>$request->param('preview3',''),'product_id'=>$id,'update_at'=>date('Y-m-d H:i:s',time())]
                     ];


                    //更新详细内容
                    $pcontent=DBpcontent::get(['product_id'=>$id]);
                    $pcontent->content=$request->param('content');
                    $pcontent->update_at=date("Y-m-d H:i:s",time());

                   //保存修改
                    if($Pimages->saveAll($lists) && $pcontent->save()){
                        $m3_result->status=3;
                        $m3_result->message='修改成功';
                        return  $m3_result->toJson();
                    }else{
                        $m3_result->status=4;
                        $m3_result->message='修改失败';
                        return  $m3_result->toJson();
                    }

              }
         }

         //产品信息显示
         if($id=$request->param('id')){
              $product=DBProduct::get($id);
              $pimages=DBPimage::all(['product_id'=>$id])->toArray();
              $pcontent=DBpcontent::get(['product_id'=>$id]);
              $pcate=DBPcate::all()->toArray();
              $cate=getChildMes($pcate,0,1);

            $assign=[
                'cate'=>$cate,
                'product'=>$product,
                'pimages'=>$pimages,
                'content'=>$pcontent
            ];
            $this->assign($assign);
            return $this->fetch();

         }else{
            return '非法操作';
         }
    }


    public function del(){

         $request=request::instance();

         if($id=$request->param('id')){
              $m3_result=new M3Result;
              if(DBProduct::destroy($id) && DBPimage::destroy(['product_id'=>$id]) && DBpcontent::destroy(['product_id'=>$id])){
                  $m3_result->status=5;
                  $m3_result->message='删除成功';
                  return  $m3_result->toJson();
              }else{
                  $m3_result->status=6;
                  $m3_result->message='删除失败';
                  return  $m3_result->toJson();
              }
         }else{
            return '非法操作';
        }
    }
}
