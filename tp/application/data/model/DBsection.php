<?php
/**
 * @Author: Marte
 * @Date:   2018-04-07 15:59:08
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-07 15:59:41
 */
namespace app\data\model;

use think\Model;

class DBsection extends Model{

    protected $table='lxs_section';
    protected $pk = 'id';

    protected $resultSetType='collection';

}