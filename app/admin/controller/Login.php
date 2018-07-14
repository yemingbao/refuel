<?php
namespace app\admin\controller;
use app\model\MList;
use think\Db;
use think\Controller;
use think\Session;
// use think\File;
/**
* 自定义导航栏
*/
class Login extends controller
{
	public function index()
	{
		return $this->fetch('login/admin');
	}
	public function login()
	{	
		return $this->fetch();
	}	


			//检测非 newdreamvip 下的登陆是否是允许的帐号
			// $getUrl=request()->domain();
			// $url=explode('//',$getUrl);
			// if ($url[1]!='newdreamvip.com'&&$url[1]!='www.newdreamvip.com') {
			// 		$requestName=[
			// 			'80010038'
			// 		];
			// 		if (!in_array($mail, $requestName)) {
			// 				showMessage('用户名不存在!',url('login/index'));
			// 				exit;
			// 		}
			// 	}


	public function check_login(){
		Session::set('applet_id',"");
		       $mail = input('post.mail');
				//测试数据
				$res = Db::query("select salt from applets where mail = '{$mail}'");
				//$res = Db::query("select salt from applets where mail = '100@my.com'");
				//先查询邮箱拿取随机值
				if(!empty($res)){
					$password = input('post.password');
					// 测试数据
					 $password .= $res[0]['salt'];
					 $password = md5($password);


					 $getUrl=request()->domain();
						$url=explode('//',$getUrl);
					if ($url[1]!='newdreamvip.com'&&$url[1]!='www.newdreamvip.com') {
						$requestName=[
							'80010038'
						];
						if (!in_array($mail, $requestName)) {
								showMessage('用户名不存在!',url('login/index'));
								exit;
						}
					
					}

					 $res = Db::query("select applet_id,name from applets where mail='{$mail}' and password='{$password}'");
					 if(!empty($res)){
					 	Session::set('user',$res[0]['applet_id']);
					 	Session::set('wide_applet_id',$res[0]['applet_id']);
					 	Session::set('costype',"cosmetology");
					 	Session::set('name',$res[0]['name']);
					 	echo "<script>alert('登陆成功');location.href='".url('index/index')."'</script>";
					 }else{
					 	echo "<script>alert('密码错误');location.href='".url('login')."'</script>";
					 
					 }
				
				}else{
					echo "<script>alert('邮箱错误');location.href='".url('login')."'</script>";
				}
	}


	public  function outlogin(){
		Session::delete('applet_id');
		Session::delete('name');
		Session::delete('user');
		echo "<script>alert('注销成功');location.href='".url('login')."'</script>";
	}
	public function action(){
	    Session::set('applet_id',"");
		$a = input('a');
		switch ($a) {
			case 'login':
				$mail = input('post.mail');
				//测试数据
				$res = Db::query("select salt from applets where mail = '{$mail}'");
				//$res = Db::query("select salt from applets where mail = '100@my.com'");
				//先查询邮箱拿取随机值
				if(!empty($res)){
					$password = input('post.password');
					// 测试数据
					 $password .= $res[0]['salt'];
					 $password = md5($password);
					 $res = Db::query("select applet_id,name from applets where mail='{$mail}' and password='{$password}'");
					 if(!empty($res)){
					 	Session::set('user',$res[0]['applet_id']);
					 	Session::set('wide_applet_id',$res[0]['applet_id']);
					 	Session::set('costype',"cosmetology");
					 	Session::set('name',$res[0]['name']);
					 	echo "<script>alert('登陆成功');location.href='".url('index/index')."'</script>";
					 }else{
					 	echo "<script>alert('密码错误');location.href='".url('login')."'</script>";
					 
					 }
				
				}else{
					echo "<script>alert('邮箱错误');location.href='".url('login')."'</script>";
				}
				break;
			case 'outlogin':
					Session::delete('applet_id');
					Session::delete('name');
					Session::delete('user');
					echo "<script>alert('注销成功');location.href='".url('login')."'</script>";
				break;
			default:
				# code...
				break;
		}	
	}
	public function editpass(){
		Session::delete('applet_id');
		Session::delete('name');
		Session::delete('user');
		echo "<script>alert('已注销,请重新登录');location.href='".url('login')."'</script>";
	}

}
