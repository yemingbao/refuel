<?php
namespace app\common\model;
use think\Model;

class Applets extends Model
{

	public function getAlipayPveKeyAttr($value)
  {
     return $value;
  }



  public function getName($applet_id)
  {
    return $this->where('applet_id',$applet_id)->field('name',false)->find();
  }



}