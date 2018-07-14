<?php
namespace app\common\model;
use think\Model;

class OrdersGoods extends Model
{


    protected function getUrlAttr($image){
        
        $rootDir = '/think1';
        $temp = explode('/',$_SERVER['PHP_SELF']);
        $rootDir = '/'.$temp[1];

        return  request()->domain() . $rootDir . $image;
    }

    /**
     * 函数名：insertOrderGoods
     * 函数说明：订单商品入库
     * @param $goods_data
     * @param $order_id
     * author：liu_sd
     */
    public function insertOrderGoods($goods_data,$order_id,$emptycart,$user_id)
    {

        $rootDir = '/think1';
        $temp = explode('/',$_SERVER['PHP_SELF']);
        $rootDir = '/'.$temp[1];

        foreach ($goods_data as $value)
        {
            // 单品规格值ID数组
            $spec_set = explode('-',model('GoodsSpu')->get($value['goods_spu_id'])->spec_set);
            // 单品规格完整字符串
            $spec_set_str = '';
            // 规格值模型
            $spec_value = model('SpecValues');
            // 循环单品规格值ID数组 拼接 单品规格完整字符串
            foreach ($spec_set as $id)
            {
                // 返回规格值ID的一条记录
                $temp = $spec_value->alias('sv')
                    ->join('spec_names sn','sv.spec_name_id=sn.spec_name_id','left')
                    ->where('sv.spec_value_id',$id)
                    ->field('spec_name,spec_value')
                    ->find();
                // 根据规格值ID返回名 并接 规格   例 颜色:红色
                $spec_set_str .= $temp->spec_name . ':';
                $spec_set_str .= $temp->spec_value .'; ';
            }
            $data = array();
            $data['goods_spu_id'] = $value['goods_spu_id'];
            $data['order_id'] = $order_id;
            $data['number'] = $value['goods_number'];
            $data['url'] = $value['url'];
            $data['name'] = $value['name'];
            $data['price'] = $value['present_price'];
            $data['spec_set'] = $spec_set_str;
            $this->insert($data);
            $goods_spu_model = new GoodsSpu();
            if ($emptycart) model('Cart')->where(['goods_spu_id'=>$value['goods_spu_id'], 'user_id'=>$user_id])->update(['number'=>0]);
        }

    }

    /**
     * 函数名: getOrderGoods
     * 函数说明：获取订单商品详情
     * @param $orderslist 订单列表
     * author: knkp
     */

    public function getOrderGoods($orderslist)
    {

        foreach ($orderslist as $key => $value) {


            // 使用Db::query 会出现奇怪问题,所以转换对象
            if(is_array($value))
            {
                $value = (object)$value;
            }

            $orderId = $value->order_id;

            $res = $this
            ->where('order_id',$orderId)
            ->field('order_id',true)
            ->select();


            foreach ($res as $k => $v) {
                $id = $v['goods_spu_id'];
                $id = model('GoodsSpu')->where('goods_spu_id',$id)->value('goods_id');
                $v['goods_id'] = $id;
            }
            // return $res[0]['goods_spu_id'];

            $create_time = $value->create_time;
            $update_time = $value->update_time;
            // 如果是时间戳则转换成日期
            if(is_int($create_time))
            {
                $create_time = date("Y-m-d H:i:s",$create_time);
            }

        
            if(!is_int($update_time))
            {
               $time = strtotime($update_time);;
            }
            else
            {
                $time = $update_time;
            }

            $seven_day = 7 * 24 * 60 * 60;

            $time = $time + $seven_day;

            if($time < time())
            {
                // 已超过7天退款时间
                $alrefund = 1;
            }
            else
            {
                // 没超过7天退款时间
                $alrefund = 0;
            }

            
            
            // 收货信息
            $orders[$key]['c_info'] = $value->consignee . ' ' . $value->mobile . ' ' . $value->province . $value->city . $value->district . $value->address;

            $orders[$key]['alrefund'] = $alrefund;
            $orders[$key]['spus'] = $res;
            $orders[$key]['ordersNo'] = $value->order_sn;
            $orders[$key]['create_time'] = $create_time;
            $orders[$key]['total_amount'] = $value->total_amount;
            $orders[$key]['true_amount'] = $value->true_amount;
            $orders[$key]['status'] = $value->status;

        }

        if(isset($orders)){
            return $orders;
        }else{
            return array('error'=>'001','msg'=>'没有订单');
        }
    }




}

?>