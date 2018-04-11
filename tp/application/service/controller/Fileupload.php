<?php
/**
 * @Author: Marte
 * @Date:   2018-04-06 12:57:02
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-07 12:59:52
 */
namespace app\service\controller;

use think\Request;
use Models\M3Result;

class Fileupload{

     //图片上传
    public function  upload(){
        $request=request::instance();
        $m3_result=new M3Result;

         if($_FILES['file']['error']>0){
            $m3_result->status=2;
            $m3_result->message='错误代码：'.$_FILES['file']['error'];
            return $m3_result->toJson();
         }

         if($_FILES['file']['size']>1024*1024){
            $m3_result->status=2;
            $m3_result->message='上传图片大小超过1M！';
            return $m3_result->toJson();
         }



         $file = request()->file('file');

         if($file){


          //设置图片存放路径
           $dirname=ROOT_PATH.'public'.DS.'static'.DS.'uploads';
           if(!is_dir($dirname)){
                mkdir($dirname);
           }
           $info=$file->move($dirname);


           //图片上传返回
           if($info){
               $m3_result->status=6;
               $m3_result->message='图片上传成功';
               $m3_result->uri=$info->getSaveName();
               return $m3_result->toJson();
               die;
               //获取扩展名
                echo $info->getExtension();
                //默认日期文件夹+文件名
                echo $info->getSaveName();
                //文件名
                echo $info->getFileName();

           }else{
              echo $file->getError();
           }
         }
    }

}