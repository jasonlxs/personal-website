<?php
/**
 * @Author: Marte
 * @Date:   2018-03-14 20:54:35
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-07 15:38:37
 */
namespace app\data\model;
use think\Model;


class DBarticle extends Model{

    protected $table='lxs_article';
    protected $pk = 'id';

    protected $resultSetType='collection';

    public function DBcategory(){

        return $this->hasOne('DBcategory','id','cid');
    }
     public function attr(){

         return $this->belongsToMany('DBattr','blogattr','aid','bid');
    }
}