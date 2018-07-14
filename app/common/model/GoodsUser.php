<?php
namespace app\common\model;
use think\Model;

/*
*
* 收藏功能
*/


class GoodsUser extends Model
{
	// 获取单品
	public function goods()
	{
		return $this->belongsTo('Goods')->field('applet_id,show,details',true);
	}
}