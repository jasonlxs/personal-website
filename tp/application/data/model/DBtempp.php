<?php
/**
 * @Author: Marte
 * @Date:   2017-08-11 16:54:07
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-01 23:03:41
 */
namespace app\data\model;

use think\Model;

class DBtempp extends Model{

    protected $table='lxs_tempp';
    protected $pk = 'id';
    protected $resultSetType = 'collection';
}