<?php
/**
 * @Author: Marte
 * @Date:   2018-03-25 18:41:42
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-01 20:47:45
 */
namespace app\data\model;
use think\Model;

class DBpimage extends Model{
    protected $table='lxs_pimage';
    protected $pk='id';

    protected $resultSetType='collection';
}