<?php
namespace app\mobile\controller;

use think\Request;
use app\data\model\DBarticle;

/**
 *  博客详情
 */
class Detail
{
    public function index()
    {
        $request=Request::instance();
        $aid=$request->param('aid');
        $article=new DBarticle;
        $aone=$article::get($aid);
        $aone['thumb']=str_replace('\\',"/",$aone['thumb']);
        $aone['thumb']='http://'.$_SERVER['HTTP_HOST'].'/static/uploads/'.$aone['thumb'];
        return json_encode($aone);
    }
}
