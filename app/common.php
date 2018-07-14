<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

// 应用公共文件
/**
 * @param string $url get请求地址
 * @param int $httpCode 返回状态码
 * @param mixed
 */
use think\Db;

function curl_get($url,&$httpCode = 0){
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

    //不做证书校验，部署在linux环境下请改位true
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,10);
    $file_contents = curl_exec($ch);
    $httpCode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $file_contents;
}

function curl_post($url,array $params = array()){
    $data_string = json_encode($params);
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_HEADER,0);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,10);
    curl_setopt($ch,CURLOPT_POST,1);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$data_string);
    curl_setopt($ch,CURLOPT_HTTPHEADER,
        array(
            'Content-Type: application/json'
        )
    );
    $data = curl_exec($ch);
    curl_close($ch);
    return ($data);
}
/**
 * 设置时间
 * @param string $value [description]
 */
function setTimeData($value='')
{
  return date('Y-m-d', $value);
  return date('Y-m-d H:i:s', $value);
}
function curl_post_raw($url,$rawData){
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_HEADER,0);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,10);
    curl_setopt($ch,CURLOPT_POST,1);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$rawData);
    curl_setopt($ch,CURLOPT_HTTPHEADER,
        array(
            'Content-Type: text'
        )
    );
    $data = curl_exec($ch);
    curl_close($ch);
    return ($data);
}
/**
 * 得到文件扩展名
 * @param string $filename
 * @return string
 */
function getExt($filename){
    return strtolower(pathinfo($filename,PATHINFO_EXTENSION));
}
/**
 * 产生唯一字符串
 * @return string
 */

/**
 * 产生唯一字符串
 * @return string
 */
function getUniName(){
    return md5(uniqid(microtime(true),true));
}
/** 
* 取中文字符串 
* param $string 字符串
* param $start 起始位 
* param $length 长度
* param $charset 编码
* param $dot 附加字串 
*/ 
function msubstr($string, $start=0, $length=22, $dot = '', $charset = 'UTF-8'){ 
    $string = str_replace(array('&', '"', '<', '>', ' '), array('&', '"', '<', '>', ' '), $string); 
    if(strlen($string) <= $length){ 
        return $string; 
    } 
    if(strtolower($charset) == 'utf-8'){ 
        $n = $tn = $noc = 0; 
        while($n < strlen($string)){ 
            $t = ord($string[$n]); 
            if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)){ 
                $tn = 1; 
                $n++; 
            }elseif (194 <= $t && $t <= 223){ 
                $tn = 2; 
                $n += 2; 
            }elseif (224 <= $t && $t <= 239){ 
                $tn = 3; 
                $n += 3; 
            }elseif (240 <= $t && $t <= 247){ 
                $tn = 4; 
                $n += 4; 
            }elseif (248 <= $t && $t <= 251){ 
                $tn = 5; 
                $n += 5; 
            }elseif ($t == 252 || $t == 253){ 
                $tn = 6; 
                $n += 6; 
            } else { 
                $n++; 
            } 
            $noc++; 
            if($noc >= $length){ 
                break; 
            } 
        } 
        if($noc > $length){ 
            $n -= $tn; 
        } 
        $strcut = substr($string, 0, $n); 
    }else{ 
        for($i = 0; $i < $length; $i++) { 
            $strcut .= ord($string[$i]) > 127 ? $string[$i] . $string[++$i] : $string[$i]; 
        } 
    } 
    return $strcut . $dot; 
} 
function getRandChar($length){
    $str = null;
    $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
    $max = strlen($strPol)-1;

    for ($i=0;$i<$length;$i++){
        $str .= $strPol[rand(0,$max)];
    }

    return $str;
}

/**
 * SQL注入检查
 * @param  [type] $sql_str 传入的字符
 * @return [type]          bool
 * 
 */
function input_check($sql_str='') {
    return preg_match('/select|insert|and|or|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile/i', $sql_str);
}

function showMessage($msg='', $url='') {
  if (!$url) {
      $url=url('./'.request()->controller());
  }
    header('Content-Type:text/html; charset=utf-8');
        echo "<script>alert('$msg');window.location.href='$url';</script>";
    }
      /**
       * 检测是否为纯数字的数据 
       * @param  string  $intval    [description]
       * @param  integer $head      [description]
       * @param  integer $endnumber [description]
       * @return [type]             [description]
       */
 function check_number($intval='',$head=1,$endnumber=99999999)
  {
        if (is_numeric($intval) && is_int($intval + 0) && ($intval + 0) >= $head && ($intval + 0) <= $endnumber) {
                return false;
        }
        return true;
  }
    /**
     * 判断信息的状态
     * @param  [type] $status [description]
     * @return [type]         [description]
     */
    function status($status='')
    {
    if($status == 1) {
        $str = "<span class='label label-success radius'>展示</span>";
    }elseif($status ==0) {
        $str = "<span class='label label-danger radius'>不展示</span>";
    }else {
        $str = "<span class='label label-danger radius'>是妖怪,联系管理员</span>";
    }
    return $str;
    }

    /**
     * 判断信息的状态
     * @param  [type] $status [description]
     * @return [type]         [description]
     */
    function statusi($status='')
    {
    if($status == 1) {
        $str = "<span class='label label-success radius'>未支付</span>";
    }elseif($status ==2) {
        $str = "<span class='label label-danger radius'>已付款</span>";
    }elseif($status ==3) {
        $str = "<span class='label label-danger radius'>已发货</span>";
    }elseif($status ==4) {
        $str = "<span class='label label-danger radius'>付款/但没库存</span>";
    }elseif($status ==5) {
        $str = "<span class='label label-danger radius'>订单异常</span>";
    }elseif($status ==6) {
        $str = "<span class='label label-danger radius'>申请退款</span>";
    }else {
        $str = "<span class='label label-danger radius'>是妖怪,联系管理员</span>";
    }
    return $str;
    }


    /**
     * 检测性别 
     * @param  string $sex [description]
     * @return [type]      [description]
     */
    function sexIn($sex='')
    {
        if($sex == 6) {
            $str = "<span class='label label-success radius'>男</span>";
        }elseif($sex ==9) {
            $str = "<span class='label label-danger radius'>女</span>";
        }else {
            $str = "<span class='label label-danger radius'>是妖怪,联系管理员</span>";
        }
        return $str;
    }
/**
 * 合拼数组,合成字符串
 * @param  [type] $arr [description]
 * @return [type]      [description]
 */
    function arr2str ($arr)
      {
          foreach ($arr as $v)
          {
              $v = join(",",$v); //转换为用逗号连接的字符串
              $temp[] = $v;
          }
          $t="";
          foreach($temp as $v){
              // $t.="'".$v."'".";"; //写入数据库时加''单引号
              $t.=$v.";";//转换为用分号连接的字符串
          }
          $t=substr($t,0,-1);
          return $t;
      }
     
    /**
     * 显示红绿两色的按钮
     * @param [type] $ztsc [description]
     */
      function ViewFlagsc($ztsc){
        if($ztsc){
          echo 'true';
        }else{
          echo 'wrong';
        }
      }
          /**
     * 显示红绿两色的按钮
     * @param [type] $ztsc [description]
     */
      function ViewFlagscs($ztsc){
        if($ztsc==2||$ztsc==3){
          echo 'true';
        }else{
          echo 'wrong';
        }
      }

    /**
     * 函数名：obj2array
     * 函数说明：对象转数组
     * @param $data
     * @return array
     * @return:array
     * author：liu_sd
     */
    function obj2array($data)
    {
        if (!is_object($data) && !is_array($data))
        {
            throw new \Think\Exception('数据类型必须是array或者object');
        }
        if (is_object($data)) {
            if (method_exists($data, 'toArray')) {
                $data = $data->toArray();
            } else {
                $data = (array)$data;
            }
        }
        foreach ($data as $key => &$value)
        {
            if (is_array($value) || is_object($value)) $value = obj2array($value);
        }
        unset($value);
        return $data;
    }


        /**
     * 函数名：array2xml
     * 函数说明：数组转xml
     * @param $arr
     * @param int $level
     * @return mixed|string
     * author：liu_sd
     */
    function array2xml($arr, $level = 1)
    {
        $s = $level == 1 ? "<xml>" : '';
        foreach ($arr as $tagname => $value)
        {
            if (is_numeric($tagname))
            {
                $tagname = $value['TagName'];
                unset($value['TagName']);
            }
            if (!is_array($value))
            {
                $s .= "<{$tagname}>" . (!is_numeric($value) ? '<![CDATA[' : '') . $value . (!is_numeric($value) ? ']]>' : '') . "</{$tagname}>";
            }
            else
            {
                $s .= "<{$tagname}>" . array2xml($value, $level + 1) . "</{$tagname}>";
            }
        }
        $s = preg_replace("/([\x01-\x08\x0b-\x0c\x0e-\x1f])+/", ' ', $s);
        return $level == 1 ? $s . "</xml>" : $s;
    }

//
// function http_post($url,$rawData,$extras){
//     $ch = curl_init();
//     curl_setopt($ch,CURLOPT_URL,$url);
//     curl_setopt($ch,CURLOPT_HEADER,0);
//     curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
//     curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,10);
//     curl_setopt($ch,CURLOPT_POST,1);
//     curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,true);
//     curl_setopt($ch,CURLOPT_SSLCERT,$extras['cert']);
//     curl_setopt($ch,CURLOPT_SSLKEY,$extras['key']);
//     curl_setopt($ch,CURLOPT_CAINFO,$extras['root']);
//     curl_setopt($ch,CURLOPT_POSTFIELDS,$rawData);
//     curl_setopt($ch,CURLOPT_HTTPHEADER,
//         array(
//             'Content-Type: text'
//         )
//     );
//     $data = curl_exec($ch);
//     curl_close($ch);
//     return ($data);
// }

  function random($length, $numeric = FALSE)
  {
      $seed = base_convert(md5(microtime() . $_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
      $seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
      if ($numeric)
      {
          $hash = '';
      }
      else
      {
          $hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
          $length--;
      }
      $max = strlen($seed) - 1;
      for ($i = 0; $i < $length; $i++)
      {
          $hash .= $seed{mt_rand(0, $max)};
      }
      return $hash;
  }


  function post_https_xml($url, $xml)
  {
      $header[] = "Content-type: text/xml";      //定义content-type为xml,注意是数组
      $ch = curl_init ($url);
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
      curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
      curl_setopt($ch, CURLOPT_SSLCERTTYPE,"PEM");
      curl_setopt($ch, CURLOPT_SSLCERT,'./app/extra/newdream/apiclient_cert.pem');
      curl_setopt($ch, CURLOPT_SSLKEY,'./app/extra/newdream/apiclient_key.pem');
      curl_setopt($ch, CURLOPT_CAINFO,'./app/extra/newdream/rootca.pem');
      $response = curl_exec($ch);
      if(curl_errno($ch))
      {
          print_r(curl_error($ch));
      }
      curl_close($ch);
      return $response;
  }


