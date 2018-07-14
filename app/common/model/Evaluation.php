<?php
namespace app\common\model;
use think\Model;
use traits\model\SoftDelete;
class Evaluation extends Model
{
	use SoftDelete;
	protected $deleteTime = 'delete_time';
    // 设置数据表（不含前缀）
    // protected $name = 'swpimg';
    protected $autoWriteTimestamp = true;
	// protected $autoWriteTimestamp = 'timestamp';
	// 
}