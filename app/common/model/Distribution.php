<?php
namespace app\common\model;
use think\Model;

class Distribution extends Model
{
    protected $table = 'shop_distribution';
    protected $pk = 'distributor_id';
    protected $autoWriteTimestamp = 'datetime';

    /**
     * 函数名：checkAgent
     * 函数说明：检查某个用户是否已经是分销商
     * @param int $user_id 用户id
     * @return bool
     * author：liu_sd
     */
    public function isAgent($user_id)
    {

        $agent = $this
            ->where(array('user_id'=>$user_id,'status'=>2))
            ->find();
        if ($agent)
        {
            return $agent;
        }
        return false;
    }

    /**
     * 函数名：checkAgent
     * 函数说明：检查某个用户是否已经申请过成为分销商
     * @param int $user_id 用户id
     * @return bool
     * author：liu_sd
     */
    public function checkAgent($user_id)
    {
        $agent = $this
            ->where(array('user_id'=>$user_id))
            ->find();
        if ($agent)
        {
            return true;
        }
        return false;
    }

    /**
     * 函数名：getDitributorId
     * 函数说明：获取某个用户的分销商id
     * @param int $user_id 用户id
     * @return mixed
     * author：liu_sd
     */
    public function getDitributorId($user_id)
    {
        $ditributor_id = $this
            ->where(array('user_id'=>$user_id))
            ->value('distributor_id');
        return $ditributor_id;
    }


    /**
     * 函数名：ditributorStatus
     * 函数说明：检查某个分销商是否审核通过
     * @param $distributor_id
     * @return bool
     * author：liu_sd
     */
    public function ditributorStatus($distributor_id)
    {
        $status = $this->where(array('distributor_id'=>$distributor_id))->value('status');
        if($status==2)
        {
            return true;
        }
        return false;
    }

    /**
     * 函数名：getSubordinateList
     * 函数说明：获取某个或某些分销商的下一级分销商
     * @param int|string $distributor_id 分销商id或id列表(逗号分隔的字符串)
     * @param bool $is_in
     * @return false|\PDOStatement|string|\think\Collection
     * author：liu_sd
     */
    public function getSubordinateList($distributor_id,$is_in=false)
    {
        $where = array();
        if ($is_in)
        {
            $where['sd.parent_id'] = ['in',$distributor_id];
        }
        else
        {
            $where['sd.parent_id'] = $distributor_id;
        }
        $where['sd.is_deleted'] = 'N';
        $distributor_list = $this
            ->alias('sd')
            ->join('user u','sd.user_id=u.user_id','left')
            ->where($where)
            ->field('sd.distributor_id,u.nickname,u.avatar,sd.create_time')
            ->select();
        if (!empty($distributor_list) && is_array($distributor_list))
        {
            foreach ($distributor_list as &$value)
            {
                $value = $value->toArray();
                $value['subordinate_num'] = $this->countSubordinate($value['distributor_id']);
                $value['subordinate_order_num'] = $this->getFinishedOrderNum($value['distributor_id']);
            }
            unset($value);
        }
        return $distributor_list;
    }


    /**
     * 函数名：countSubordinate
     * 函数说明：统计某个分销商下线人数
     * @param int $distributor_id 分销商id
     * @return: int
     * author：liu_sd
     */
    public function countSubordinate($distributor_id)
    {
        $level_1_count = 0;
        $level_2_count = 0;
//        $level_3_count = 0;
        $level_1_list = $this->getSubordinateList($distributor_id);//一级下线
        if (!empty($level_1_list) && is_array($level_1_list))
        {
            $level_1_count = count($level_1_list);
            $level_1_ids = array();
            foreach($level_1_list as $k=>$v)
            {
                $level_1_ids[] = $v['distributor_id'];
            }
//            $level_1_ids = array_column($level_1_list,'distributor_id');
            $level_2_list = $this->getSubordinateList($level_1_ids,true);//二级下线
            if (!empty($level_2_list) && is_array($level_2_list))
            {
                $level_2_count = count($level_2_list);
//                $level_2_ids = implode(',',array_column($level_2_list,'distributor_id'));
//                $level_3_list = $this->getSubordinateList($level_2_ids,true);//三级下线
//                if (!empty($level_3_list) && is_array($level_3_list))
//                {
//                    $level_3_count = count($level_3_list);
//                }
            }
        }
//        $count = $level_1_count+$level_2_count+$level_3_count;
        $count = $level_1_count+$level_2_count;
        return $count;
    }

    /**
     * 函数名：getSuperior
     * 函数说明：获取某个分销商的直属上级分销商信息
     * @param int $distributor_id 分销商id
     * @return: array
     * author：liu_sd
     */
    public function getSuperior($distributor_id)
    {
        $parent = array();
        $distributor = $this->where(array('distributor_id'=>$distributor_id))->find();
        if ($distributor)
        {
            $parent_id = $distributor['parent_id'];
            if ($parent_id)
            {
                $parent = $this->where(array('distributor_id'=>$parent_id))->find();
            }
        }
        return $parent;
    }

    /**
     * 函数名：getThreeSuperior
     * 函数说明：获取某个分销商的两个上级分销商信息以及他自己的分销商信息
     * @param int $distributor_id 分销商id
     * @return array
     * author：liu_sd
     */
    public function getThreeSuperior($distributor_id)
    {
        $level_1_superior = array();
        $level_2_superior = array();
        $level_3_superior = array();
        $level_1_superior = self::get($distributor_id);
        $level_2_superior = $this->getSuperior($distributor_id);
        if ($level_2_superior)
        {
            $level_3_superior = $this->getSuperior($level_2_superior['distributor_id']);
//            if ($level_2_superior)
//            {
//                $level_3_superior = $this->getSuperior($level_2_superior['distributor_id']);
//            }
        }
        $return_data = array();
        $return_data[0] = $level_1_superior;
        $return_data[1] = $level_2_superior;
        $return_data[2] = $level_3_superior;
        return $return_data;
    }


    /**
     * 函数名：getFinishedOrderNum
     * 函数说明：获取某个分销商的已完成订单数量
     * @param $distributor_id
     * @return int|string
     * author：liu_sd
     */
    public function getFinishedOrderNum($distributor_id)
    {
        $user_id = $this->where(['distributor_id'=>$distributor_id])
            ->value('user_id');
        $order = new Orders();
        $commission_details = new CommissionDetail();
        $where = array();
        $where['o.status'] = 5;
        $where['scd.distributor_id'] = $distributor_id;
        $where['o.user_id'] = $user_id;
        $order_num = $commission_details->alias('scd')
            ->join('orders o','scd.order_id=o.order_id','left')
            ->where($where)
            ->count();

        return $order_num;
    }
}