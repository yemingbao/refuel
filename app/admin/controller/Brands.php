<?php
namespace app\admin\controller;
use app\admin\common\Common;
use app\admin\common\Excel;
use app\model\MList;
use think\Paginator;
use think\Db;
use think\Controller;
use think\Session;
class Brands extends Common
{
	public function index()
	{
		$name=input('get.name');
        $nickname=input('get.nickname');
        $cat_name=input('get.cat_name');
        $start=input('get.create_time1');
        $end=input('get.create_time2');

        $where=array();
        if(!empty($name)) $where['bra.name'] = array('like','%'.$name.'%');
        if(!empty($nickname)) $where['use.nickname'] = array('like','%'.$nickname.'%');
         if(!empty($cat_name)) $where['ca.name'] = array('like','%'.$cat_name.'%');
        if(!empty($start)){
            $where['bra.create_time'] = array('between',array($start,$end));
        }
        $appid = Session::get('wide_applet_id');
	      $list=Db::table("brands")
	        ->alias("bra")
	        ->join('user use','bra.user_id=use.user_id','left')
	        ->join('cat ca','bra.cat_id=ca.cat_id','left')
          ->field('bra.brand_id,use.nickname,ca.name as cat_name,bra.name,bra.picture,bra.licence,bra.linkman,bra.mobile,bra.bhours,bra.intro,bra.longitude,bra.latitude,bra.user,bra.password,bra.address,bra.status,bra.create_time,bra.update_time')
          ->where('bra.applet_id',$appid)
           ->where('bra.delete_time',null)
          ->where($where)
          ->paginate(8,false,['query' => request()->param(),]);
	      $this->assign('list',$list);
        $page = $list->render();
        $this->assign('page', $page);
        $this->assign('base_dir',$this->basedir);
		    return $this->fetch();

	}

     public  function edit(){
    	$id=input('id/d');
        $list=Db::table("brands")
	        ->alias("bra")
	        ->join('user use','bra.user_id=use.user_id','left')
	        ->join('cat ca','bra.cat_id=ca.cat_id','left')
            ->field('bra.brand_id,use.nickname,ca.name as cat_name,bra.name,bra.picture,bra.licence,bra.linkman,bra.mobile,bra.bhours,bra.intro,bra.longitude,bra.latitude,bra.user,bra.password,bra.address,bra.status,bra.create_time,bra.update_time')
            ->where('bra.brand_id',$id)
            ->find();
        $picture=Db::table('brands_banners')->field('picture')->where('brand_id',$id)->select();
        //var_dump($picture);
        $this->assign('picture',$picture);
        $this->assign('list',$list);
		return $this->fetch();    
    }


	public function is_status(){
		$id = input('id/d');
		$check_brands=Db::table('brands')->where('brand_id',$id)->field('brand_id,status')->find();
		if($check_brands['status'] == 1){
             echo "<script>alert('已同意入驻，不可重复操作');location.href='".url('index')."'</script>";
		}else {
			$data['status']=1;
			$arr = Db::table('brands')->where('brand_id',$id)->update($data);	
				if($arr){
				   echo "<script>alert('同意入驻成功');location.href='".url('index')."'</script>";
				}else{
					 echo "<script>alert('同意入驻失败');location.href='".url('index')."'</script>";
				}
		}
		
		
	}

	public function no_status(){
		$id = input('id/d');
		$check_brands=Db::table('brands')->where('brand_id',$id)->field('brand_id,status')->find();
		if($check_brands['status'] == 0){
             echo "<script>alert('已经是拒绝状态,不可重复拒绝');location.href='".url('index')."'</script>";
		}else {
			$data['status']=0;
			$arr = Db::table('brands')->where('brand_id',$id)->update($data);	
				if($arr){
				   echo "<script>alert('拒绝入驻成功');location.href='".url('index')."'</script>";
				}else{
					 echo "<script>alert('拒绝入驻失败');location.href='".url('index')."'</script>";
				}
		}

	}


    

	 public function del(){
      $id=input('id/d');
       $check_del=Db::table('brands')->where('brand_id',$id)->update(['delete_time'=>time()]);
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
            $date['delete_time']=time();
            $uploads=Db::table('brands')->where('brand_id','in',$id)->update($date);
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
