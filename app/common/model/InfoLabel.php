<?php
namespace app\common\model;
use think\Model;

/*
*
* 发布信息表
*/


class InfoLabel extends BaseModel
{
    protected $deleteTime = true;
    protected $autoWriteTimestamp = true;


    /**
    *   函数名：myInsert
    *   函数说明：插入信息标签
    *   @param array 标签ID数组
    *   @param $info_id 信息ID
    *   @return int
    */
    public function myInsert($data,$info_id)
    {

        foreach ($data as $value)
        {
            $list[] = array('label_id' => $value, 'info_id' => $info_id);
        }
        return $this->saveAll($list);
    }

}