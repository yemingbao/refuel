<?php
namespace app\cityapi\controller;
use app\cityapi\common\Common;

class Test extends Common
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
	public function test()
	{
		$type = input('post.type/d');
		$abc = <<<Eexa
		[
 {
        "image": "/public/images/test2/shop01.png",
        "name": "餐饮美食",
        "id": 1
      },
      {
        "image": "/public/images/test2/shop02.png",
        "name": "酒店住宿",
        "id": 2
      },
      {
        "image": "/public/images/test2/shop03.png",
        "name": "运动健身",
        "id": 3
      },
      {
        "image": "/public/images/test2/shop04.png",
        "name": "爱车",
        "id": 4
      },
      {
        "image": "/public/images/test2/shop05.png",
        "name": "休闲娱乐",
        "id": 5
      },
      {
        "image": "/public/images/test2/shop06.png",
        "name": "丽人/美发",
        "id": 6
      },
      {
        "image": "/public/images/test2/shop07.png",
        "name": "母婴亲子",
        "id": 7
      },
      {
        "image": "/public/images/test2/shop008.png",
        "name": "结婚",
        "id": 8
      },
      {
        "image": "/public/images/test2/shop009.png",
        "name": "广告传媒",
        "id": 9
      },
      {
        "image": "/public/images/test2/shop010.png",
        "name": "教育培训",
        "id": 10
      }
    ]
Eexa;
	
		$abc = preg_replace("/[\"][i][d][\"][:][\s][0-9]+/",'"type":1',$abc);


		$data = json_decode($abc);


		// foreach ($data as $key => $value) 
		// {
		// 	echo "
		// 	$key : $value->"image" , $value->"name" , $value->type [
		// 	";
		// 	foreach ($value->list as $k => $v) {
		// 		echo $v;
		// 		echo ',';
		// 	}
		// 	echo ']';
		// 	echo '<br />';

		// }
		
		
		$catdb = model('Cat');
		// exit;
// 		$goodsdb->data($goodsData);
// 			$goodsdb->isUpdate(false)->save();
// 			$goods_"id" = $goodsdb->goods_"id";

// 		$user = User::get(1);
// $user->"name" = 'thinkphp';
// // 显式指定当前操作为新增操作
// $user->isUpdate(false)->save();

		foreach ($data as $key => $value) {

			$list = array();
			$list = [
				'applet_id' => $this->applet_id,
				'name' => $value->name, 'picture' => $value->image, 'type' => $value->type];

			$catdb->data($list);
			$catdb->isUpdate(false)->save();
			var_dump($list);
			var_dump($catdb->cat_id);
			$list = NULL;

			if(isset($value->list))
			{
				foreach ($value->list as $k => $v) {
					// $list = array();
					$list[] = ['applet_id' => $this->applet_id,'name' => $v,'parent_id' => $catdb->cat_id];
				}
				// var_dump($list);
				$catdb->isUpdate(false)->saveAll($list);
				// exit;
				var_dump($list);
			}

		}


		// var_dump($data);
	}

	public function info()
	{

		$pdata = input('post.');
		$page = $pdata['page'];
		$cat_id = $pdata['cat_id'];
		

		// 根据分类获取不同表的信息
		$result = model('Cat')->getType($this->applet_id,$cat_id);

		if(isset($result['code']))
		{
			// 分类ID不存在 或者 和小程序ID不匹配
			return array('code' => 1000, 'msg' => '非法操作或数据为空');
		}


		$result = model($result)->getBrands($page,$cat_id);



		if(isset($result['code']))
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
}