<?php
namespace app\common\model;
use think\Model;

class CouponUser extends BaseModel
{
	public function coupon()
	{
		// 获取商品  => 单品的上级
		return $this->belongsTo('Coupon');
	}

	
    /**
     * 函数名：ineffectiveCoupon
     * 函数说明：核销优惠券
     * @param $coupon_user_ids
     * @param bool $is_array
     * @return bool
     * author：liu_sd
     */
	public function ineffectiveCoupon($coupon_user_ids,$is_array=false)
    {
        if ($is_array)
        {
            foreach($coupon_user_ids as $coupon_user_id)
            {
                $rs = $this->where(['coupon_user_id'=>$coupon_user_id])->update(['status'=>1]);
                if ($rs === false)
                {
                    return false;
                }
            }
            return true;
        }
        else
        {
            $rs = $this->where(['coupon_user_id'=>$coupon_user_ids])->update(['status'=>1]);
            if ($rs === false)
            {
                return false;
            }
            else
            {
                return true;
            }
        }
    }
}

?>