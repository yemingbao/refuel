<?php
namespace app\admin\common;
use think\Controller;
use think\Loader;
use think\move;
use think\File;

Loader::import('PHPExcel.Classes.PHPExcel', EXTEND_PATH, '.php');
Loader::import('PHPExcel.Classes.PHPExcel.IOFactory', EXTEND_PATH, '.php');
Loader::import('PHPExcel.Classes.PHPExcel.Reader.Excel5');
class Excel extends Controller
{


  /**
   * 导入Excel表单数据
   * @return [type] [description]
   */
  public function phpInserExcel()
  {
      $file = request()->file('excel');
      if($file){
          $info = $file->move('upload');
        if($info && $info->getPathname()){
              $exclePath = $info->getSaveName();//获取文件名
              $file_name = ROOT_PATH  . 'upload' . DS . $exclePath;   //上传文件的地址
              $objReader =\PHPExcel_IOFactory::createReader('Excel5');
              $obj_PHPExcel =$objReader->load($file_name, $encode = 'utf-8');  //加载文件内容,编码utf-8
              $excel_array=$obj_PHPExcel->getsheet(0)->toArray();
              //转换为数组格式
              array_shift($excel_array);  //删除第一个数组(标题);
              return $excel_array;
          } else {
              echo $file->getError();
          }
      }
  }

 

  /**
      * 导出数据为excel表格
      *@param $data    一个二维数组,结构如同从数据库查出来的数组
      *@param $title   excel的第一行标题,一个数组,如果为空则没有标题
      *@param $filename 下载的文件名
      *@examlpe 
      $stu = M ('User');
      $arr = $stu -> select();
      exportexcel($arr,array('id','账户','密码','昵称'),'文件名!');
  */
  public function phpOutExcel($data=array(),$title=array(),$filename='message'){
      header("Content-type:application/octet-stream");
      header("Accept-Ranges:bytes");
      header("Content-type:application/vnd.ms-excel");  
      header("Content-Disposition:attachment;filename=".$filename.".xls");
      header("Pragma: no-cache");
      header("Expires: 0");

      if (!empty($title)){

          foreach ($title as $k => $v) {
              $title[$k]=iconv("UTF-8", "GB2312",$v);
          }
          $title= implode("\t", $title);
          echo "$title\n";

      }else{
        foreach ($data as $val) {
            foreach ($val as $k => $v) {
             $key[$k]=iconv("UTF-8", "GB2312",$k);
          }
        }
        $key= implode("\t", $key);
        echo "$key\n";
      }
      if (!empty($data)){
        foreach($data as $key=>$val){
          foreach ($val as $ck => $cv) {
            $data[$key][$ck]=iconv("UTF-8", "GB2312", $cv);
          }
            $data[$key]=implode("\t", $data[$key]);
          }
         echo implode("\n",$data);
      }
  }






 }
?>