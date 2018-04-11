<?php
/**
 * @Author: Marte
 * @Date:   2018-03-31 13:37:02
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-01 21:10:02
 */
namespace app\data\model;
use think\Model;


class DBclike extends Model{

    protected $table='lxs_clike';
    protected $pk = 'id';

    protected $resultSetType='collection';


}