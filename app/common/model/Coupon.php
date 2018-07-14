<?php
namespace app\common\model;
use think\Model;
use traits\model\SoftDelete;
class Coupon extends Model
{
	use SoftDelete;
	protected $deleteTime = 'delete_time';
	protected $autoWriteTimestamp = true;



	 public function getamountAttr($value)
   {
      return (int)$value;
   }


   public function getmoneyAttr($value)
   {
      return (int)$value;
   }
}
?>