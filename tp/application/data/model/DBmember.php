<?php
/**
 * @Author: Marte
 * @Date:   2018-03-22 20:14:41
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-01 20:47:13
 */
namespace app\data\model;
use think\Model;

class DBmember extends Model{
    protected $table="lxs_member";
    protected $pk='id';

    protected $resultSetType='collection';

     public function order(){
       return $this->hasMany('DBorder','member_id','id');
    }
}