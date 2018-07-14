<?php
namespace app\admin\common;
use think\model\Tenant;
use think\Validate;
use think\Controller;
use think\Session;
use think\Request;
use think\Db;


class Common extends Controller
{
    protected $condition;
    protected $applet_id;
    protected $filter = 'applet_id,delete_time,update_time,create_time';

    function __construct()
    {
        $start = strpos(__DIR__, 'app');
        $absolute_path = substr(__DIR__, 0, $start);
        define('WEB_ROOT', $absolute_path);

        $temp = explode('/', $_SERVER['PHP_SELF']);
        $this->rootDir = '/' . $temp[1];
        if (empty(Session::get('wide_applet_id'))) {
            echo "<script>alert('请先登陆');location.href='" . url('login/login') . "'</script>";
        }

        $temp = explode('/',$_SERVER['PHP_SELF']);
       $rootDir = '/'.$temp[1];
       // $rootDir = '';
       // var_dump($rootDir);
        $this->basedir = $rootDir;
        $this->applet_id = Session::get('applet_id');
        $this->param = Request::instance()->param();
        // 所有数据操作必须带上，避免操作其他用户的数据
        $this->condition = array('applet_id' => $this->applet_id);
        parent::__construct();
    }

    /**
     * 函数名：getParam
     * 函数说明：获取参数
     * @param $value
     * @return null
     * author：liu_sd
     */
    protected function getParam($value)
    {
        if (isset($this->param[$value]))
        {
            return $this->param[$value];
        }
        else
        {
            return null;
        }
    }

    /**
     * 函数名：getParamEmptyTip
     * 函数说明：获取参数，非空提示
     * @param $value
     * @return null
     * author：liu_sd
     */
    protected function getParamEmptyTip($value)
    {
        if (isset($this->param[$value]) && !empty($this->param[$value]))
        {
            return $this->param[$value];
        }
        else
        {
            $this->returnJson('-2',$value.'不能为空','');
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


	