<?php
/**
 * @Author: Marte
 * @Date:   2018-03-13 22:06:01
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-01 20:46:54
 */
namespace app\data\model;
use think\Model;

class DBcategory extends Model{

    protected $table='lxs_category';
    protected $pk = 'id';

    protected $resultSetType='collection';
}