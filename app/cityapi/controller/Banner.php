<?php
namespace app\cityapi\controller;
use app\cityapi\common\Common;

class Banner extends Common
{

	public function get()
	{

		$applet_id = input('post.applet_id/d');
		$type = input('post.type/d');

		/*
		// 小程序在审核中的数据
		$app_pass = $this->isAuditPass($applet_id);

		if(!$app_pass)
		{
			return Data::banner();
		}
		*/
		$data = model('Banners')->getImage($applet_id,$type);

		if(empty($data))
		{
			// 轮播图为空
			return array('code' => 100, 'msg' => '数据为空');
		}

		return array('code' => 0, 'data' => $data);

	}
}