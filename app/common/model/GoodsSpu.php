<?php
namespace app\common\model;
use think\Model;
use traits\model\SoftDelete;
use app\common\model\SpecValues;
/*
 * 单品 ==> 带规格的商品
 */ 


class GoodsSpu extends Model
{
	use SoftDelete;
	protected $deleteTime = 'delete_time';
	public function goods()
	{
		// 获取商品  => 单品的上级
		return $this->belongsTo('Goods');
	}


	/**
     * 函数名：checkStock
     * 函数说明：检查商品库存
     * @param $goods_spu_id
     * @param $number
     * @return bool
     * author：liu_sd
     */
	public function checkStock($goods_spu_id,$number)
    {
        $goods = self::get($goods_spu_id);
        if ($goods)
        {
            $stock = $goods['stock'];
            if ($number > $stock)
            {
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * 函数名：minusStock
     * 函数说明：商品减去库存
     * @param $goods_spu_id
     * @param $number
     * @return bool
     * author：liu_sd
     */
    public function minusStock($goods_spu_id,$number)
    {
        $goods = self::get($goods_spu_id);
        if ($goods)
        {
            $stock = $goods['stock'];
            $new_stock = $stock - $number;
            if ($new_stock < 0) return false;
            $rs = $this->where(['goods_spu_id'=>$goods_spu_id])->update(['stock'=>$new_stock]);
            if ($rs !== false)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        return false;
    }


    /**
     * 函数名：getGoodsSpu
     * 函数说明：获取商品规格列表
     * @param $goods_id
     * @return false|\PDOStatement|string|\think\Collection
     * author：liu_sd
     */
    public function getGoodsSpu($goods_id)
    {
        $spec_values = new SpecValues();
        $where = array();
        $where['goods_id'] = $goods_id;
        $goods_spu_list = $this->where($where)->select();
        if (is_array($goods_spu_list) && !empty($goods_spu_list))
        {
            foreach($goods_spu_list as &$each_list)
            {
                $spec_set = $each_list->spec_set;
                $each_list = $each_list->toArray();
                $spec_sets = explode('-',$spec_set);
                $spec_set_name_arr = $spec_values->where(['spec_value_id'=>['in',$spec_sets]])->column('spec_value_id,spec_value');
                $spec_set_name_data = '';
                if (is_array($spec_set_name_arr) && !empty($spec_set_name_arr))
                {
                    $spec_set_name_data = implode('-',$spec_set_name_arr);
                }
                $each_list['spec_set_name'] = $spec_set_name_data;
            }
            unset($each_list);
        }
        return $goods_spu_list;
    }

}