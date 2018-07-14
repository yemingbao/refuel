<?php
namespace app\common\model;
use think\Model;


   /**
    *   函数名：
    *   函数说明：
    *   @param 
    *   @param 
    *   @return 
    */

class Brands extends BaseModel
{
    // 设置数据表（不含前缀）
    // protected $name = 'swpimg';
    protected function getPictureAttr($image){
        
        $rootDir = '/think1';
        $temp = explode('/',$_SERVER['PHP_SELF']);
        $rootDir = '/'.$temp[1];

        return  request()->domain() . $rootDir . $image;
    }
    protected function getLicenceAttr($image){
        
        $rootDir = '/think1';
        $temp = explode('/',$_SERVER['PHP_SELF']);
        $rootDir = '/'.$temp[1];

        return  request()->domain() . $rootDir . $image;
    }

    public function banners()
    {
        // $filter = $this->filter;
        // 获取轮播图  => 下级

        $this->filter .= ',sort,brand_id,brands_banners_id';
        return $this->hasMany('BrandsBanners','brand_id')
        ->order('sort asc')
        ->field($this->filter,true);

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

        $keyword = '%' . $keyword . '%';

        $this->filter .= ',user_id,user,password,licence,picture,create_time,salt,status';

        $list = $this
        ->where('name', 'like' , $keyword)
        ->where('status',1)
        ->where('applet_id',$applet_id)
        ->field($this->filter,true)->select();

        return $list;

    }

    /**
    *   函数名：getCatBrands
    *   函数说明：获取某分类下的商家
    *   @param 页数
    *   @param 分类ID
    *   @return Aarray
    */
    public function getCatBrands($page,$cat_id)
    {
        $start = ($page - 1) * 8;
        $count = $page * 8;

        $this->filter .= ',user_id,user,password,licence,create_time,salt,status';


        $condition = array('cat_id' => $cat_id);

        $ids = model('cat')->getASub($cat_id);

        if($ids != false)
        {
            $condition = "i.cat_id in (" . $ids . ")";
        }




        $list = $this
        ->where($condition)
        ->where('status',1)
        ->limit($start,$count)->field($this->filter,true)->select();

        return $list;
    }

    /**
    *   函数名：getBrands
    *   函数说明：返回商家信息集
    *   @param AppID 
    *   @param 页数
    *   @param 排序方式 1 为更新时间 2为点击量
    *   @return Array
    */
    public function getBrands($applet_id,$page='',$type = 1)
    {
        $this->filter .= ',user_id,user,password,licence,create_time,salt,status';

        if($type == 1)
        {
            $order = 'update_time desc';
        }
        else
        {
            $order = 'click_rate desc';
        }

        //页数为空是返回最新的10条记录  ==>(对应) 首页
        if($page == '')
        {
            $this->filter = 'brand_id,picture,name';
            $list = $this
            ->where(['applet_id' => $applet_id, 'status' => 1])
            ->order($order)
            ->limit(10)->field($this->filter,false)->select();

            return $list;
        }


        $start = ($page - 1) * 8;
        $count = $page * 8;

        
        $list = $this
        ->where(['applet_id' => $applet_id, 'status' => 1])
        ->order($order)
        ->limit($start,$count)->field($this->filter,true)->select();
        return $list;
    }

    /**
    *   函数名：getNearby
    *   函数说明：返回商家附近信息集
    *   @param AppID 
    *   @param 页数
    *   @return Array
    */
    public function getNearby($data,$page='')
    {

        $lon = $data['longitude'];
        $lat = $data['latitude'];
        $applet_id = $data['applet_id'];
        // $controller=request()->controller();//获得控制器名


        
        // $goodsdb = model($controller);
        
        $list = $this->query("SELECT
    mobile,name,picture,brand_id,bhours,click_rate,
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
    brands where `applet_id` = $applet_id AND `delete_time` IS NULL
    AND `status` = 1
ORDER BY
    juli ASC");

        // 单位： 米

        // return $list;
        $rootDir = '/think1';
        $temp = explode('/',$_SERVER['PHP_SELF']);
        $rootDir = '/'.$temp[1];


        foreach ($list as $key => $value) 
        {
            $list[$key]['picture'] = request()->domain() . $rootDir . $value['picture'];

        }
        return $list;
       
    }
    

    /**
    *   函数名：getBrandDetails
    *   函数说明：获取商家的详细信息
    *   @param 商家ID
    *   @return Obj
    */
    public function getBrandDetails($brand_id)
    {
        $this->filter .= ',user_id,user,password,licence,create_time,salt,status';

        $value = $this
        ->where(['brand_id' => $brand_id, 'status' => 1])
        ->field($this->filter,true)->find();


        if(empty($value))
        {
            return NULL;
        }

        $this->where(['brand_id' => $brand_id, 'status' => 1])->setInc('click_rate');

        $value->banners;

        return $value;
    }
}