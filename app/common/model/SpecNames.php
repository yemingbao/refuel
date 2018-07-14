<?php
namespace app\common\model;
use think\Model;

class SpecNames extends Model
{
	public function values()
	{
		return $this->hasMany('SpecValues','spec_name_id')->field('spec_name_id',true);
	}
}