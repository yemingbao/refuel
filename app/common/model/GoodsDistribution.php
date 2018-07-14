<?php
namespace app\common\model;
use think\Model;

class GoodsDistribution extends Model
{
    protected $table = 'shop_goods_distribution';
    protected $pk = 'id';
    protected $autoWriteTimestamp = 'datetime';

    /**
     * 函数名：setGoodsDistribution
     * 函数说明：设置商品分销比例及金额
     * @param int $goods_id 商品id
     * @param int $one_commission 一级分销比例
     * @param int $two_commission 二级分销比例
     * @param int $three_commission 三级分销比例
     * @param float $money 可分销金额
     * @return bool
     * author：liu_sd
     */
    public function setGoodsDistribution($goods_id,$one_commission,$two_commission,$three_commission,$money)
    {
        $data = array();
        $data['goods_id'] = $goods_id;
        $data['one_commission'] = $one_commission;
        $data['two_commission'] = $two_commission;
        $data['three_commission'] = $three_commission;
        $data['commission_money'] = $money;
        //$goods = self::get($goods_id);
        $goods = $this->where(['goods_id'=>$goods_id])->find();
        if ($goods)
        {
            unset($data['goods_id']);
            $data['update_time'] = date('Y-m-d H:i:s');
            $rs = $this->fetchSql(false)->where(['goods_id'=>$goods_id])->update($data);
        }
        else
        {
            $data['create_time'] = date('Y-m-d H:i:s');
            $rs = $this->fetchSql(false)->insert($data);
        }
        
        if ($rs!==false)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
   


}