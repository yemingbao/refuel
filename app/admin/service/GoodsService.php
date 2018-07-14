<?php
/*
修改时间 2017/12/25@knkp
*/
namespace app\admin\service;
use app\admin\common\Common;
use think\Session;
use think\Db;



// 添加商品
// return array('error'=>64, 'msg' => "规格总数不能大于50");

class GoodsService
{
	// brandId 是标识商品的入驻商品 如果为NULL则是自营

	public function index($appletId,$brandId,$classId,$name,$details,$show,$picture,$banners)
	{
		
		$thead = input('post.thead/a');  // 规格名
		// $content = input('post.content0/a');   // 规格值
		$oldMoney = array_filter(input('post.oldMoney/a'));  // 原价
		$newMoney = array_filter(input('post.newMoney/a'));  // 现价
		$store = array_filter(input('post.store/a'));  // 库存
		

		$rows = 1;	// 规格数至少为1
		$spec_str = '';	// 单品规格
		$goodsdb = model('Goods');  // 商品表模型
		$goodsbannerdb = model('GoodsBanners'); // 商品轮播图
		$goodsSpudb = model('GoodsSpu'); // 单品表模型
		$specNamesdb = model('SpecNames');  // 规格名表模型
		$specValuedb = model('specValues'); // 规格值表模型

		for($i=0; $i < count($thead) - 3; $i++)
		{

			// 判断规格名是否存在
			$res = $specNamesdb->get(['spec_name' => $thead[$i] ]);

			// 存在
			if($res)
			{
				// 取出规格名ID
				$spec_name_id = $res->spec_name_id;
			}
			// 不存在
			else
			{
				// 插入规格名(颜色)
				$specNamesdb->data(['spec_name' => $thead[$i] ]);
				$specNamesdb->isUpdate(false)->save();
				// 返回规格名ID
				$spec_name_id = $specNamesdb->spec_name_id;
			}

			// 规格名ID
			// $spec_name_id;

			$valuename = 'content'.$i;  
			$variable = input("post.$valuename/a"); // 规格值数组(红色，黄色)
			$variable = array_unique($variable);		// 排除重名规格值
			$rows *= count($variable);							// 计算 SKU 总数

			if($rows > 50){		// SKU 总数不能大于50
				return array('error'=>64, 'msg' => "规格总数不能大于50");
			}

			foreach ($variable as $key => $value) 
			{
				// 判断规格名对应的规格值是否存在
				$res = $specValuedb->get(['spec_value' => $value, 'spec_name_id' => $spec_name_id]);

				// 存在
				if($res)
				{
					// 取出规格值ID
					$spec_value_id = $res->spec_value_id;
				}
				// 不存在
				else
				{
					// 插入规格值(红色)
					$specValuedb->data(['spec_value' => $value, 'spec_name_id' => $spec_name_id]);
					$specValuedb->isUpdate(false)->save();
					// 返回规格名ID
					$spec_value_id = $specValuedb->spec_value_id;
				}
			}

		}


		// 上面已去掉空值
		if((count($oldMoney) == count($newMoney)) && (count($newMoney) == count($store)) && (count($store) == $rows))
		{
			;
		}
		else
		{
			return array('error'=>100, 'msg' => "规格参数没填写完整");
		}

		
		// 启动事务
		Db::startTrans();
		try
		{

			// // 商品入库
			// ['name','require','商品名称不能为空'],
   //      	['class_id','require','分类不能为空'],
   //      	['details','require','商品详情不能为空']
			$goodsData = array('applet_id' => $appletId, 
				'brand_id' => $brandId, 'class_id' => $classId,
				'name' => $name, 'picture' => $picture, 'details' => $details, 'show' => $show, 'sales' => 0);


			$goodsdb->data($goodsData);
			$goodsdb->isUpdate(false)->save();
			$goods_id = $goodsdb->goods_id;


            //添加分销关系
            $dist_money = input('post.dist_money');
            $one = input('post.one');
            $two  = input('post.two');
            $three  = input('post.three');
            if (($one+$two+$three)!==100)
            {
                return array('error'=>165, 'msg' => "分销比例相加之后不能大于100");exit;
                throw new \think\Exception('分销比例相加之后不能大于100', 111);
            }
            $model =  new \app\Common\model\GoodsDistribution;
            $model->setGoodsDistribution($goods_id,$one,$two,$three,$dist_money);




			foreach ($banners as $value) {
				$list[] = ['goods_id' => $goods_id , 'picture'=>$value];
			}
			$goodsbannerdb->saveAll($list);

			// 商品SKU入库
			for($i=0; $i < $rows; $i++)  // rows SKU数
			{

				for($j=0; $j < count($thead) - 3; $j++)
				{
					$valuename = 'content'.$j;
					$variable = input("post.$valuename/a");
					$res = $specValuedb->get(['spec_value' => $variable[$i] ]);

					// 存在
					if($res)
					{
						// 取出规格值ID
						$spec_value_id = $res->spec_value_id;
						$spec_str .= $spec_value_id . '-';
					}
					
				}

				$spec_str = substr($spec_str,0,-1); // 每行的规格
				$goodsSpuData = array('goods_id' => $goods_id, 'original_price' => $oldMoney[$i], 'present_price' => $newMoney[$i], 'stock' => $store[$i],'brand_id' => $brandId,'spec_set' => $spec_str);

				$goodsSpudb->data($goodsSpuData);
				$goodsSpudb->isUpdate(false)->save();
				if($i == 0)
				{
					$goodsData = array('original_price' => $oldMoney[$i], 'present_price' => $newMoney[$i]);
					$goodsdb->save($goodsData,['goods_id' => $goods_id]);
				}
				$spec_str = ''; // 每行的规格
				

					
			}
		    Db::commit();  
		}
		catch (\Exception $e) 
		{
		    // 回滚事务
		    Db::rollback();
		    return array('error'=>165, 'msg' => "添加失败");
		}
		return "success";

	}

	public function del($appletId,$goodsId)
	{
		$g = model('Goods');
		$m = model('goods_spu');
		$res = $m->where('goods_id',$goodsId)->select();
		if($res)
		{
			foreach ($res as $k => $v) 
			{
				$spid = $v['goods_spu_id'];
				$m->destroy($spid);
			}	
		}
		model('GoodsUser')->destroy(function($query)use($goodsId){
			$query->where('goods_id',$goodsId);
		});
		$res = $g->destroy($goodsId);
		if($res)
		{
			return 'success';
		}
		else
		{
			return array('error'=>64, 'msg' => "规格总数不能大于50");
		}
	}


}
