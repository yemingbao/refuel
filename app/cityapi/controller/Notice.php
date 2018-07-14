<?php
namespace app\cityapi\controller;
use app\cityapi\common\Common;

class Notice extends Common
{
	private $applet_id;

	function __construct()
	{
		$this->applet_id = input('post.applet_id');
	}


    /**
    * 函数名：get
    * 函数说明：根据传入applet_id,type返回公告信息
    * @param applet_id
    * @return array
    * author: knkp
    */
	public function get()
	{

		/*
		// 小程序在审核中的数据
		$app_pass = $this->isAuditPass($applet_id);

		if(!$app_pass)
		{
			return Data::banner();
		}
		*/

		// 
		$data = model('Sellers')->getInfo($this->applet_id);

		if(empty($data))
		{
			return array('code' => 0, 'data' => '非法操作数据为空');
		}

		return array('code' => 0, 'data' => $data);

	}

}