<?php
namespace app\admin\controller;
use app\admin\common\Common;
use app\admin\common\Excel;
use app\model\MList;
use think\Db;
use think\Controller;
use think\Session;
use think\Request;

class Banners extends Common
{
	//查看
	public function index()
	{
      $appid = Session::get('wide_applet_id');
	    $banners_list=Db::table("banners")
            ->field('banner_id,picture,type')
            ->where('applet_id',$appid)
            ->where('delete_time',null)
           ->paginate(8,false,['query' => request()->param(),]);
           $banners_lists=array();
           foreach ($banners_list as $key => $value) {
            if($value['type']==1){
                 $type="首页";
            }else if($value['type']==2){
                  $type="商家页";
            }
              $banners_lists[]=array(
                       'banner_id'=>$value['banner_id'],
                       'picture'=> $value['picture'],
                       'type'=>$type,
              );
           }
      $this->assign('base_dir',$this->basedir);
	    $this->assign('arr',$banners_lists);
	    $page = $banners_list->render();
      $this->assign('page', $page);
		return $this->fetch();

	}


    public function add(){
        if(request()->isPost()){
    	   $date['applet_id']=Session::get('wide_applet_id');
         $date['type']=input('post.type');
         //$mail=Db::table('applets')
         $file = request()->file('file');
       
       // var_dump($rootDir);exit;
         if(!empty($file)){
             if($file){
	                  
                    $info = $file->move(ROOT_PATH . 'public' . DS . 'banner');
                    $date['picture']="/public/banner/" .$info->getSaveName();
		     }else{
	         	  echo "<script>alert('图片上传失败');</script>";
	         }
         }
        $check=Db::table('banners')->insert($date);
             if($check){
                   echo "<script>alert('数据添加成功');location.href='".url('index')."'</script>";
             }else{
                   echo "<script>alert('数据添加失败');</script>";
             }
        
    	}
    	return $this->fetch('banners/add');
    }



    public  function edit(){
    	$id=input('id/d');
        $banner_list=Db::table('banners')
                 ->field('banner_id,picture,type')
                 ->where('banner_id',$id)
                 ->find();
            
         if(!empty($banner_list['picture'])){
             $banner_list['picture']=$banner_list['picture'];
         }
         if($banner_list['type']==1){
             $banner_list['type_name']="首页";
         }else if($banner_list['type']==2){
             $banner_list['type_name']="商家页";
         }else{
             $banner_list['type_name']="首页";
         }
         if(request()->isPost()){
           	 $ids=input('post.banner_id');
  	         $date['type']=input('post.type');
              $date['update_time']=time();
  	         $file = request()->file('file');
  	         if(!empty($file)){
  	         	if($file){
  	         	      $info = $file->move(ROOT_PATH . 'public' . DS . 'banner');
                    $date['picture']="/public/banner/" .$info->getSaveName();
  		         }else{
  		             	echo "<script>alert('图片上传失败');location.href='".picture('edit')."'</script>";
  		         }
  	         }
  	         
             $check=Db::table('banners')->where('banner_id',$ids)->update($date);
           	 if($check){
                 echo "<script>alert('数据修改成功');location.href='".url('index')."'</script>";
             }else{
                 echo "<script>alert('数据修改失败');location.href='".url('banners/edit',array('id'=>$ids))."'</script>";
             }
         }
         $this->assign('base_dir',$this->basedir);
         $this->assign('banner',$banner_list);
		     return $this->fetch();    
    }


    public function del(){
      $id=input('id/d');
      $check_del=Db::table('banners')->where('banner_id',$id)->update(['delete_time'=>time()]);
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
            $uploads=Db::table('banners')->where('banner_id','in',$id)->update($date);
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
