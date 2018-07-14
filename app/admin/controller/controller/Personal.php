<?php
namespace app\admin\controller;
use app\admin\common\Common;
use app\model\MList;
use think\Db;
use think\Controller;
use think\Session;
use think\Model;

class Personal extends Common
{
	public function index()
	{
		$appid = Session::get('wide_applet_id');
	    $list=Db::table('applets')->where('applet_id',$appid)->field('mail,applet_id,name,admin_phone,wechat_id,wechat_key,mer_num,mer_key,sign_integral')->find();
		$this->assign('arr',$list);
		return $this->fetch();
	}
	public function edit(){
	      if(request()->isPost()){
            $id=Session::get('wide_applet_id');
	      	$date['name']=input('post.name');
	      	$date['admin_phone']=input('post.admin_phone');
	      	$date['wechat_id']=input('post.wechat_id');
	      	$date['wechat_key']=input('post.wechat_key');
	      	$date['mer_num']=input('post.mer_num');
	      	$date['mer_key']=input('post.mer_key');
	      	$date['sign_integral']=input('post.sign_integral');
	      	$date['update_time']=time();
	        $check=Db::table('applets')->where('applet_id',$id)->update($date);
	        if($check){
				echo "<script>alert('修改成功');location.href='".url('index')."'</script>";		
			}else{
				echo "<script>alert('修改失败');location.href='".url('index')."'</script>";	
			}
	      }
		return $this->fetch();
	}
		public function editdo(){
			$applet_id = Session::get('wide_applet_id');
			$res = Db::query("select salt from applets where applet_id = '{$applet_id}'");
			$password = input('post.npass');
			$password .= $res[0]['salt'];
			$password = md5($password);
			$data['password']=$password;
			$res = model('applets')->where("applet_id={$applet_id}")->update($data);
			if($res){
				echo "<script>alert('修改成功');location.href='".url('login/editpass')."'</script>";		
			}else{
				echo "<script>alert('修改失败');location.href='".url('edit')."'</script>";	
			}
	}

		public function ajaxs(){
			$applet_id = Session::get('wide_applet_id');
			$res = Db::table("applets")->field('salt')->where('applet_id',$applet_id)->find();
			
			$password = input('post.pass');
			$salt = $res['salt'];
			$password = md5($password.$salt);
			$res = Db::table("applets")->field('applet_id')->where('applet_id',$applet_id)->where('password',$password)->find();
		
			if($res){
				echo 1;
			}else{
				echo 0;
			}
	}


	public function ajaxss(){
		 $id=input('post.id');
		 $sort=input('post.sort');
		 $applet_id=Session::get('wide_applet_idd');
		 $res = Db::table('banners')->where("sort",$sort)->find();
		if($res){
			echo 2;
		}else{
			$res = Db::execute("update banners set sort='{$sort}' where applet_id='{$applet_id}' and banner_id='{$id}'");
			if($res){
				echo 1;
			}else{
				echo 0;
			}
		}
	}


}
