<?php
namespace app\common\model;
use think\Model;

class Cart extends BaseModel
{
	// 获取单品
	public function spu()
	{
		return $this->hasOne('GoodsSpu','goods_spu_id','goods_spu_id');
	}
}

?>