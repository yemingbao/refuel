<?php
namespace app\admin\controller;
use app\admin\common\Common;
use app\admin\common\Excel;
use app\model\MList;
use think\Db;
use think\Controller;
use think\Session;
use think\Request;

class Label extends Common
{
	//查看
	public function index()
	{
        $name=input('get.cat_name');
        $label_name=input('get.label_name');
        $start=input('get.create_time1');
        $end=input('get.create_time2');

        $where=array();
        if(!empty($name)) $where['ca.name'] = array('like','%'.$name.'%');
         if(!empty($label_name)) $where['la.name'] = array('like','%'.$label_name.'%');
        if(!empty($start)){
            $where['la.create_time'] = array('between',array($start,$end));
        }

        $appid = Session::get('wide_applet_id');
	    $list=Db::table("label")
            ->alias('la')
            ->join('cat ca','la.cat_id=ca.cat_id','left')
            ->field('la.label_id,la.name,la.create_time,la.update_time,ca.name as cat_name,ca.parent_id')
            ->where('ca.applet_id',$appid)
            ->where($where)
            ->paginate(8,false,['query' => request()->param(),]);
            // var_dump(db('label')->getLastSql());exit;
	    $this->assign('list',$list);
        $page = $list->render();
        $this->assign('page', $page);
		return $this->fetch();

	}


    public function add(){
        $appid = Session::get('wide_applet_id');
        $cat_list=Db::table('cat')
        ->field('cat_id,name')
        ->where('parent_id',null)
        ->where('applet_id',$appid)
        ->select();
        if(request()->isPost()){
    	// $date['cat_id']=input('post.cat_id');
         if(!empty($one_cat_id)){
                 $two_cat_id=input('post.two_cat_id');
                 if(!empty($two_cat_id)){
                     $date['cat_id']=$two_cat_id;
                 }else{
                     $date['cat_id']=$one_cat_id;
                 }
             }else{
                  $date['cat_id']=$one_cat_id;
             }
         $date['name']=input('post.name');
         $date['create_time']=time();
         $check=Db::table('label')->insert($date);
         if($check){
                   
                   echo "<script>alert('数据添加成功');location.href='".url('index')."'</script>";
         }else{
                   echo "<script>alert('数据添加失败');location.href='".url('add')."'/script>";
         }
        
    	}
        $this->assign('base_dir',$this->basedir);
        $this->assign('cat_list', $cat_list);
    	return $this->fetch();
    }



    public  function edit(){
    	$id=input('id/d');
        $list=Db::table("label")
            ->alias('la')
            ->join('cat ca','la.cat_id=ca.cat_id','left')
            ->field('la.label_id,la.name,la.create_time,la.update_time,ca.cat_id,ca.name as cat_name,ca.parent_id')
            ->where('la.label_id',$id)
            ->find();

         if(empty($list['parent_id'])){
           $list['one_cat_id']=$list['cat_id'];
           $list['one_cat_name']=$list['cat_name'];
           $list['two_cat_id']="";
           $list['two_cat_name']="";
         }else{
           $check_one_cat=Db::table('cat')->field('cat_id,name')->where('cat_id',$list['parent_id'])->find();
           $list['one_cat_id']=$check_one_cat['cat_id'];
           $list['one_cat_name']=$check_one_cat['name'];
           $list['two_cat_id']=$list['cat_id'];
           $list['two_cat_name']=$list['cat_name'];
         }   
        $appid = Session::get('wide_applet_id');
        $cat_list=Db::table('cat')
        ->field('cat_id,name')
        ->where('parent_id',null)
        ->where('applet_id',$appid)
        ->select();
         if(request()->isPost()){
             $ids=input('post.id/d');
           $one_cat_id=input('post.one_cat_id');
             if(!empty($one_cat_id)){
                 $two_cat_id=input('post.two_cat_id');
                 if(!empty($two_cat_id)){
                     $date['cat_id']=$two_cat_id;
                 }else{
                     $date['cat_id']=$one_cat_id;
                 }
             }else{
                  $date['cat_id']=$one_cat_id;
             }
             $date['name']=input('post.name');
             $date['update_time']=time();
             $check=Db::table('label')->where('label_id',$ids)->update($date);
           	 if($check){
                 echo "<script>alert('数据修改成功');location.href='".url('index')."'</script>";
             }else{
                 echo "<script>alert('数据修改失败');location.href='".url('info/edit',array('id'=>$ids))."'</script>";
             }
         }
        $this->assign('base_dir',$this->basedir);
        $this->assign('list',$list);
        $this->assign('cat_list', $cat_list);
		return $this->fetch();    
    }



    public function del(){
    	$id=input('id/d');
    	$check_del=Db::table('label')->where('label_id',$id)->update(['is_deleted'=>1]);
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
             $data['is_deleted']="1";
            $uploads=Db::table('label')->where('label_id','in',$id)->update($data);
            if($uploads){
                echo 1;
            }else{
                echo 3;
            }
        }else{
            echo 2;
        }
    }

            /**
     * @Author    Navy
     * @DateTime  2018-01-18
     * @version   获取二级二类名
     * @return    根据cat_id和parent_id查出对应的二级分类
     */
    public  function getCatList(){
         $cat_id=input('post.one_cat_id');
         $two_cat_list=Db::table('cat')->field('cat_id,name')->where('parent_id',$cat_id)->select();
         return(['two_cat_list'=>$two_cat_list]);
    }
}
