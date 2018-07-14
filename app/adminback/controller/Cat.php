<?php
namespace app\admin\controller;
use app\admin\common\Common;
use app\admin\common\Excel;
use app\model\MList;
use think\Db;
use think\Controller;
use think\Session;
use think\Request;
use app\common\model\Cat as Mcat;

class Cat extends Common
{
	//查看
	public function index()
	{
      $appid = Session::get('wide_applet_id');
	    $cat_list=Db::table("cat")
            ->field('cat_id,name,picture,type,update_time,create_time')
            ->where('parent_id','NULL')
            ->where('applet_id',$appid)
            ->order('cat_id','desc')
           ->paginate(8,false,['query' => request()->param(),]);
      $cat_lists=array();
           foreach ($cat_list as $key => $value) {
            if($value['type']==1){
                 $type="首页";
            }else if($value['type']==2){
                  $type="商家页";
            }
              $cat_lists[]=array(
                       'cat_id'=>$value['cat_id'],
                       'name'=>$value['name'],
                       'picture'=> $value['picture'],
                       'type'=>$type,
              );
           }
	    $this->assign('arr',$cat_lists);
	    $page = $cat_list->render();
      $this->assign('page', $page);
      $this->assign('base_dir',$this->basedir);
		  return $this->fetch();

	}


    public function add(){
        if(request()->isPost()){
    	   $date['applet_id']=Session::get('wide_applet_id');
         $date['name']=input('post.name');
         $date['type']=input('post.type');
         $date['create_time']=time();
         $type['create_time']=time();
         $type_name=input('post.type_name/a');   
         $file = request()->file('file');
         if(!empty($file)){
             if($file){
                $info = $file->move(ROOT_PATH . 'public' . DS . 'cat');
                $date['picture']="/public/cat/" .$info->getSaveName();
      	     }else{
               	echo "<script>alert('图片上传失败');</script>";
             }
         }

            $check=Db::table('cat')->insert($date);
            $catId = Db::table('cat')->getLastInsID();
             if($check){
                   if(is_array($type_name)==true && !empty($type_name[0])){  
                      foreach ($type_name as $key) {
                            $type['applet_id']=Session::get('wide_applet_id');
                            $type['name']=$key;
                            $type['parent_id']=$catId;
                            $type['create_time']=time();
                            Db::table('cat')->insert($type);      
                      }
                    }
                   echo "<script>alert('数据添加成功');location.href='".url('index')."'</script>";
             }else{
                   echo "<script>alert('数据添加失败');</script>";
             }
        
    	}
    	return $this->fetch('cat/add');
    }



    public  function edit(){
    	  $id=input('id/d');
        $banner_list=Db::table('cat')
                 ->field('cat_id,picture,type,name')
                 ->where('cat_id',$id)
                 ->find();
        $type_name=Db::table('cat')
                ->where('parent_id',$id)
                ->select();

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
             	 $ids=input('post.id');

    	         $date['type']=input('post.type');
               $date['name']=input('post.name');
               $date['update_time']=time();
               
               $type_name=input('post.type_name/a'); 
               $type_name_id=input('post.type_name_id/a'); 
               $type_name_new=input('post.type_name_new/a');
    	         $file = request()->file('file');
    	         if(!empty($file)){
    	         	if($file){
    	         	      $info = $file->move(ROOT_PATH . 'public' . DS . 'cat');
                      $date['picture']="/public/cat/" .$info->getSaveName();
    		         }else{
    		             	echo "<script>alert('图片上传失败');location.href='".picture('edit')."'</script>";
    		         }
    	         }         
                 
                
               $check=Db::table('cat')->where('cat_id',$ids)->update($date);
             	 if($check){
                    for($i = 0 ; $i < count($type_name); $i++)
                   {
                    $cat=new Mcat();
                     $cat->save(['name' => $type_name[$i],'update_time'=>time()], ['cat_id' => $type_name_id[$i]]);
                   }

                   if(is_array($type_name_new)==true && !empty($type_name_new[0])){  
                      foreach ($type_name_new as $key) {
                            $type['applet_id']=Session::get('wide_applet_id');
                            $type['name']=$key;
                            $type['parent_id']=$ids;
                            $type['create_time']=time();
                            Db::table('cat')->insert($type);      
                      }
                    }
                echo "<script>alert('数据修改成功');location.href='".url('index')."'</script>";
               }else{
                   echo "<script>alert('数据修改失败');location.href='".url('cat/edit',array('id'=>$ids))."'</script>";
               }
         }
         $this->assign('banner',$banner_list);
         $this->assign('type_name',$type_name);
         $this->assign('base_dir',$this->basedir);
		     return $this->fetch();    
    }

    public function del(){
      $id=input('id/d');
      $check_del=Db::table('cat')->where('cat_id',$id)->delete();
      if($check_del){
             $cat_list=Db::table('cat')->where('cat_id',$id)->column('cat_id');
             $ids=implode(',',$cat_list);
             Db::table('cat')->where('cat_id','in',$ids)->delete();
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
            $uploads=Db::table('cat')->where('cat_id','in',$id)->delete();
            if($uploads){
                Db::table('cat')->where('parent_id','in',$id)->delete();
                echo 1;
            }else{
                echo 3;
            }
        }else{
            echo 2;
        }
    }
   
    /**
     * 函数名：子分类删除
     * 函数说明：批量删除
     * @author Navy
     */
    public function typedel(){
      $id=input('post.id');
     $check_del=Db::table('cat')->where('cat_id',$id)->delete();
      if($check_del){
             echo 1;
      }else{
             echo 2;
      }
    }

}
