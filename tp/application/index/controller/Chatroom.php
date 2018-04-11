<?php
/**
 * @Author: Marte
 * @Date:   2018-04-02 20:33:05
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-07 17:14:13
 */
namespace app\index\controller;
use think\Request;
use app\data\model\DBchat;

class Chatroom extends Common{

    public function index(){
        $request=Request::instance();
        $chat=DBchat::all()->toArray();

        $assign=[
          'chat'=>$chat,
          'current'=>$request->param('current')
        ];
        $this->assign($assign);
        return $this->fetch();
    }

    public function saveData(){

          $request=Request::instance();
          if($uid=$request->param('uid')){
               $chat=new DBchat;
               $chat->uid=$uid;
               $chat->chatContent=$request->param('content');
               if($chat->save()){
                   return '保存成功';
               }else{
                   return '保存失败';
               }
          }else{
              return '非法操作';
          }
    }
}