<?php
namespace app\cityapi\common;
use think\Controller;
use think\Request;
use think\Cache;
use think\Db;

class Common extends Controller
{
  // 图片存放文件夹
  protected  $img_files = '/public/images/';

 


  protected function checkUserToken()
  {
     $token = Request::instance()
            ->header('token');
     $identities = Cache::get($token);
        if (!$identities) {
            return false;
        }
      return json_decode($identities,true);
  }

  /**
  * 函数名: isAuditPass
  * 函数说明: 判断小程序是否审核通过
  * @param int appID
  * @return bool
  * author: knkp
  */
  protected function isAuditPass($applet_id)
  {
  	$res = Db::table('applets')->where('applet_id',$applet_id)->value('applet_status');

  	if($res == 0)
  	{
  		return false;
  	}
  	else
  	{
  		return true;
  	}

  }


  
    /**
   * 函数名：returnJson
   * 函数说明：返回json数据
   * @param int $code 错误码
   * @param string $message 错误信息
   * @param null $data 返回数据
   * author：liu_sd
   */
  protected function returnJson($code=403,$message='非法请求！',$data='')
  {
      if ($data && (is_array($data) || is_object($data))) $data = obj2array($data);
      $return_data = [
          'code'=>$code,
          'msg'=>$message,
          'data'=>$data
      ];
      exit(json($return_data)->send());
  }
}