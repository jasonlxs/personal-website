<?php
/**
 * @Author: Marte
 * @Date:   2018-03-25 09:37:54
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-01 20:47:32
 */
namespace app\data\model;
use think\Model;

class DBpcate extends Model{
    protected $table='lxs_pcate';
    protected $pk='id';

    protected $resultSetType='collection';
}