<?php
namespace app\admin\validate;

use think\Validate;

/**
* 
*/
class Goods extends Validate
{
	
    // 验证规则
    protected $rule = [
        ['name','require','商品名称不能为空'],
        ['class_id','require','分类不能为空'],
        ['details','require','商品详情不能为空'],
        ['thead','require','规格不能为空'],
        // ['oldMoney[]','require','请填写完原价'],
        // ['newMoney[]','require','请填写完现价'],
        // ['store[]','require','请填写完库存'],
    ];

}