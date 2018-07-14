<?php
namespace app\admin\validate;

use think\Validate;

/**
* 
*/
class Banner extends Validate
{
	
    // 验证规则
    protected $rule = [
        ['name','require'],
        ['cy_name','require'],
        ['address','require'],
        ['linkman','require'],
        ['mobile','require'],
        ['mobile','number'],
        ['submitted','require'],
    ];
}