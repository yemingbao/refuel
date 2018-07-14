<?php
namespace app\common\model;


use think\Model;
use traits\model\SoftDelete;

class BaseModel extends Model
{
    // 软删除，设置后在查询时要特别注意whereOr
    // 使用whereOr会将设置了软删除的记录也查询出来
    // 可以对比下SQL语句，看看whereOr的SQL
    use SoftDelete;



    protected $autoWriteTimestamp = true;
    protected $deleteTime = 'delete_time';

    protected $filter = 'applet_id,delete_time,update_time,create_time';
    
    protected function  prefixImgUrl($value, $data){
        $finalUrl = $value;
        if($data['from'] == 1){
            $finalUrl = config('setting.img_path').$value;
        }
        return $finalUrl;
    }

    
}