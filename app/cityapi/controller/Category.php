<?php
namespace app\cityapi\controller;
use app\cityapi\common\Common;

class Category extends Common
{
	private $applet_id;

	function __construct()
	{
		$this->applet_id = input('post.applet_id');
	}


    /**
    * 函数名：get
    * 函数说明：根据传入applet_id,type返回分类
    * @param applet_id
    * @param type
    * @return array
    * author: knkp
    */
	public function get()
	{
		$type = input('post.type/d');

		/*
		// 小程序在审核中的数据
		$app_pass = $this->isAuditPass($applet_id);

		if(!$app_pass)
		{
			return Data::banner();
		}
		*/
		
		// 
		$data = model('Cat')->getRows($this->applet_id,$type);
		if(empty($data))
		{
			// 分类为空
			return array('code' => 100081, 'msg' => '非法操作或分类为空');
			
		}

		return array('code' => 0, 'data' => $data);

	}


	/*
	*
	*
	*/
	public function info()
	{

		$pdata = input('post.');
		$page = $pdata['page'];
		$id = $pdata['id'];
		

		// 根据分类获取不同表的信息
		$type = model('Cat')->getType($this->applet_id,$id);

		if($type  == 1)
		{
			$result = model('Info')->myGetList($id,3);
		}
		else if($type == 2)
		{
			$result = model('Brands')->getCatBrands($page,$id);
		}
		else
		{
			// 分类ID不存在 或者 和小程序ID不匹配
			return array('code' => 1000, 'msg' => '非法操作或数据为空');
		}

		



		if(empty($result))
		{
			// 商家为空
			return array('code' => 1001, 'msg' => '数据为空');
		}
		else
		{
			return array('code' => 0, 'data' => $result);
		}

		// 区别表

	}

	// 
	public function getAllSub()
	{
		$type = input('post.type/d');

		/*
		// 小程序在审核中的数据
		$app_pass = $this->isAuditPass($applet_id);

		if(!$app_pass)
		{
			return Data::banner();
		}
		*/

		// 
		$data = model('Cat')->getAllSub($this->applet_id,$type);
		if(empty($data))
		{
			// 分类为空
			return array('code' => 100081, 'msg' => '非法操作或分类为空');
			
		}

		return array('code' => 0, 'data' => $data);
	}

	public function getSub($id)
	{
		$data = model('Cat')->getSub($id);


        if(empty($data))
        {
            $this->returnJson(100080,'数据为空');
        }

        $this->returnJson(0,'success',$data);
	}




	
}