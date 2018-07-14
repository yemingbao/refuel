<?php
namespace app\common\model;
use think\Model;

class Cat extends BaseModel
{

    protected function getPictureAttr($image){
        
        $rootDir = '/think1';
        $temp = explode('/',$_SERVER['PHP_SELF']);
        $rootDir = '/'.$temp[1];

        return  request()->domain() . $rootDir . $image;
    }

    /**
    * 函数名：getAllSub
    * 函数说明：获取包括子分类的所有分类
    * @param APPID
    * @param 类型 1 首页 | 2 商家页
    * @return array(array()) 多行数据集  
    */
    public function getAllSub($applet_id,$type)
    {
        // 过滤字段
        $this->filter .= ',type';
        $subfilter = $this->filter . ',parent_id,picture';

            
        $res = $this
        ->where(['applet_id' => $applet_id, 'type' => $type])
        ->field($this->filter,true)
        ->select();

        foreach ($res as $key => $value) {
            $value->cat_id;
            $sub = $this
            ->where(['parent_id' => $value->cat_id])
            ->field($subfilter,true)
            ->select();
            $value->sub = $sub;
        }

        return $res;
         
    }

    /**
    * 函数名：getSub
    * 函数说明：获取子分类
    * @param 父分类ID
    * @return Array  
    */
    public function getSub($cat_id)
    {
        // 过滤字段
        $this->filter .= ',type,parent_id,picture';
        $res = $this->where(['parent_id' => $cat_id])
            ->field($this->filter,true)
            ->select();
        return $res;
         
    }

    /**
    * 函数名：getRows
    * 函数说明：获取多行一级分类
    * @param APPID
    * @param 类型 1 首页 | 2 商家页
    * @return array 多行数据集  
    */
    public function getRows($applet_id,$type)
    {
        // 过滤字段
		$this->filter .= ',type';
        $subfilter = $this->filter . ',parent_id,picture';

           	
        $res = $this
        ->where(['applet_id' => $applet_id, 'type' => $type])
        ->field($this->filter,true)
        ->select();

        return $res;
         
    }



    /*
    *
    */
    public function getType($applet_id,$cat_id)
    {

        $cat = $this->where(['applet_id' => $applet_id, 'cat_id' => $cat_id])
        ->find();

        if($cat['parent_id'] != NULL)
        {
            $type = $this->where(['applet_id' => $applet_id, 'cat_id' => $cat['parent_id']])
        ->value('type');
        }
        else
        {
            $type = $this->where(['applet_id' => $applet_id, 'cat_id' => $cat_id])
        ->value('type');
        }


    	

        // 
        return $type;
    }

    public function getASub($id)
    {
        $cat_ids = model('cat')->where('parent_id',$id)->column('cat_id');

        if(!empty($cat_ids))
        {
            $ids = implode(',', $cat_ids);
            return $ids;
        }
        else
        {
            return false;
        }
    }
}

?>