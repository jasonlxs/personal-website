<?php
/**
 * @Author: Marte
 * @Date:   2018-03-17 11:47:27
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-01 21:09:05
 */
namespace app\data\model;
use think\Model;

class DBcomment extends Model{
    protected $table="lxs_comment";
    protected $pk='id';

    protected $resultSetType='collection';

     public function member(){
       return $this->hasOne('DBmember','id','mid')->field('nickname,phone,email');
    }
}