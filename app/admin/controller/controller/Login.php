<?php
namespace app\admin\controller;

use app\model\MList;
use think\Db;
use think\Controller;
use think\Session;
use gtphpsdk\Geetestlib;
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
		return $this->fetch('admin');
	}	
	public function logindo(){
			$mail = input('post.mail');
			$data=input('post.');
			$geetest_seccode=explode("|",$data['geetest_seccode']);

			if (empty($data['geetest_seccode'])||$data['geetest_validate']!==$geetest_seccode[0]) {
					showMessage('验证码操作不正确！,请从新操作',url('login'));
					return;
			}
			//测试数据
			//$res = Db::query("select salt from applets where mail = '$mail'");
			$res = model('applets')->where("mail = '{$mail}'")->find();
			//$res = Db::query("select salt from applets where mail = '100@my.com'");
			//先查询邮箱拿取随机值
			//var_dump($res);exit;
			if(!empty($res)){
				$password = input('post.password');
				// 测试数据
				 $password .= $res['salt'];
				 $password = md5($password);
				 $res = Db::query("select applet_id,mail from applets where mail='{$mail}' and password='{$password}'");
				 //$res = model('applets')->where("mail = '{$mail} and password = '$password'")->find();
					 if(!empty($res)){
					 	Session::set('user',$res[0]['applet_id']);
					 	Session::set('applet_id',$res[0]['applet_id']);
					 	Session::set('name',$res[0]['mail']);
					 	echo "<script>alert('登陆成功');location.href='".url('index/index')."'</script>";
					 }else{
					 	echo "<script>alert('密码错误');location.href='".url('login')."'</script>";
					 
					 }
				 }else{
					echo "<script>alert('用户不存在');location.href='".url('login')."'</script>";
				}
	}
	/**
	 *滑图验证生成
	 */
		public function StartCaptchaServlet()
		{
			$it=input('get.t',0,'intval');
			// session_start();
			$static=session('admin_account','',config('appuserpath.app_userpath'));
			$data = array(
					"id" => "id", # 网站用户id
					"client_type" => "web", #web:电脑上的浏览器；h5:手机上的浏览器，包括移动应用内完全内置的web_view；native：通过原生SDK植入APP应用的方式
					"ip_address" => "127.0.0.1" # 请在此处传输用户请求验证时所携带的IP
				);

			$a=new Geetestlib;
			$status=$a->pre_process($data, 1);

			$static['gtserver'] = $status;
			$static['id'] = $data['id'];
			// dump($static);
			
			echo $a->get_response_str();

		}

	public function outlogin(){
		Session::delete('applet_id');
		Session::delete('name');
		Session::delete('user');
		echo "<script>alert('注销成功');location.href='".url('login')."'</script>";
	}
	public function editpass(){
		Session::delete('applet_id');
		Session::delete('name');
		Session::delete('user');
		echo "<script>alert('已注销,请重新登录');location.href='".url('login')."'</script>";
	}

}
