<?php
/**
 * @Author: Marte
 * @Date:   2017-08-16 15:22:52
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-01 23:20:07
 */
namespace app\mobile\controller;

use think\Request;
use think\Session;
use \Verify\Verify;
use \Models\M3Result;
use app\data\model\DBmember;
//小程序登录凭证请求地址：https://api.weixin.qq.com/sns/jscode2session?appid=wx937d4f66aedcacda&secret=c4933b40fcad49adc4df77bf874c9cca&js_code=071noojD19T1M00rjvjD1QzfjD1nooj4&grant_type=authorization_code
//appid=APPID      wx937d4f66aedcacda
//&secret=SECRET   c4933b40fcad49adc4df77bf874c9cca
//&js_code=JSCODE
//&grant_type=authorization_code
//localhost/TP5/public/mobile/login/index?uname=lxsptlxs@163.com&upwd=123456&code=z&wx_code=081A6nKg2ds52D06GcLg2MizKg2A6nKU&session_id=rlvevo3g1g1c03lq4iu2nfvhq1
class Login{

    public function login(){
         $request=Request::instance();
         if($request->post()!=null){
           $m3_result=new M3Result;
           $verify=new Verify;

           $uname=$request->param('uname');
           $upwd=md5('lxs'.$request->param('upwd'));
           $code=$request->param('code');



           //验证验证码
           if(!$verify->check($code)){
                 $m3_result->status=8;
                 $m3_result->message='验证码不正确,请重新输入';
                 return $m3_result->toJson();
           }

           //验证账号或密码
           if(strpos($uname,'@')){
               if(!$member=DBmember::get(['email'=>$uname,'upwd'=>$upwd])){
                     $m3_result->status=12;
                     $m3_result->message='账号或密码不正确,请重新输入';
                     return $m3_result->toJson();
                   }
           }else{
                 if(!$member=DBmember::get(['phone'=>$uname,'upwd'=>$upwd])){
                     $m3_result->status=12;
                     $m3_result->message='账号或密码不正确,请重新输入';
                     return $m3_result->toJson();
               }
           }
           $member=$member->toArray();
           //验证是否激活
           if(!$member['is_active']){
                $m3_result->status=13;
                $m3_result->message='账号未激活,请登录邮箱进行激活';
                return $m3_result->toJson();
           }

            Session::prefix('member');
            Session::set('membertime',time(),'member');
            Session::set('mid',$member['id'],'member');
            Session::set('mname',$uname,'member');
            //增强验证信息
            Session::set('check',$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'],'member');

            $m3_result->status=14;
            $m3_result->message='登录成功';
            $m3_result->id=$member['id'];
            $m3_result->uname=$uname;

            return  $m3_result->toJson();
          /*
           //登录凭证请求地址
           $url="https://api.weixin.qq.com/sns/jscode2session";
           //提交参数
           $postData=[
             'appid'=>'wx937d4f66aedcacda',
             'secret'=>'c4933b40fcad49adc4df77bf874c9cca',
             'js_code'=>$wx_code,
             'grant_type'=>'authorization_code'
           ];

           //file_get_content发送http请求
           $postData=http_build_query($postData);
           $opt=[
              'http'=>[
                'method'=>'post',
                'header'=>'Host:localhost\r\n'.'Content-type:application/json\r\n'.'Content-length:'.strlen($postData).'/r/n',
                'content'=>$postData
              ]
           ];
           $content=stream_context_create($opt);
           return file_get_contents('https://api.weixin.qq.com/sns/jscode2session',false,$content);


            //curl方式发送http请求
            $ch=curl_init();

            //设置选项，包括URL
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
            //curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
            curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
            curl_setopt ( $ch, CURLOPT_HTTPHEADER, ["content-type: application/json; charset=UTF-8"] );
            curl_setopt ( $ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)' );
            curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
            curl_setopt ( $ch, CURLOPT_AUTOREFERER, 1 );
            //执行并获取返回值
            $info=curl_exec($ch);

            //关闭
            curl_close($ch);
            print_r(json_decode($info));


            //socket方式
            $URL=parse_url($url);
            if(!isset($URL['port'])){
                $URL['port'] = 443;
            }

            $postData=http_build_query($postData);
            $request="POST ".$URL['path']." HTTP/1.1\r\n";
            $request.="Host:".$URL['host']."\r\n";
            $request.="Content-type:application/json\r\n";
            $request.="Content-length:".strlen($postData)."\r\n\r\n";
            $request.=$postData;

            try{
                $fp = fsockopen('ssl://'.$URL['host'], $URL['port']);
                fwrite($fp, $request);
                $res = fread($fp, 1024);
            }catch(Exception $e){
                fclose($fp);
                return false;
            }
            fclose($fp);

            $res=explode("\r\n\r\n",$res);
            $restr=json_decode($res[1],true);


            if(isset($restr['errcode'])){
                $m3_result->status=$restr['errcode'];
                $m3_result->message=$restr['errmsg'];
                return $m3_result->toJson();
            }

            $value=$restr['openid'].$restr['session_key'];
            */
       }else{
           exit('非法访问！');
       }
    }

    public function loginout(){

        $m3_result=new M3Result;
        Session::clear('member');
        $m3_result->status=1;
        $m3_result->message='安全退出';
        return $m3_result->toJson();

    }
}
