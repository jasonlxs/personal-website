<?php
/**
 * @Author: Marte
 * @Date:   2018-03-28 19:00:11
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-01 20:47:18
 */
namespace app\data\model;
use think\Model;

class DBorder extends Model{
    protected $table='lxs_order';
    protected $pk='id';

    protected $resultSetType='collection';
}