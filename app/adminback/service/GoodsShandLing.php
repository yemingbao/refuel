<?php
/*
date 2017/12/30
author yjp
处理商品数据业务 
 */
namespace app\admin\service;
use app\admin\common\Common;
use think\Db;

class GoodsShandLing {
    
    /**
     * @date 2017-12-30
     * @author yjp
     * @param string $spec_key
     * @return $spec_name 商品规格名
     * @return $spec_value 商品规格值
     */
    public function getGoodsSpecValue($spec_key=''){
        //获取规格名称
        $data = array();
        if(!empty($spec_key)){            
            $spec_id = explode('-', $spec_key);
            $spec_name = model('spec_names')->where(['spec_name_id'=>$spec_id[0]])->field('spec_name')->find();
            $data['spec_name'] = $spec_name['spec_name'];
            
            
            $spec_value = '';
            foreach ($spec_id as $v){
                $name = model('spec_values')->field('spec_value_id,spec_value')->where(['spec_value_id'=>$v])->find();	    //$this->dd($name);exit;
                if(empty($spec_value)){
                    $spec_value = $name['spec_value'];
                }
                $spec_value .= '|'.$name['spec_value'];
            }
            $data['spec_value'] =$spec_value;
            return $data;
             
        }
            return $data;

    }
    
    /**
     * @date 2017-12-31
     * @author yjp
     * @param string $spec_arr
     * @param $value 指定处理值
     * @return $spec_name 商品规格名
     */
    public function getGoodsSpecArrayValue($spec_arr=array(),$value){
        $data = array();
        if(!empty($spec_arr)){
            foreach ($spec_arr as $k=>$v){            
                $data[$k][$value] = $v[$value];
            }            
             return $data;
        }
        return $data;
    
    }
    
    
    /**
     * 去除二维数组重复值,返回新的值
     * @date 2017-12-31
     * @author yjp
     * @param $data 指定处理值
     * @return $spec_name 商品规格名
     */
    public function goodsSpecNameUnique($data){
        $str='';
        $spec_name =[];
        $arr= [];
        //降维
        foreach ($data as $k => $v){ 
            $arr[] = $v['spec_name'];
           
        }     
        //去重
        $unqiue = array_unique($arr);

        foreach ($unqiue as $a => $b){
            $name = explode(',', $b);
            $c['spec_name'] = $name[0];
            $spec_name[$a] = $c;
        }
        return $spec_name;
    }
    
    
    public function processTime($time=''){
        if(!empty($time)){
            switch ($time){
                case '00':  return 0;
                case '01':  return 1;
                case '02':  return 2;
                case '03':  return 3;
                case '04':  return 4;
                case '05':  return 5;
                case '06':  return 6;
                case '07':  return 7;
                case '08':  return 8;
                case '09':  return 9;
                default: return   $time;
            }
        }
    }
}