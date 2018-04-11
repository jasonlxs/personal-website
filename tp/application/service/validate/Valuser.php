<?php
/**
 * @Author: Marte
 * @Date:   2018-04-06 09:00:01
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-04-06 10:52:32
 */
namespace app\service\validate;

use think\Validate;

class Valuser extends Validate{

    //验证规则
     protected $rule = [

        'uname'  =>  'require|max:60',
        'upwd'   =>  'require|min:6',
        'code'   =>  'require'
    ];
    //验证提示信息
    protected $message=[

        'uname.require' =>'用户不能为空',
        'upwd.require' =>'密码不能为空',
        'code.require' =>'验证码不能为空',
    ];

}