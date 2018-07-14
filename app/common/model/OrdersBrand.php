<?php
namespace app\common\model;
use think\Model;
use traits\model\SoftDelete;
use app\common\model\CouponUser;
use app\common\model\GoodsSpu;
class OrdersBrand extends Model
{
	use SoftDelete;
    protected $name = 'orders';
	protected $deleteTime = 'delete_time';
    // 设置数据表（不含前缀）
    protected $autoWriteTimestamp = true;
	// protected $autoWriteTimestamp = 'timestamp';
	//

    /**
     * 函数名：getSortedCoupon
     * 函数说明：获取排好序的优惠券详情
     * @param $coupon_list
     * @param $user_id
     * @return array|false|\PDOStatement|string|\think\Collection
     * author：liu_sd
     */
    public function getSortedCoupon($coupon_list,$user_id)
    {
        $coupon_user = new CouponUser();
        if (!empty($coupon_list) && is_array($coupon_list))
        {
            $coupon_ids = implode(',',$coupon_list);
            $where = array();
            $where['cu.coupon_user_id'] = ['in',$coupon_ids];
            $where['cu.user_id'] = $user_id;
            $where['cu.status'] = 0;
            $coupon = $coupon_user->alias('cu')
                ->join('coupon c','cu.coupon_id=c.coupon_id','left')
                ->where($where)
                ->field('c.amount,c.money,c.brand_id,cu.coupon_user_id')->select();
            $coupon_data = array();
            foreach($coupon as $value)
            {
                if (isset($coupon_data[$value->brand_id]))
                {
                    return false;
                }
                $coupon_data[$value->brand_id] = $value->toArray();
            }
            return $coupon_data;
        }
        return array();
    }


    /**
     * 函数名：getSortedGoods
     * 函数说明：获取排好顺序的订单商品详情
     * @param $goods_list
     * @param $user_id
     * @return array
     * author：liu_sd
     */
    public function getSortedGoods($goods_list)
    {
        $goods_spu_model = new GoodsSpu();
        $sorted_goods_list = array();
        foreach ($goods_list as $key=>$value)
        {
            $goods_spu_id = $value['goods_spu_id'];
            $goods_number = $value['number'];
            $goods_data = $goods_spu_model
                ->alias('gs')
                ->join('goods g','gs.goods_id=g.goods_id','left')
                ->where(['goods_spu_id'=>$goods_spu_id])
                ->field('gs.*,g.brand_id')
                ->select();
            $goods_data['goods_number'] = $goods_number;
            $brand_id = $goods_data['brand_id']===null ? 0 : $goods_data['brand_id'];
            $sorted_goods_list[$brand_id][] = $goods_data;
        }
        return $sorted_goods_list;
    }
}