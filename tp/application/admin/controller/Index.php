<?php
/**
 * @Author: Marte
 * @Date:   2018-03-08 20:07:06
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-01 21:00:49
 */
namespace app\admin\controller;
use think\request;
use think\Session;
use app\data\model\DBuserRole;
use app\data\model\DBrole;
use app\data\model\DBrule;
use app\data\model\DBblogattr;
use Page\Page;

class Index extends Common{
    //后台主页框架
    public function index(){
        //根据用户权限判断功能展示
        $id=Session::get('uid','user');
        $userRole=DBuserRole::all(['uid'=>$id])->toArray();
        $roles=DBrole::all()->toArray();
        //得该用户的最高权限模块的ID数组
        $arr=[];
        foreach ($userRole as $v) {
           foreach($roles as $r){
              if($v['group_id']==$r['id']){
                  $arr=array_merge($arr,explode(',',$r['parent_rules']));
              }
           }
        }
        //获得最高权限ID的名称数组
        $rule=DBrule::all(function($query)use($arr){
              $query->where('id','in',$arr);
        })->toArray();
        $arr=[];
        foreach ($rule as $ru) {
            $arr[]=$ru['name'];
        }

        //排除未授权模块
        $rule=['menu-article','menu-product','menu-comments','menu-member','menu-admin','menu-tongji','menu-system'];
        $arr=array_diff($rule,$arr);
        $arr=implode(',',$arr);
        $assign=[
          'arr'=>$arr,
          'user'=>Session::get('uname','user')
        ];
        $this->assign($assign);
        return $this->fetch();
    }

    //欢迎页面
    public function welpage(){

        return $this->fetch();
    }

}
