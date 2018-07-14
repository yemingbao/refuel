<?php
namespace app\admin\validate;

use think\Validate;

/**
* 
*/
class TwoUpdate extends Validate
{
	
    // 验证规则
    protected $rule = [
        ['ch_name','require'],
    ];
}