<?php
namespace app\cityapi\controller;
use app\cityapi\common\Common;

class Picture extends Common
{
	private $user_id;
	private $id;
	private $applet_id;

    function __construct()
    {

        // 根据token 获取对应的用户ID
        $res = $this->checkUserToken();

        $this->id = input('post.id');
        $this->applet_id = input('post.applet_id');
        $this->table_id = input('post.table_id');
        $this->file = request()->file('img');
        // $this->user_id = $res['uid'];


        $this->user_id = 234;
        if(empty($this->user_id)){
            
            // 没有用户ID
            echo "非法操作";
            exit;
            
        }
    }

    // 上传图片
	public function addPicture($table_id = '')
	{


		$applet_id = $this->applet_id;
		$data['id'] = $this->id;

		if(empty($table_id))
		{
			$data['table_id'] = $this->table_id;
		}
		else
		{
			// 其他类调用
			$data['table_id'] = $table_id;
		}
		
		$file = $this->file;

	    // 获取表单上传文件
	    

	    $name = model('applets')->where('applet_id',$applet_id)->value('mail');
	    $name .= '/';


		// 创建文件名
    	$filename = '/public/images/' . $name . uniqid() . '.jpg';

    	$data['picture'] = $filename;


    	$picture_id = model('Picture')->myInsert($data);

    	if($picture_id < 1)
    	{
    		return array('code' => 10083, 'msg' => '系统错误');
    	}

    	$info = $file->move(ROOT_PATH,$filename);

    	if($info)
    	{
    		return array('code' => 0, 'msg' => 'success');
    	}
    	else
    	{
    		return array('code' => 10084, 'msg' => '上传图片失败');
    	}
    	
	}

}