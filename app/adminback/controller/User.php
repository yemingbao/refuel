<?php
namespace app\admin\controller;
use app\admin\common\Common;
use app\admin\common\Excel;
use app\model\MList;
use think\Db;
use think\Controller;
use think\Session;
use think\Request;

class User extends Common
{
	//查看
	public function index()
	{
        $user_name=input('get.nickname');
        $start=input('get.create_time1');
        $end=input('get.create_time2');

        $where=array();
        if(!empty($user_name)) $where['nickname'] = array('like','%'.$user_name.'%');
        if(!empty($start)){
            $where['create_time'] = array('between',array($start,$end));
        }
        $appid = Session::get('wide_applet_id');
	    $list=Db::table("user")
            ->field('user_id,nickname,avatar,extend,create_time,update_time,integral,city,gender,country,province')
            ->where('applet_id',$appid)
            ->where($where)
            ->paginate(8,false,['query' => request()->param(),]);
        $page = $list->render();
        $this->assign('page', $page);
	    $this->assign('list',$list);
		return $this->fetch();

	}


    public function add(){
        
    	return $this->fetch();
    }



    public  function edit(){
		return $this->fetch();    
    }



    public function del(){
    	$id=input('id/d');
    	$check_del=Db::table('user')->where('user_id',$id)->delete();
    	if($check_del){
             echo "<script>alert('删除成功');location.href='".url('index')."'</script>";
    	}else{
             echo "<script>alert('删除失败);location.href='".url('index')."'</script>";
    	}
    }

}
