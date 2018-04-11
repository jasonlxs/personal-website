<?php
/**
 * @Author: Marte
 * @Date:   2018-04-01 21:19:48
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-01 22:40:49
 */
namespace app\service\controller;

use think\Request;
use Verify\Verify;

class Validatecode{
    //创建验证码
    public function create(){

        $request=Request::instance();
        $sess_id=$request->param('seid',0);
        $verify=new Verify(['length'=>1,'imageW'=>100,'imageH'=>50,'sess_id'=>$sess_id]);
        ob_end_clean();
        return $verify->entry();

    }
}