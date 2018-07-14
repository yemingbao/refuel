<?php
namespace app\admin\validate;

use think\Validate;

/**
* 
*/
class Three extends Validate
{
	
    // 验证规则
    protected $rule = [
        ['name','require'],
        ['title','require'],
        ['content_1','require'],
        ['content_2','require']
    ];
}