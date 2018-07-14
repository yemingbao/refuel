<?php
namespace app\common\model;


/*
 * 自营平台信息
 */ 



class Sellers extends BaseModel
{
	public function getInfo($applet_id)
	{
		return $this->where('applet_id',$applet_id)->value('notice');
	}
}