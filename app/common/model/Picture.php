<?php
namespace app\common\model;
use think\Model;

/*
*
* 发布信息表
*/


class Picture extends BaseModel
{

	protected function getPictureAttr($image){
        
        $rootDir = '/think1';
        $temp = explode('/',$_SERVER['PHP_SELF']);
        $rootDir = '/'.$temp[1];

    	return  request()->domain() . $rootDir . $image;
   }

	/**
    *   函数名：myInset
    *   函数说明：插入图片
    *   @param Aarray
    *   @param 用户ID
    *   @return int
    */
	public function myInsert($data)
	{

		$this->data = ['picture' => $data['picture'], 'field_id' => $data['id'],'table_id' => $data['table_id']];

		$this->save();

		return (int)$this->picture_id;
	}
}