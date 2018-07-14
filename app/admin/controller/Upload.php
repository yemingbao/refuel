<?php
namespace app\admin\controller;
use app\admin\common\Common;
use app\model\MList;
use think\Db;
use think\Controller;
use think\Session;

class Upload extends Common
{
	public function imgUpload()
    {
        $path = isset($this->param['path']) ? $this->param['path'] : 'other';
        $files = request()->file('files');

        $data = array();
        foreach($files as $file){
            $info = $file->move(ROOT_PATH . 'public' . DS . 'images' . DS . $path);
            if($info){
                $data[] = '/public' . '/' . 'images' . '/' . $path . '/' . $info->getSaveName();
            }else{
                $error_msg = $file->getError();
                $this->returnJson(-1,$error_msg,'');
            }
        }

        $this->returnJson(1,'上传成功',$data);
    }


}
