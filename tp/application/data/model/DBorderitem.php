<?php
/**
 * @Author: Marte
 * @Date:   2018-03-28 19:02:50
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-01 22:58:24
 */
namespace app\data\model;
use think\Model;

class DBorderitem extends Model{
    protected $table='lxs_orderitem';
    protected $pk='id';

    protected $resultSetType='collection';

    public function Product(){

        return $this->hasone('DBproduct','id','product_id')->field('id,name,price,preview');
    }
}