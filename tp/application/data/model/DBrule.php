<?php
/**
 * @Author: Marte
 * @Date:   2018-03-10 15:10:53
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-01 20:47:57
 */
namespace app\data\model;
use think\Model;

class DBrule extends Model{

    protected $table='lxs_auth_rule';
    protected $pk = 'id';

    protected $resultSetType='collection';
}