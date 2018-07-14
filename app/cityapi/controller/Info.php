<?php
namespace app\cityapi\controller;
use app\cityapi\common\Common;

class Info extends Common
{
	private $applet_id;
	private $user_id;

	function __construct()
	{
		$this->applet_id = input('post.applet_id');
	}

	public function is_login()
  	{
     	 // 根据token 获取对应的用户ID
     	$res = $this->checkUserToken();
     	$this->user_id = $res['uid'];
     	// $this->user_id = 200;
      	if (empty($this->user_id)) {
	          
	        // 没有用户ID
	        echo "非法操作";
	        exit;

      	}
  	}

	public function get()
	{
		$applet_id = $this->applet_id;
		$data = model('info')->myGetList($applet_id);


		if(empty($data))
		{
			return array('code' => 100088, 'msg' => '数据为空');
		}


		return array('code' => 0, 'data' => $data);

		/*
		// 小程序在审核中的数据
		$app_pass = $this->isAuditPass($applet_id);

		if(!$app_pass)
		{
			return Data::banner();
		}
		*/

		// 
		// return model('Cat')->getInfo($applet_id,$type);

	}

	public function getCatInfo($id)
	{
		$data = model('info')->myGetList($id,3);


		if(empty($data))
		{
			return array('code' => 100088, 'msg' => '数据为空');
		}


		return array('code' => 0, 'data' => $data);

		/*
		// 小程序在审核中的数据
		$app_pass = $this->isAuditPass($applet_id);

		if(!$app_pass)
		{
			return Data::banner();
		}
		*/

		// 
		// return model('Cat')->getInfo($applet_id,$type);

	}



    // 搜索
    public function search($keyword,$applet_id)
    {
        if(!$keyword || !$applet_id)
        {
            $this->returnJson(100080,'关键字为空');
        }

        $data = model('Info')->search($keyword,$applet_id);


        if(empty($data))
        {
            $this->returnJson(100080,'数据为空');
        }

        $this->returnJson(0,'success',$data);

    }

	public function details()
	{
		$info_id = input('post.id');
		$data = model('info')->details($info_id);

		if(empty($data))
		{
			return array('code' => 10089, 'msg' => '数据为空');
		}
		else
		{
			return array('code' => 0, 'data' => $data);
		}

	}

	public function add()
	{
		$label = input('post.label/a');
		$this->is_login();
		$data['user_id'] = $this->user_id;
		$info_id = input('post.id');
		$label = input('post.label/a');
		$data['applet_id'] = input('post.applet_id');
		$data['content'] = input('post.content');
		$data['mobile'] = input('post.mobile');
		$data['linkman'] = input('post.linkman');
    	$data['longitude'] = input('post.address.longitude');
    	$data['latitude'] = input('post.address.latitude');
        $data['address'] = input('post.address.address');


		$data = model('info')->myUpdate($data,$info_id);

		// 信息插入不成功
		if(empty($data))
		{
			return array('code' => 100085, 'msg' => 'fail');
		}

		// 是否有标签
		if(!empty($label) && is_array($label))
		{
			$data = model('InfoLabel')->myInsert($label,$info_id);
			// 标签插入不成功
			if(empty($data))
			{
				return array('code' => 100085, 'msg' => 'fail');
			}
		}

		return array('code' => 0, 'msg' => 'success');

	}

	public function getNearby()
	{

        $data['applet_id'] = $this->applet_id;
        $data['longitude'] = input('post.longitude');
        $data['latitude']  = input('post.latitude');

        $data = model('Info')->getNearby($data,$page = '');

        if(empty($data))
        {
            $this->returnJson(100080,'数据为空');
        }

        $this->returnJson(0,'success',$data);
	}



	public function preAdd()
	{


		$this->is_login();

		$applet_id = $this->applet_id;
		$cat_id = input('post.cat_id');
		$user_id = $this->user_id;

		$info_id = model('Info')->Pk($applet_id,$user_id,$cat_id);

		if($info_id < 1)
		{
			return array('code'=> 100083, 'msg' => '系统错误');
		}

		$data = model('Label')->getLabel($cat_id);


		return array('code' => 0, 'data' => $data, 'info_id' =>$info_id);
		
	}



}