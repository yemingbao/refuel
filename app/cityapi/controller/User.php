<?php
namespace app\cityapi\controller;
use app\cityapi\common\Common;

class User extends Common
{
	private $applet_id;
	private $user_id;

	function __construct()
	{
		// 根据token 获取对应的用户ID
        $res = $this->checkUserToken();
        $this->user_id = $res['uid'];
        // $this->user_id = 371;
        if (empty($this->user_id)) 
        {
            
            // 没有用户ID
            echo "非法操作";
            exit;

        }
	}


	public function userReleaseList()
	{
		$data = model('Info')->myGetList($this->user_id,2);

		if(empty($data))
        {
            $this->returnJson(100080,'数据为空');
        }

        $this->returnJson(0,'success',$data);
	}

}