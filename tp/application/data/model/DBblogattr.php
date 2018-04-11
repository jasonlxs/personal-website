<?php
/**
 * @Author: Marte
 * @Date:   2018-03-29 21:08:55
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-01 21:09:56
 */
namespace app\data\model;
use think\Model;


class DBblogattr extends Model{

    protected $table='lxs_blogattr';
    protected $pk = 'id';

    protected $resultSetType='collection';


}