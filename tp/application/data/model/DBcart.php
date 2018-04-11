<?php
/**
 * @Author: Marte
 * @Date:   2018-03-28 18:59:17
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-01 23:00:28
 */
namespace app\data\model;
use think\Model;

class DBcart extends Model{
    protected $table='lxs_cart';
    protected $pk='id';

    protected $resultSetType='collection';
    public function product(){

        return $this->hasOne('DBproduct','id','product_id');
    }

}