<?php
namespace app\cityapi\controller;
use app\cityapi\common\Common;

class Applet extends Common
{
	public function get()
	{
		
		$applet_id = input('post.applet_id/d');

		$data = model('Applets')->getName($applet_id);

		if(empty($data))
		{
			// 小程序名称为空
			return array('code' => 10006, 'msg' => '小程序名称为空');
		}

		return array('code' => 0, 'data' => $data);
		
	}

}