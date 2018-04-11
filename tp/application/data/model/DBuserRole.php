<?php
/**
 * @Author: Marte
 * @Date:   2018-03-11 16:26:10
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-01 20:59:01
 */
namespace app\data\model;
use think\Model;

class DBuserRole extends Model{

    protected $table='lxs_auth_group_access';
    protected $pk = 'id';

    protected $resultSetType='collection';
}