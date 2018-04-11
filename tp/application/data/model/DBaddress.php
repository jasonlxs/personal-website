<?php
/**
 * @Author: Marte
 * @Date:   2018-01-09 18:26:53
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-06 22:13:59
 */
namespace app\data\model;

use think\Model;

class DBaddress extends Model{

    protected $table='lxs_address';
    protected $pk = 'id';

    protected $resultSetType='collection';

}