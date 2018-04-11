<?php
/**
 * @Author: Marte
 * @Date:   2018-03-25 18:41:22
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-01 22:42:54
 */
namespace app\data\model;
use think\Model;

class DBproduct extends Model{
    protected $table='lxs_product';
    protected $pk='id';

    protected $resultSetType='collection';


    public function Pimages(){

         return $this->hasMany('DBpimage','product_id','id');
    }
}