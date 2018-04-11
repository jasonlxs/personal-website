<?php
/**
 * @Author: Marte
 * @Date:   2018-04-03 17:16:40
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-03 17:17:14
 */
namespace app\data\model;
use think\Model;


class DBchat extends Model{

    protected $table='lxs_chat';
    protected $pk = 'id';

    protected $resultSetType='collection';


}