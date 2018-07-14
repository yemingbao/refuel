<?php
namespace app\common\model;
use think\Model;
use traits\model\SoftDelete;
use app\common\model\CouponUser;
use app\common\model\GoodsSpu;
use think\Db;
class Orders extends Model
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    // 设置数据表（不含前缀）
    // protected $name = 'swpimg';
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
            $where['c.start_time'] = ['<',time()];
            $where['c.end_time'] = ['>',time()];
            $where['c.delete_time'] = null;
            $coupon = $coupon_user
                ->alias('cu')
                ->join('coupon c','cu.coupon_id=c.coupon_id','left')
                ->where($where)
                ->field('c.amount,c.money,c.brand_id,cu.coupon_user_id')->select();
            if (empty($coupon) || count($coupon_list)!==count($coupon))
            {
                return false;
            }
            $coupon_data = array();
            foreach($coupon as $value)
            {
                if ($value->brand_id === null) $value->brand_id = 0;
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
    public function getSortedGoods($goods_list,$applet_id)
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
                ->where(['goods_spu_id'=>$goods_spu_id,'applet_id'=>$applet_id])
                ->field('gs.*,g.name,g.brand_id,g.picture')
                ->find();
            if ($goods_data)
            {
                $goods_data = $goods_data->toArray();
            }
            else
            {
                return false;
            }
            $goods_data['url'] = $goods_data['picture'];
            $goods_data['goods_number'] = $goods_number;
            $brand_id = $goods_data['brand_id']===null ? 0 : $goods_data['brand_id'];
            $sorted_goods_list[$brand_id][] = $goods_data;
        }
        return $sorted_goods_list;
    }


    /**
     * 函数名：getOrders
     * 函数说明：根据传入条件参数获取N个订单详情
     * @param $filter_data
     * @param $status = ''
     * @return array
     * author：knkp
     */
    public function getOrders($filter_data, $status = '')
    {
        if(empty($status))
        {
            $filter = NULL;
        }
        else
        {
            $filter = array('status' => $status);
        }
        

        // return $status;
        // $status = 1;




        $orderslist = $this
        ->where($filter_data)
        ->where($filter)
        ->field('user_id,delete_time',true)
        ->Order('update_time desc')
        ->select();

        return model('OrdersGoods')->getOrderGoods($orderslist);

        
    }



    /**
    * 函数名：statsOrder
    * 函数说明：根据传入条件参数统计订单各个状态的总数和总额
    * @param $filter_data
    * @return array
    * author: knkp
    */
    public function  statsOrder($filter_data)
    {

        // 统计订单
        $stats_order = $this
        ->where($filter_data)
        ->field('status,sum(true_amount) as sum_money,count(status) as count',false)
        ->group('status')
        ->Order('update_time desc')
        ->select();

        return $stats_order;
    }


   /**
   * 函数名：excludeParentOrder
   * 函数说明：根据传入条件参数排除父订单获取N个订单详情
   * @param $filed 
   * @param $id
   * @param $status = ''
   * @return array
   * author：knkp
   */
   /*
    public function excludeParentOrder($field,$id, $status = '')
    {


        if(empty($status))
        {
          $orderslist =  Db::query("select *  from orders where $field = ? and order_id NOT IN ( select parent_id  from orders where  applet_id = 1 and parent_id != 0 group by parent_id)",[$id]);
        }
        else
        {
          $orderslist =  Db::query("select *  from orders where $field = ? and status = ? and order_id NOT IN ( select parent_id  from orders where  applet_id = 1 and parent_id != 0 group by parent_id)",[$id,$status]);
        }

        var_dump($orderslist);
        return model('OrdersGoods')->getOrderGoods($orderslist);
    }
    */


    /*
    /**
    * 函数名：sumOrderMenoy
    * 函数说明：根据传入条件参数统计订单各个状态的付款总额
    * @param $filter_data
    * @return array
    * author: knkp
   
    public function  sumOrderMenoy($filter_data)
    {

        // 统计订单
        $sumMenoy = model('Orders')
        ->where($filter_data)
        ->field('status,sum(true_amount) as sum_money',false)
        ->group('status')
        ->Order('update_time desc')
        ->select();

        return $sumMenoy;
    }
    */




}