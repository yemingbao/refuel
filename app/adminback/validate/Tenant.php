<?php
namespace app\admin\validate;

use think\Validate;

/**
* 
*/
class Tenant extends Validate
{
	
    // 验证规则
    protected $rule = [
        ['email','require|email'],
        ['password','require'],
        ['appname','require'],
        ['appID','require'],
        ['secret','require'],
        ['introduce','require'],
        ['phone','require'],
        ['submitted','require'],
        ['aliplay_id','require'],
        ['public_key','require'],
        ['private_key','require']
    ];
}