<?php
/**
 * @Author: Marte
 * @Date:   2018-03-08 20:08:02
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-07 14:18:58
 */
namespace app\admin\controller;
use app\data\model\DBuser;
use app\data\model\DBrole;
use app\data\model\DBuserRole;
use Models\M3Result;
use think\Request;

class User extends Common{


    //管理员列表
    public function index(){
        $user=DBuser::all(function($query){
             $query->field('id,uname,logintime,loginip,lock,create_at,update_at');
        })->toArray();

        //管理员与角色多对多模型查询
        foreach($user as &$u){
            $v=DBuser::get($u['id']);
            $roles=$v->DBrole->toArray();
            $str='';
            foreach($roles as $v){
                $str.=$v['title'];
            }
            $u['role']=rtrim($str,',');
        }
        $assign=[
          'user'=>$user,
        ];
        $this->assign($assign);
        return $this->fetch();
    }
    //新建管理员
    public function insert(){
        $request=Request::instance();
        $m3_result=new M3Result;

        if($request->param('adminName')){
            $user=new DBuser;
            $user->uname=$request->param('adminName');
            $user->upwd=md5($request->param('password'));
            $user->lock=$request->param('sex');
            $user->logintime=time();
            $user->loginip=$_SERVER['REMOTE_ADDR'];
            $user->update_at=date('Y-m-d H:i:s',time());
            $role=$_POST['adminRole'];

            if($user->save()){
               $uid=$user->id;
               //插入用户角色中间表
               $user_role=new DBuserRole;

               $data=[];
               foreach($role as $v){
                  $data[]=['uid'=>$uid,'group_id'=>$v];
               }

               if($user_role->saveAll($data)){
                    $m3_result->status=1;
                   $m3_result->message='添加成功!';
                   return  $m3_result->toJson();
               }else{
                   $m3_result->status=2;
                   $m3_result->message='添加失败!';
                   return  $m3_result->toJson();
               }

            }else{
               $m3_result->status=2;
               $m3_result->message='添加失败!';
               return  $m3_result->toJson();
            }
        }

        $roles=DBrole::all()->toArray();


        $assign=[
          'roles'=>$roles,
        ];
        $this->assign($assign);
        return $this->fetch();
    }

    //编辑管理员信息
    public function edit(){
        $request=Request::instance();
        $m3_result=new M3Result;

        //保存修改信息
        if($request->param('adminName')){
           $id=$request->post('id');
           $adminRole=$_POST['adminRole'];
           $user=DBuser::get($id);
           $user->uname=$request->param('adminName');
           if($request->param('password')!=null)$user->upwd=md5($request->param('password'));
           $user->lock=$request->param('status');
           $user->logintime=time();
           $user->loginip=$_SERVER['REMOTE_ADDR'];
           $user->update_at=date("Y-m-d H:i:s",time());
           if($user->save()){
              //先删除旧的用户组
              DBuserRole::destroy(['uid'=>$id]);

              //重新插入用户角色中间表
              $user_role=new DBuserRole;
              $data=[];
              foreach ($adminRole as $v) {
                  $data[]=['uid'=>$id,'group_id'=>$id];
              }
              if($user_role->saveAll($data)){
                    $m3_result->status=3;
                    $m3_result->message='修改成功!';
                    return  $m3_result->toJson();
              }else{
                 $m3_result->status=4;
                 $m3_result->message='修改失败!';
                 return  $m3_result->toJson();
              }

           }else{
               $m3_result->status=4;
               $m3_result->message='修改失败!';
               return  $m3_result->toJson();
           }
        }

        //显示管理员已有信息
        $user=DBuser::get($request->param('id'));
        $userRole=DBuserRole::all(['uid'=>$user['id']])->toArray();


        $roles=DBrole::all()->toArray();
        foreach($userRole as $v){
            foreach ($roles as &$r) {
               if($v['group_id']==$r['id']){
                  $r['checked']=1;
                  continue;
               }
            }
        }

        $assign=[
          'user'=>$user,
          'roles'=>$roles
        ];
        $this->assign($assign);
        return $this->fetch();
    }

    //删除管理员
    public function del(){
        $request=Request::instance();
        $m3_result=new M3Result;
        if($id=$request->param('id')){
               if(DBuser::destroy($id) && DBuserRole::destroy(['uid'=>$id])){
                   $m3_result->status=5;
                   $m3_result->message='删除成功!';
                   return  $m3_result->toJson();
               }else{
                   $m3_result->status=6;
                   $m3_result->message='删除失败!';
                   return  $m3_result->toJson();
               }
        }else{
            $this->error('非法操作');
        }
    }

    //停用管理员
    public function disable(){
        $request=Request::instance();
        $m3_result=new M3Result;
        if($id=$request->param('id')){
          $user=DBuser::get($id);
          if($user['lock']==0){
             //停用
             $user->lock=1;
             if($user->save()){
                   $m3_result->status=7;
                   $m3_result->message='已停用!';
                   return  $m3_result->toJson();
             }
          }else{
             //启用
             $user->lock=0;
             if($user->save()){
               $m3_result->status=7;
               $m3_result->message='已启用!';
               return  $m3_result->toJson();
             }
          }
        }
    }
}