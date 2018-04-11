<?php
/**
 * @Author: Marte
 * @Date:   2018-03-29 21:09:54
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-01 21:09:51
 */
namespace app\data\model;
use think\Model;


class DBattr extends Model{

    protected $table='lxs_attr';
    protected $pk = 'id';

    protected $resultSetType='collection';


}