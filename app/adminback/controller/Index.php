<?php
namespace app\admin\controller;
use app\admin\common\Common;
use app\model\MList;
use think\Db;
use think\Controller;
use think\Session;

class Index extends Common
{
	public function index()
	{	
		
		
		$appid = Session::get('wide_applet_id');
		// $data['user_all_numb']=Db::table('user')->where('applet_id',$appid)->count();
		// //var_dump($appid);
		// //echo "</br>";
		// //var_dump($data);
		// $data['user_numb'] = Db::table('user')->where("applet_id = '{$appid}'")->whereTime('create_time', 'today')->count();
		//    if($data['user_all_numb'] == 0){
  //               $data['day']=0;
		//    }else{
		//    	   $data['day']=round(($data['user_numb']/$data['user_all_numb'])*100,2);
		//    }
  

  //     //  $data['day']=1;

  //       //预约总数
  //       $data['user_all_reservation']=Db::table('reservation')->where('applet_id',$appid)->count();
  //      //今天预约数
  //       $data['reservation_numb'] = Db::table('reservation')->where("applet_id = '{$appid}'")->whereTime('create_time', 'today')->count();
  //       if( $data['user_all_reservation'] ==0){
         
  //          $data['reservation_day']=0;
  //       }else{
  //       	  $data['reservation_day']=round(($data['reservation_numb']/$data['user_all_reservation'])*100,2);
  //      // $data['reservation_day']=0;
  //       }
    
		// $this->assign('list',$data);
		return $this->fetch();

	}
	public function shuju(){
			$appid = Session::get('wide_applet_id');
		// $app = model('applets')->where("applet_id = '{$appid}'")->find();
		// $this->assign('aaa',$app['brand_status']);
		// $this->tianqi();
		return $this->fetch();
	}

}
