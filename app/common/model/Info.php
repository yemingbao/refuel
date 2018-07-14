<?php
namespace app\common\model;
use think\Model;
use think\Db;

/*
*
* 发布信息表
*/


class Info extends BaseModel
{

	/**
    *   函数名：Pk
    *   函数说明：返回发布信息主键ID
    *   @param AppID
    *   @param 用户ID
    *	@param 分类ID
    *   @return int
    */
	public function Pk($applet_id,$user_id,$cat_id)
	{
		$this->data = ['applet_id' => $applet_id, 'user_id' => $user_id, 'cat_id' => $cat_id];

		$this->save();

		return (int)$this->info_id;
	}


    /**
    *   函数名：search
    *   函数说明：关键字搜索
    *   @param $applet_id
    *   @param $keyowrd 
    *   @return Obj
    */
    public function search($keyword,$applet_id)
    {

        $this->filter = 'info_id,avatar,longitude,latitude,address,
        content,linkman,mobile,name as cat_name,i.create_time,click_rate';
        
        $keyword = '%' . $keyword . '%';


        $list = $this
        // ->fetchSql()
        ->alias('i')
        ->join('cat c','i.cat_id = c.cat_id')
        ->join('user u','i.user_id = u.user_id')
        ->where('i.applet_id', $applet_id)
        ->where('content','like',$keyword)
        ->where('content IS NOT NULL')
        ->where('mobile IS NOT NULL')
        ->order('i.update_time desc')
        ->field($this->filter,false)
        ->select();

        foreach ($list as $key => $value) {
            // $value->avatar
            $value->picture;
            $label_ids = Db::table('info_label')->where('info_id',$value->info_id)->column('label_id');
            $label = array();
            for($i = 0; $i < count($label_ids); $i++)
            {
              $label[] = model('label')
              ->where('label_id',$label_ids[$i])->value('name');
            }

            // 标签组合
            $value->label = $label;
        }
        
        return $list;

    }

    public function details($id)
    {

        $filter = 'info_id,avatar,longitude,latitude,address,
        content,linkman,mobile,name as cat_name,i.create_time';

        $info = $this
        ->alias('i')
        ->join('cat c','i.cat_id = c.cat_id')
        ->join('user u','i.user_id = u.user_id')
        ->where('info_id',$id)->field($filter,false)
        ->find();

        $info->picture;


        $this->where(['info_id' => $id])->setInc('click_rate');


        $label_ids = Db::table('info_label')->where('info_id',$info->info_id)->column('label_id');
        $label = array();
        for($i = 0; $i < count($label_ids); $i++)
        {
          $label[] = model('label')
          ->where('label_id',$label_ids[$i])->value('name');
        }

        // 标签组合
        $info->label = $label;

        return $info;
    }


	/**
    *   函数名：myUpdate
    *   函数说明：插入发布信息
    *   @param data 更新数据
    *	@param $info_id 更新主键
    *   @return int
    */

    public function myUpdate($data,$info_id)
    {
    	return $this->allowField(true)->save($data,['info_id' => $info_id]);
    }

    /**
    *   函数名：myGetList
    *   函数说明：获取发布信息列表
    *   @param id 
    *   @param 1  为applet_id  | 2 为user_id | 3 为分类id
    *   @return Array
    */

    public function myGetList($id, $type = 1)
    {

        $this->filter = 'info_id,avatar,longitude,latitude,address,
        content,linkman,mobile,name as cat_name,i.create_time,click_rate';

        if($type == 1)
        {
           $condition = array('i.applet_id' => $id); 
        }
        else if($type == 2)
        {
            $condition = array('i.user_id' => $id);
        }
        else
        {
            $condition = array('i.cat_id' => $id);

            $ids = model('cat')->getASub($id);

            if($ids != false)
            {
                 $condition = "i.cat_id in (" . $ids . ")";
            }


        }
        
        


        $list = $this
        // ->fetchSql()
        // ->count()
        ->alias('i')
        ->join('cat c','i.cat_id = c.cat_id')
        ->join('user u','i.user_id = u.user_id')
        ->where($condition)
        ->where('content IS NOT NULL')
        ->where('mobile IS NOT NULL')
        ->order('i.update_time desc')
        ->field($this->filter,false)
        ->select();

        // return $list;

        foreach ($list as $key => $value) {
            // $value->avatar
            $value->picture;
            $label_ids = Db::table('info_label')->where('info_id',$value->info_id)->column('label_id');
            $label = array();
            for($i = 0; $i < count($label_ids); $i++)
            {
              $label[] = model('label')
              ->where('label_id',$label_ids[$i])->value('name');
            }

            // 标签组合
            $value->label = $label;
        }
        
        return $list;
    }


    /**
    *   函数名：getNearby
    *   函数说明：返回附近信息集
    *   @param AppID 
    *   @param 页数
    *   @return Array
    */
    public function getNearby($data,$page='')
    {

        $this->filter = 'info_id,avatar,longitude,latitude,address,
        content,linkman,mobile,name as cat_name,i.create_time,click_rate';

        $lon = $data['longitude'];
        $lat = $data['latitude'];
        $applet_id = $data['applet_id'];
        // $controller=request()->controller();//获得控制器名


        
        // $goodsdb = model($controller);
        
        $list = $this->query("SELECT
    *,
    ROUND(
        6378.138 * 2 * ASIN(
            SQRT(
                POW(
                    SIN(
                        (
                            $lat * PI() / 180 - latitude * PI() / 180
                        ) / 2
                    ),
                    2
                ) + COS($lat* PI() / 180) * COS(latitude * PI() / 180) * POW(
                    SIN(
                        (
                            $lon * PI() / 180 - longitude * PI() / 180
                        ) / 2
                    ),
                    2
                )
            )
        ) * 1000
    ) AS juli
FROM
    info where `applet_id` = $applet_id AND `delete_time` IS NULL
    
ORDER BY
    juli ASC");


        foreach ($list as $key => $value) {
            $list[$key]['avatar']  = model('user')->where('user_id',$list[$key]['user_id'])->value('avatar');
            $list[$key]['cat_name']  = model('cat')->where('cat_id',$list[$key]['cat_id'])->value('name');
            $list[$key]['picture']  = model('picture')
            ->where('field_id',$list[$key]['info_id'])
            ->where('table_id',1)
            ->field('picture',false)
            ->select();

            $label_ids = Db::table('info_label')->where('info_id',$list[$key]['info_id'])->column('label_id');

            $label = array();

            for($i = 0; $i < count($label_ids); $i++)
            {
              $label[] = model('label')
              ->where('label_id',$label_ids[$i])->value('name');
            }


            $list[$key]['label'] = $label;

            $list[$key]['create_time'] = date("Y-m-d H:i", $list[$key]['create_time']);

            unset($list[$key]['update_time']);
            unset($list[$key]['delete_time']);
            unset($list[$key]['user_id']);
        }

        // 单位： 米

        // // return $list;
        // $rootDir = '/think1';
        // $temp = explode('/',$_SERVER['PHP_SELF']);
        // $rootDir = '/'.$temp[1];


        // foreach ($list as $key => $value) 
        // {
        //     $list[$key]['picture'] = request()->domain() . $rootDir . $value['picture'];

        // }


        return $list;
       
    }


    // 获取关联图片
    public function picture()
    {
        $this->filter = 'picture';
        // $filter = $this->filter . ',goods_id';
        // 获取轮播图  => 下级
        return $this->hasMany('Picture','field_id','info_id')->where('table_id',1)->field($this->filter,false);
    }


}