<?php
namespace app\common\model;
use think\Model;
use traits\model\SoftDelete;

class Classs extends Model
{
	use SoftDelete;
	protected $deleteTime = 'delete_time';
    // 设置数据表（不含前缀）
    // protected $name = 'swpimg';
    protected function getPictureAttr($image){
        
        $rootDir = '/think1';
        $temp = explode('/',$_SERVER['PHP_SELF']);
        $rootDir = '/'.$temp[1];

    	return  request()->domain() . $rootDir . $image;
    }
}