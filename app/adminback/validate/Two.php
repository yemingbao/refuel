<?php
namespace app\admin\validate;

use think\Validate;

/**
* 
*/
class Two extends Validate
{
	
    // 验证规则
    protected $rule = [
        ['name','require'],
    ];
}