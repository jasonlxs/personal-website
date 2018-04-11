<?php
/**
 * @Author: Marte
 * @Date:   2018-03-10 20:21:12
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-01 23:02:12
 */
namespace app\data\model;
use think\Model;

class DBrole extends Model{

    protected $table='lxs_auth_group';
    protected $pk = 'id';

    protected $resultSetType='collection';

     public function users()
    {
        return $this->belongsToMany('DBuser','auth_group_access','uid','group_id');
    }
}