<?php
namespace app\admin\validate;

use think\Validate;

/**
* 
*/
class Login extends Validate
{
	
    // 验证规则
    protected $rule = [
        ['email','require','用户名不能为空'],
        ['password','require','密码不能为空']
    ];

}