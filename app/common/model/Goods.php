<?php
/*
修改时间2017/12/20@knkp
*/
namespace app\common\model;

/*
商品
 */

class Goods extends BaseModel
{
	protected $autoWriteTimestamp = true;
	
	public function banners()
	{
		$filter = $this->filter . ',goods_id';
		// 获取轮播图  => 下级
		return $this->hasMany('GoodsBanners')->field($filter,true);
	}

	public function spu()
	{
		// 获取一个单品  => 下级
		return $this->hasMany('GoodsSpu')->field('goods_id,goods_spu_id',true)->limit(1);
	}

	public function spus()
	{
		// 获取全部单品  => 下级
		$filter = $this->filter . ',goods_id';
		return $this->hasMany('GoodsSpu')->field($filter,true);
	}

	public function classs()
	{
		$filter = $this->filter . ',class_id,picture';
		// 获取分类  => 商品的上级
		return $this->belongsTo('Classs','class_id')->field($filter,true);
	}

	protected function getPictureAttr($image)
	{
        
        $rootDir = '/think1';
        $temp = explode('/',$_SERVER['PHP_SELF']);
        $rootDir = '/'.$temp[1];

    	return  request()->domain() . $rootDir . $image;
  }
}