<?php
/**
 * @Author: Marte
 * @Date:   2018-03-10 20:20:47
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-07 14:19:18
 */
namespace app\admin\controller;
use app\data\model\DBrole;
use app\data\model\DBrule;
use app\data\model\DBuserRole;
use app\data\model\DBuser;
use think\request;
use Models\M3Result;

class Role extends Common{

    //角色列表
    public function index(){

        $group=DBrole::all()->toArray();
        foreach($group as &$v){
            //权限说明
            $rule_id=$v['rules'].','.$v['parent_rules'];
            $rule_id=explode(',',$rule_id);
            $rule=DBrule::all(function($query)use($rule_id){
               $query->where('id','in',$rule_id);
            })->toArray();
            $str='';
            foreach ($rule as $value) {
                $str.=$value['title'].',';
            }

            $v['str']=rtrim($str,',');

            //用户列表 还有另一种方式通过多对多模型查询
            $uname='';
            $user_list=DBuserRole::all(['group_id'=>$v['id']])->toArray();
            $user=DBuser::all()->toArray();
            foreach($user_list as $l){
                foreach($user as $u){
                    if($l['uid']==$u['id']){
                         $uname.=$u['uname'].',';
                    }
                }
            }
            $v['uname']=rtrim($uname,',');

        }
        $assign=[
          'group'=>$group,
        ];
        $this->assign($assign);
        return $this->fetch();
    }

    //添加角色
    public function insert(){
        $request=request::instance();

        $all_rules=DBrule::all()->toArray();
        $m3_result=new M3Result;

        //插入新角色到数据库
        if($request->param('roleName')){
            $dbrole=new DBrole;
            //组合权限
            $rule2=isset($_POST['user-Character-0-0'])?$_POST['user-Character-0-0']:[];
            $rule3=isset($_POST['user-Character-0-0-0'])?$_POST['user-Character-0-0-0']:[];
            $rules=array_merge($rule2,$rule3);
            $rules=implode(',',$rules);


            $dbrole->title=$request->post('roleName');
            $dbrole->status=$request->post('status');
            $dbrole->rules=$rules;
            $dbrole->description=$request->post('description');
            //组合最高级权限
            $dbrole->parent_rules=implode(',',$_POST['user-Character-0']);


            if($dbrole->save()){
               $m3_result->status=1;
               $m3_result->message='添加成功!';
               return  $m3_result->toJson();
           }else{
               $m3_result->status=2;
               $m3_result->message='添加失败,请重试!';
               return  $m3_result->toJson();
           }
        }

        //组合选择权限数据
        $parent_rules=getChildMes($all_rules);
        $assign=[
          'rules'=>$parent_rules
        ];
        $this->assign($assign);
         return $this->fetch();
    }

    //修改角色
    public function edit(){
        $request=request::instance();
        $parent_rules=DBrule::all(['pid'=>0])->toArray();
        $all_rules=DBrule::all()->toArray();
        $m3_result=new M3Result;

        //保存修改
        if($request->post('roleName')){
            $dbrole=DBrole::get($request->post('id'));
            //组合权限
            $rules=array_merge($_POST['user-Character-0-0'],$_POST['user-Character-0-0-0']);
            $rules=implode(',',$rules);


            $dbrole->title=$request->post('roleName');
            $dbrole->status=$request->post('status');
            $dbrole->rules=$rules;
            $dbrole->description=$request->post('description');
            //组合最高级权限
            $dbrole->parent_rules=implode(',',$_POST['user-Character-0']);


            if($dbrole->save()){
               $m3_result->status=3;
               $m3_result->message='修改成功!';
               return  $m3_result->toJson();
           }else{
               $m3_result->status=4;
               $m3_result->message='修改失败,请重试!';
               return  $m3_result->toJson();
           }
        }


        //查询需要编辑的角色记录
        $role=DBrole::get($request->param('id'));
        $parent=explode(',',$role['parent_rules']);
        $childs=explode(',',$role['rules']);
        //组合选择权限数据
        $child=[];
        foreach($parent_rules as &$v){
           if(in_array($v['id'],$parent))$v['checked']=1;
           $child=getChildMes($all_rules,$v['id'],0,$childs);
           $v['child']=$child;
        }

        $assign=[
            'rules'=>$parent_rules,
            'role'=>$role
        ];
        $this->assign($assign);
        return $this->fetch();
    }


    //删除角色
    public function del(){
        $request=request::instance();
        $m3_result=new M3Result;

        if($id=$request->param("id")){
            if(DBuserRole::all(['group_id'=>$id])){
                $m3_result->status=7;
                $m3_result->message='该角色存在用户，请先删除用户';
                return  $m3_result->toJson();
            }else{
               if(Role::destroy($id)){
                    $m3_result->status=5;
                    $m3_result->message='删除成功!';
                    return  $m3_result->toJson();
               }else{
                    $m3_result->status=6;
                    $m3_result->message='删除失败!';
                    return  $m3_result->toJson();
               }
            }
        } else{
            $this->error('非法操作');
        }
    }
}