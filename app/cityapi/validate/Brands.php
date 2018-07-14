<?php

namespace app\cityapi\validate;


class Brands extends BaseValidate
{
    protected $rule = [
        'applet_id' => 'require|isPositiveInteger',
        'bis_name' => 'require|isNotEmpty',
        'name' => 'require|isNotEmpty',
        'phone' => 'require|isMobile',
        'user' => 'require|isNotEmpty',
        'pass' => 'require|isNotEmpty',
        'intro' => 'require|isNotEmpty',
        'address'  => 'require|isNotEmpty',
        'category_id' => 'require|isPositiveInteger',
    ];
}