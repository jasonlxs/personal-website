<?php
/**
 * @Author: Marte
 * @Date:   2018-03-30 23:39:07
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-01 21:09:44
 */
namespace app\data\model;
use think\Model;


class DBalike extends Model{

    protected $table='lxs_alike';
    protected $pk = 'id';

    protected $resultSetType='collection';


}