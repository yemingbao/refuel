<?php
namespace app\common\model;
use think\Model;
use traits\model\SoftDelete;



class GoodsSpike extends Model
{
    protected $table = 'goods_spike';
    protected $pk = 'id';
    protected $autoWriteTimestamp = null;
    protected $dateFormat = false;

    public function getIsUpdAttr($value)
    {
        $status = ['Y'=>'上架','N'=>'下架'];
        return $status[$value];
    }

    /**
     * 函数名：getSecKillGoods
     * 函数说明：获取秒杀商品列表
     * author：liu_sd
     */
	public function getSecKillGoods($applet_id,$key_word=null,$start_time=null,$end_time=null)
    {
        $where = array();
        $where['gse.applet_id'] = $applet_id;
        $where['gse.is_deleted'] = 'N';
        if ($key_word!==null)
        {
            $where['g.name'] = ['like',"%$key_word%"];
        }
        if ($start_time!==null && $end_time!==null)
        {
            $where['gse.create_time'] = ['between',[$start_time,$end_time]];
        }
  
//        $where['g.delete_time'] = null;
//        $where['gsu.delete_time'] = null;
        $seckill_goods = $this
            ->alias('gse')
            ->join('goods g','g.goods_id=gse.goods_id','left')
            ->join('goods_spu gsu','gse.goods_spu_id=gsu.goods_spu_id','left')
            ->where($where)
            ->field('gse.id,gse.status,gse.is_up,gse.create_time,gse.update_time,g.name,g.picture,gse.start_date,gse.start_time,time_range')
            ->paginate(8);
        return $seckill_goods;
    }


}