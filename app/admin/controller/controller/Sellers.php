<?php
namespace app\admin\controller;
use app\admin\common\Common;
use app\admin\common\Excel;
use app\model\MList;
use think\Db;
use think\Controller;
use think\Session;
use think\Request;

class Sellers extends Common
{
	//查看
	public function index()
	{
        $name=input('get.name');
        $phone=input('get.phone');
        $start=input('get.create_time1');
        $end=input('get.create_time2');

        $where=array();
        if(!empty($name)) $where['name'] = array('like','%'.$name.'%');
         if(!empty($phone)) $where['use.phone'] = array('like','%'.$phone.'%');
        if(!empty($start)){
            $where['create_time'] = array('between',array($start,$end));
        }
        $appid = Session::get('wide_applet_id');
	    $list=Db::table("sellers")
            ->field('seller_id,name,phone,intro,address,b_hours,longitude,latitude,wechat,integral_rule,notice,create_time,update_time')
            ->where('applet_id',$appid)
            ->where($where)
            ->paginate(8,false,['query' => request()->param(),]);
	    $this->assign('list',$list);
        $page = $list->render();
        $this->assign('page', $page);
		return $this->fetch();

	}


    public function add(){
        if(request()->isPost()){
         $appid = Session::get('wide_applet_id');
         $date['applet_id']=$appid;
    	 $date['name']=input('post.name');
         $date['phone']=input('post.phone');
         $date['intro']=input('post.intro');
         $date['address']=input('post.address');
         $date['b_hours']=input('post.b_hours');
         $date['longitude']=input('post.longitude');
         $date['latitude']=input('post.latitude');
         $date['wechat']=input('post.wechat');
         $date['integral_rule']=input('post.integral_rule');
         $date['notice']=input('post.notice');
         $date['create_time']=time();
         $check=Db::table('sellers')->insert($date);
         if($check){
                   
                   echo "<script>alert('数据添加成功');location.href='".url('index')."'</script>";
         }else{
                   echo "<script>alert('数据添加失败');location.href='".url('add')."'/script>";
         }
        
    	}
    	return $this->fetch();
    }



    public  function edit(){
    	$id=input('id/d');
        $list=Db::table("sellers")
            ->field('seller_id,name,phone,intro,address,b_hours,longitude,latitude,wechat,integral_rule,notice,create_time,update_time')
            ->where('seller_id',$id)
            ->find();
         if(request()->isPost()){
             $ids=input('post.id/d');
             $date['name']=input('post.name');
             $date['phone']=input('post.phone');
             $date['intro']=input('post.intro');
             $date['address']=input('post.address');
             $date['b_hours']=input('post.b_hours');
             $date['longitude']=input('post.longitude');
             $date['latitude']=input('post.latitude');
             $date['wechat']=input('post.wechat');
             $date['integral_rule']=input('post.integral_rule');
             $date['notice']=input('post.notice');
             $date['update_time']=time();
             $check=Db::table('sellers')->where('seller_id',$ids)->update($date);
           	 if($check){
                 echo "<script>alert('数据修改成功');location.href='".url('index')."'</script>";
             }else{
                 echo "<script>alert('数据修改失败');location.href='".url('info/edit',array('id'=>$ids))."'</script>";
             }
         }
        $this->assign('list',$list);
		return $this->fetch();    
    }



    public function del(){
    	$id=input('id/d');
    	$check_del=Db::table('sellers')->where('seller_id',$id)->update(['delete_time'=>time()]);
    	if($check_del){
             echo "<script>alert('删除成功');location.href='".url('index')."'</script>";
    	}else{
             echo "<script>alert('删除失败);location.href='".url('index')."'</script>";
    	}
    }

     /**
     * 函数名：delAll
     * 函数说明：批量删除
     * @author Navy
     */
    public function delAll(){
        $id = input('post.id');
        if(!empty($id)){
             $data['delete_time']=time();
            $uploads=Db::table('sellers')->where('seller_id','in',$id)->update($data);
            if($uploads){
                echo 1;
            }else{
                echo 3;
            }
        }else{
            echo 2;
        }
    }
}
