<?php
/*
修改时间2017/12/20@knkp
*/
namespace app\common\model;

/*
商品
 */

class Label extends BaseModel
{
	public function getLabel($cat_id)
	{
		$this->filter .= ',cat_id';
		$data = $this->where('cat_id',$cat_id)
		->field($this->filter,true)
		->select();

		return $data;
	}
}