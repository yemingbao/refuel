<?php
namespace app\admin\validate;

use think\Validate;

/**
* 
*/
class Applet extends Validate
{
	
    // 验证规则
    protected $rule = [
        ['applet_name','require'],
        ['footer_title','require'],
        /*
        ['appID','require'],
        ['secret','require'],
        ['introduce','require'],
        ['phone','require'],
        ['aliplay_id','require'],
        ['private_key','require'],
        ['public_key','require'],
        */
        ['submitted','require'],
    ];
}