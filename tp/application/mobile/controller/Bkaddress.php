<?php
/**
 * @Author: Marte
 * @Date:   2018-01-09 18:25:50
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-06 22:40:24
 */
namespace app\mobile\controller;

use think\Request;
use think\Session;
use Models\M3Result;
use app\data\model\DBaddress;


class Bkaddress{

     public function index(){
         $m3_result=new M3Result;
         $member_id=Session::get('mid','member');

         $address=DBaddress::all(['member_id'=> $member_id])->toArray();


         if($address){
             $def_id=DBaddress::get(['member_id'=> $member_id,'isdefault'=>1])->toArray();

             $m3_result->status=1;
             $m3_result->message='返回数据成功';
             $m3_result->def_id=$def_id['id'];
             $m3_result->address=$address;
         }else{
             $m3_result->status=2;
             $m3_result->message='数据为空';
         }

         return $m3_result->toJson();

     }

    //设置默认地址
    public function setDefault(){

          $request=Request::instance();
        if($id=$request->param('id')){

            $member_id=Session::get('mid','member');
            $addr=DBaddress::get($id)->toArray();
            if($addr['isdefault']==1){
                return '该地址已为默认地址！';
            }else{

                $address=new DBaddress;
                $address->save(['isdefault'=>0],['member_id'=>$member_id,'isdefault'=>1]);

                if($address->save(['isdefault'=>1],['id'=>$id])){
                    return '设置默认地址成功';
                }else{
                    return '设置默认地址失败';
                }
             }

        }else{
            return '请不要以这种方式访问!';
        }

    }


    //删除地址
    public function delAddress(){
        $request=Request::instance();
        if($id=$request->param('id')){
            $member_id=Session::get('mid','member');
            $addr_one=DBaddress::get($id)->toArray();

            $count=DBaddress::where('member_id',$member_id)->count();


            //删除时需判断是否默认或只剩一条
            if($count==1||$addr_one['isdefault']!=1){
                if(DBaddress::destroy($id)){
                   return '删除成功';
                }else{
                   return '删除失败，请重试';
                }
            }else{
                return '请先设置其他默认地址，再删除';
            }

        }else{
            return '请不要以这种方式访问!';
        }
    }

    //新增收货地址
    public function addAddress(){

        $request=Request::instance();
        $m3_result=new M3Result;
        $area=$request->param('area');
        $area=rtrim($area,'-');
        $member_id=Session::get('mid','member');
        $addr=new DBaddress;

        if(!DBaddress::all(['member_id'=>$member_id])->toArray()){
            $addr->isdefault=1;
        }

        $addr->contacts=$request->param('contacts');
        $addr->phone=$request->param('phone');
        $addr->postcode=$request->param('postcode');
        $addr->address=$area.$request->param('address');
        $addr->member_id=$member_id;
        $addr->update_at=date('Y-m-d H:i:s',time());

        if($addr->save()){

            $m3_result->status=1;
            $m3_result->message='添加成功';

            return $m3_result->toJson();
        }else{
            $m3_result->status=2;
            $m3_result->message='添加失败';

            return $m3_result->toJson();
        }
    }


   //修改收货地址
   public function modAddress(){
       $m3_result=new M3Result;
       $request=Request::instance();
       if($contacts=$request->param('contacts')){
          //保存修改数据

           $id=$request->param('id');
           $area=$request->param('area');
           $area=rtrim($area,'-');


           $addr=DBaddress::get($id);
           $addr->contacts=$contacts;
           $addr->phone=$request->param('phone');
           $addr->postcode=$request->param('postcode');
           $addr->address=$area.$request->param('address');
           $addr->update_at=date('Y-m-d H:i:s',time());
           if($addr->save()){
                $m3_result->status=1;
                $m3_result->message='修改成功';
                return $m3_result->toJson();
           }else{
                $m3_result->status=2;
                $m3_result->message='修改失败';
                return $m3_result->toJson();
           }

       }else{
          //修改的初始数据
           $id=$request->param('id');
           $addr_one=DBaddress::get($id)->toArray();
           $pos=strpos($addr_one['address'],'区');
           $region=substr($addr_one['address'],0,$pos+3);
           $region=explode('-',$region);
           $addr_one['address']=substr($addr_one['address'],$pos+3);

           $m3_result->status=1;
           $m3_result->message='返回数据成功';
           $m3_result->addr=$addr_one;
           $m3_result->region=$region;
           return $m3_result->toJson();
       }




   }
}