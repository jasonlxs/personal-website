<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
//
//格式化输出
function show($str){
    echo "<pre>";
    print_r($str);
}


/**
 * @Author    Hybrid
 * @DateTime  2018-03-10
 * @copyright [copyright]
 * @license   [license]
 * @version   [version]
 * @param     [type]      $arr    分类表中所有数据的数组
 * @param     [type]      $pid    父级分类id
 * @param     integer     $status 0按多维数组方式组合，1按一维数组方式组合
 * @return    [type]      $child
 * 递归查询父级别的所有子级信息
 */
function getChildMes($arr,$pid=0,$status=0,$checked=[]){
    $child=[];
    if($status==0){
        //多维数组方式
        foreach($arr as $v){
            if($v['pid']==$pid){
                $v['child']=getChildMes($arr,$v['id'],$status,$checked);
                if(count($checked) && in_array($v['id'],$checked))$v['checked']=1;
                $child[]=$v;
             }
        }
        return $child;
    }else{
        //一维数组方式
         foreach($arr as $v){
            if($v['pid']==$pid){
                $child[]=$v;
                $child=array_merge($child, getChildMes($arr,$v['id'],$status,$checked));
             }
        }
        return $child;
    }

}
//评论的递归输出
function recursion($arr){
    if($arr==null)return;
    foreach($arr as $v){
        $str=
        "<tr class='text-c'>
        <td><input type='checkbox' value='1' name=''></td>
        <td>".$v['id']."</td>
        <td>".$v['content']."</td>
        <td>".$v['mid']."</td>
        <td>".$v['aid']."</td>
        <td>".$v['pid']."</td>
        <td class='f-14 user-manage'>"
        .' <a title="编辑" href="javascript:;" onclick="com_edit('."'".$v['id']."','".'500'."','".'500'."')".'"'.' class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a>'
        .' <a title="删除" href="javascript:;" onclick="com_del(this,'.$v['id'].')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a>
        </td>
      </tr>';
        echo $str;
        $child=$v['child'];
        recursion($child);
    }
}

//UUID生成
function uuid($prefix = '')
  {
    $chars = md5(uniqid(mt_rand(), true));
    $uuid  = substr($chars,0,8) . '-';
    $uuid .= substr($chars,8,4) . '-';
    $uuid .= substr($chars,12,4) . '-';
    $uuid .= substr($chars,16,4) . '-';
    $uuid .= substr($chars,20,12);
    return $prefix . $uuid;
  }