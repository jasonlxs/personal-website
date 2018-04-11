<?php
/**
 * @Author: Marte
 * @Date:   2018-03-28 11:02:08
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-01 20:47:40
 */
namespace app\data\model;
use think\Model;

class DBpcontent extends Model{
    protected $table='lxs_pcontent';
    protected $pk='id';

    protected $resultSetType='collection';
}