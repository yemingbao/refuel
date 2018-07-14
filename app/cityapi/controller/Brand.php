<?php
namespace app\cityapi\controller;

use app\cityapi\common\Common;
use think\Request;
use think\Db;

// use think\File;

/**
 *  商家入驻 
 */
class Brand extends Common
{
    private $user_id;

    private $applet_id;

    function __construct()
    {
        $this->applet_id = input('post.applet_id');
    }

    public function is_login()
    {
        // 根据token 获取对应的用户ID
        $res = $this->checkUserToken();
        $this->user_id = $res['uid'];
        // $this->user_id = 368;
        if (empty($this->user_id)) {
            
            // 没有用户ID
            echo "非法操作";
            exit;

        }
    }


   	// 判断是否入驻 是返回 真
    public function is_user($applet_id = '')
    {
    	$this->is_login();

    	if(empty($applet_id))
    		return false;

        $dbdata = array('applet_id' => $applet_id, 'user_id' => $this->user_id);
        
        // 判断用户是否属于applet_id 对应的小程序
        if(model('User')->get($dbdata) == false){
            echo "非法操作";
            exit;
        }


        $applet_status = model('applets')->where('applet_id',$applet_id)->value('applet_status');


        if($applet_status  == 0)
        {
            // 小程序审核还没通过，不展示下面的页面
            return 'N';
        }


        // if()





        
    	// 判断用户是否已入驻applet_id对应小程序
    	$res = model('Brands')->get($dbdata);
    	// config('default_return_type','html');

    	// return $res;

    	// config('defalult_return_type','html');

    	// $query = "SELECT brand_id FROM brands where applet_id = 1 and user_id = 200; "
    	// 
    	// return $res;
        // 已申请入驻
    	if($res){
            $dbdata['status'] = 1;
    		$res = model('Brands')->where($dbdata)->find();
            if($res)
            {
                return $res->brand_id;  // 审核通过
            }else
            {
                return 0;  // 审核未通过
            }
        }
        // 未申请入驻
    	else
    		return false; // 未申请入驻
    }




    // 首页
    public function index()
    {
        $applet_id = $this->applet_id;

        $data = model('Brands')->getBrands($applet_id,$page = '');

        if(empty($data))
        {
            return array('code' => 10081, 'msg' => '非法操作或数据为空');
        }

        return array('code' => 0, 'data' => $data);

    }

    // 详情
    public function details($id)
    {
        $data = model('Brands')->getBrandDetails($id);

        if(empty($data))
        {
            $this->returnJson(100080,'数据为空');
        }

        $this->returnJson(0,'success',$data);

    }

    // 搜索
    public function search($keyword,$applet_id)
    {
        if(!$keyword || !$applet_id)
        {
            $this->returnJson(100080,'关键字为空');
        }

        $data = model('Brands')->search($keyword,$applet_id);


        if(empty($data))
        {
            $this->returnJson(100080,'数据为空');
        }

        $this->returnJson(0,'success',$data);

    }

    public function brandHot()
    {

        $applet_id = $this->applet_id;
        $page = input('post.page');

        $data = model('Brands')->getBrands($applet_id, $page = 1, 2);

        if(empty($data))
        {
            return array('code' => 10081, 'msg' => '非法操作或数据为空');
        }

        return array('code' => 0, 'data' => $data);

    }


    // 
    public function brandNew()
    {

        $applet_id = $this->applet_id;
        $page = input('post.page');

        $data = model('Brands')->getBrands($applet_id, $page = 1);

        if(empty($data))
        {
            return array('code' => 10081, 'msg' => '非法操作或数据为空');
        }

        return array('code' => 0, 'data' => $data);

    }

    public function brandNearby()
    {


        $data['applet_id'] = $this->applet_id;
        $data['longitude'] = input('post.longitude');
        $data['latitude']  = input('post.latitude');

        $data = model('Brands')->getNearby($data,$page = '');

        if(empty($data))
        {
            return array('code' => 10081, 'msg' => '非法操作或数据为空');
        }

        return array('code' => 0, 'data' => $data);

    }


    public function add($applet_id = '')
    {
        $data = input('');
        if($this->is_user($applet_id)){
        	return false;
        }

        $result = $this->validate($data, 'Brands');
        if (true !== $result) {
            return $result;
            // 输出错误信息
        }

        // error
        // $applet_name = Applets::get($applet_id)->value('name');
        // error
        // 
        $applet_name = model('Applets')->get(function($query)use($applet_id){
            $query->where('applet_id',$applet_id)->field('name');
        })->name;
        
        
        $this->user_dir = $applet_name . '/';

        $picture = $this->img_files . $this->user_dir . $this->user_id . 'logo.jpg';
        $licence = $this->img_files . $this->user_dir . $this->user_id . 'licence.jpg';

        // logo 图片地址
        if (file_exists(ROOT_PATH . $picture)) {
            $salt = substr(uniqid(),10);
            $dbdata = array('applet_id' => $applet_id, 
            	'user_id' => $this->user_id, 
            	'cat_id' => input('category_id'), 
            	'name' => input('bis_name'), 
            	'picture' => $picture , 
            	'linkman' => input('name'), 
            	'mobile' => input('phone'), 
            	'intro' => input('intro'), 
            	'longitude' => input('address.longitude'),
            	'latitude' => input('address.latitude'),
                'address' => input('address.address'),
            	'user'  => input('user'),
                'salt'  =>  $salt,
                'status' => 0,
            	'password' => md5(input('pass').$salt)
            	);
        	// 营业执照 可选
        	if (file_exists(ROOT_PATH . $licence)) {
                $dbdata['licence'] = $licence;
            }

            $ab = model('Brands')->create($dbdata);
            if($ab){
            	return "success";
            }
            else{
            	return "系统错误";
            }
        } else {
            return '文件上传失败';
        }
    }

    // 上传图片处理
    public function image($applet_id = '')
    {

        $this->is_login();
        if ($file = request()->file('logo')) {
            $applet_name = model('Applets')->get(function($query)use($applet_id){
                $query->where('applet_id',$applet_id)->field('name');
            })->name;
            $this->user_dir = $applet_name . '/';
            $name = $this->img_files . $this->user_dir . $this->user_id . 'logo.jpg';

            $info = $file->move(ROOT_PATH, $name);

            if ($info) {
                return "success";
            } else {
                // 上传失败获取错误信息
                echo $file->getError();
            }

        }
        if ($file = request()->file('licence_logo')) {
            $applet_name = model('Applets')->get(function($query)use($applet_id){
            $query->where('applet_id',$applet_id)->field('name');
            })->name;
            $this->user_dir = $applet_name . '/';
            $name = $this->img_files . $this->user_dir . $this->user_id . 'licence.jpg';

            $info = $file->move(ROOT_PATH, $name);

            if ($info) {
                return "success";
            } else {
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }

    }
}