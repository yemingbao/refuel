<?php
namespace app\common\model;
use think\Model;

class SpecValues extends Model
{
	public function name()
	{
		return $this->belongsTo('SpecNames','spec_name_id','spec_name_id')->field('spec_name');
	}
}