<?php
/**
 * @Author: Marte
 * @Date:   2018-03-09 19:00:34
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-01 20:59:22
 */
namespace app\data\model;
use think\Model;

class DBuser extends Model{

    protected $table='lxs_user';
    protected $pk = 'id';

    protected $resultSetType='collection';

    public function DBrole()
    {
        return $this->belongsToMany('DBrole','auth_group_access','group_id','uid');
    }
}