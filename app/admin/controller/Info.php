<?php
namespace app\admin\controller;
use app\admin\common\Common;
use app\admin\common\Excel;
use app\model\MList;
use think\Db;
use think\Controller;
use think\Session;
use think\Request;

class Info extends Common
{
	//查看
	public function index()
	{
        $name=input('get.cat_name');
        $nickname=input('get.nickname');
        $start=input('get.create_time1');
        $end=input('get.create_time2');

        $where=array();
        if(!empty($name)) $where['ca.cat_name'] = array('like','%'.$name.'%');
         if(!empty($nickname)) $where['use.nickname'] = array('like','%'.$nickname.'%');
        if(!empty($start)){
            $where['in.create_time'] = array('between',array($start,$end));
        }
        $appid = Session::get('wide_applet_id');
	    $list=Db::table("info")
            ->alias('in')
            ->join('cat ca','in.cat_id=ca.cat_id','left')
            ->join('user use','in.user_id=use.user_id','left')
            ->field('in.info_id,ca.name,ca.parent_id,use.nickname,in.content,in.mobile,in.linkman,in.create_time')
            ->where('in.applet_id',$appid)
            ->where($where)
             ->where('in.delete_time',null)
            ->paginate(8,false,['query' => request()->param(),]);
	    $this->assign('list',$list);
        $page = $list->render();
        $this->assign('page', $page);
		return $this->fetch();

	}


    public function add(){
        $appid = Session::get('wide_applet_id');
        $user_list=Db::table('user')
        ->field('user_id,nickname')
        ->where('applet_id',$appid)
        ->select();
         $cat_list=Db::table('cat')
        ->field('cat_id,name')
        ->where('parent_id',null)
        ->where('applet_id',$appid)
        ->select();
        //var_dump($cat_list);
        if(request()->isPost()){
    	 $date['applet_id']=$appid;
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
    	 //$date['cat_id']=input('post.cat_id');
         $date['user_id']=input('post.user_id');
         $date['linkman']=input('post.linkman');
         $date['mobile']=input('post.mobile');
         $date['content']=input('post.content');
         $date['create_time']=time();
         $picture=$this->param['thumb_url'];
         $check=Db::table('info')->insert($date);
          $infoId = Db::table('info')->getLastInsID();
             if($check){
                   if(!empty($picture)){
                     foreach ($picture as $key => $value) {
                          $pic['field_id']=$infoId;
                          $pic['table_id']=1;
                          $pic['picture']=$value;
                          $pic['create_time']=time();
                          Db::table('picture')->insert($pic);
                     }
                   }
                   echo "<script>alert('数据添加成功');location.href='".url('index')."'</script>";
             }else{
                   echo "<script>alert('数据添加失败');location.href='".url('add')."'/script>";
             }
        
    	}
        $this->assign('base_dir',$this->basedir);
        $this->assign('cat_list', $cat_list);
        $this->assign('user_list', $user_list);
    	return $this->fetch();
    }

    public  function edit(){
    	$id=input('id/d');
        $list=Db::table("info")
            ->alias('in')
            ->join('cat ca','in.cat_id=ca.cat_id','left')
            ->join('user use','in.user_id=use.user_id','left')
            ->field('in.info_id,ca.cat_id,ca.name,ca.parent_id,use.user_id,use.nickname,in.content,in.mobile,in.linkman,in.create_time')
            ->where('in.info_id',$id)
            ->find();
         
         if(empty($list['parent_id'])){
           $list['one_cat_id']=$list['cat_id'];
           $list['one_cat_name']=$list['name'];
           $list['two_cat_id']="";
           $list['two_cat_name']="";
         }else{
           $check_one_cat=Db::table('cat')->field('cat_id,name')->where('cat_id',$list['parent_id'])->find();
           $list['one_cat_id']=$check_one_cat['cat_id'];
           $list['one_cat_name']=$check_one_cat['name'];
           $list['two_cat_id']=$list['cat_id'];
           $list['two_cat_name']=$list['name'];
         }
        // var_dump($list);exit;
        $picture_list=Db::table('picture')
                ->field('picture_id,picture')
                ->where('field_id',$id)
                ->select();
        $appid = Session::get('wide_applet_id');
        $user_list=Db::table('user')
        ->field('user_id,nickname')
        ->where('applet_id',$appid)
        ->select();
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
            
             //$date['cat_id']=input('post.cat_id');
             $date['user_id']=input('post.user_id');
             $date['linkman']=input('post.linkman');
             $date['mobile']=input('post.mobile');
             $date['content']=input('post.content');
             $date['update_time']=time();
             $picture=$this->param['thumb_url'];
             $check=Db::table('info')->where('info_id',$ids)->update($date);
           	 if($check){
                 $del_price=Db::table('picture')->where('field_id',$ids)->where('table_id',1)->delete();
                 if($del_price){
                     if(!empty($picture)){
                     foreach ($picture as $key => $value) {
                          $pic['field_id']=$ids;
                          $pic['table_id']=1;
                          $pic['picture']=$value;
                          $pic['create_time']=time();
                          $pic['update_time']=time();
                          Db::table('picture')->insert($pic);
                     }
                   }
                 }
                 echo "<script>alert('数据修改成功');location.href='".url('index')."'</script>";
             }else{
                 echo "<script>alert('数据修改失败');location.href='".url('info/edit',array('id'=>$ids))."'</script>";
             }
         }
        $this->assign('base_dir',$this->basedir);
        $this->assign('list',$list);
        $this->assign('picture_list', $picture_list);
        $this->assign('cat_list', $cat_list);
        $this->assign('user_list', $user_list);
		return $this->fetch();    
    }



    public function del(){
    	$id=input('id/d');
    	$check_del=Db::table('info')->where('info_id',$id)->update(['delete_time'=>time()]);
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
            $uploads=Db::table('info')->where('info_id','in',$id)->update($data);
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
