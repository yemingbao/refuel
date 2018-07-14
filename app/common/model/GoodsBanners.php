<?php
namespace app\common\model;
use think\Model;

class GoodsBanners extends Model
{
	protected function getPictureAttr($image){
        
        $rootDir = '/think1';
        $temp = explode('/',$_SERVER['PHP_SELF']);
        $rootDir = '/'.$temp[1];

    	return  request()->domain() . $rootDir . $image;
   }
}