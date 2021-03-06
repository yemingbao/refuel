<?php 
namespace app\cityapi\service;
// use app\model\User;
use app\cityapi\service\Token;
use app\lib\exception\TokenException;
use app\lib\exception\WeChatException;
use think\cache;
use app\lib\enum\ScopeEnum;
use think\Exception;
use think\Model;
use app\model\Applets;

/**
* 
*/
class UserToken extends Token
{
    protected $code;
    protected $applet_id;
    protected $wxAppID;
    protected $wxAppSecret;
    protected $wxLoginUrl;

    function __construct($code,$applet_id)
    {

         $this->code  = $code;
         $this->applet_id = $applet_id;
         // 根据ID获取对应的appID和密钥
         $applet = model('Applets')->where('applet_id',$applet_id)->find();
         $this->wxAppID=$applet->wechat_id;
         $this->wxAppSecret=$applet->wechat_key;
         $this->wxLoginUrl=sprintf(config('wx.login_url'),
         $this->wxAppID,$this->wxAppSecret,$this->code);

    }

    /**
     * 登陆
     * 思路1：每次调用登录接口都去微信刷新一次session_key，生成新的Token，不删除久的Token
     * 思路2：检查Token有没有过期，没有过期则直接返回当前Token
     * 思路3：重新去微信刷新session_key并删除当前Token，返回新的Token
     */
    public function get($value)
    {
        $result = curl_get($this->wxLoginUrl);
        //$result接收到的微信返回的数据是字符串,不便于处理转换成数组
        // 注意json_decode的第一个参数true
        // 这将使字符串被转化为数组而非对象

        $wxResult = json_decode($result, true);
        //外部接口返回的数据存在失败的可能.
        //判断是否正常
        if (empty($wxResult)) {
            // 为什么以empty判断是否错误，这是根据微信返回
            // 规则摸索出来的
            // 这种情况通常是由于传入不合法的code
            throw new Exception('获取session_key及openID时异常，微信内部错误');
        }
        else {
            // 建议用明确的变量来表示是否成功
            // 微信服务器并不会将错误标记为400，无论成功还是失败都标记成200
            // 这样非常不好判断，只能使用errcode是否存在来判断
            $loginFail = array_key_exists('errcode', $wxResult);
            if ($loginFail) {
                $this->processLoginError($wxResult);
            }
            else {
                // 没有异常后调用方法发令牌
                return $this->grantToken($wxResult,$value);
            }
        }
    }

    // 处理微信登陆异常
    // 那些异常应该返回客户端，那些异常不应该返回客户端
    // 需要认真思考
    private function processLoginError($wxResult)
    {
        throw new WeChatException(
            [
                'msg' => $wxResult['errmsg'],
                'errorCode' => $wxResult['errcode']
            ]);
    }

    // 颁发令牌
    // 只要调用登陆就颁发新令牌
    // 但旧的令牌依然可以使用
    // 所以通常令牌的有效时间比较短
    // 目前微信的express_in时间是7200秒
    // 在不设置刷新令牌（refresh_token）的情况下
    // 只能延迟自有token的过期时间超过7200秒（目前还无法确定，在express_in时间到期后
    // 还能否进行微信支付
    // 没有刷新令牌会有一个问题，就是用户的操作有可能会被突然中断
    private function grantToken($wxResult,$value)
    {
        // 此处生成令牌使用的是TP5自带的令牌
        // 如果想要更加安全可以考虑自己生成更复杂的令牌
        // 比如使用JWT并加入盐，如果不加入盐有一定的几率伪造令牌
        //        $token = Request::instance()->token('token', 'md5');
        $openid = $wxResult['openid'];
        //检测此 open_id用户是否已存在数据库里
        //如果不存在新增用户
        $user = model('User')->getByOpenID($openid);
        if ($user)
        {
           $uid = $user->user_id;
        }
        else {
           // 以微信的openid作为用户标识,查询用户是否存在
           // 但在系统中的相关查询还是使用自己的uid
           $uid = $this->newUser($openid,$value);
        }
        $cachedValue = $this->prepareCachedValue($wxResult, $uid);
        $token = $this->saveToCache($cachedValue);
        return $token;
    }

    // 创建新用户
    private function newUser($openid,$value)
    {
        // 有可能会有异常，如果没有特别处理
        // 这里不需要try——catch
        // 全局异常处理会记录日志
        // 并且这样的异常属于服务器异常
        // 也不应该定义BaseException返回到客户端
        // 
        // 使用 TP5框架自带模型新增用户信息.
        $user = model('User')->create(
            [
                'openid' => $openid,
                'nickname' => $value['nickname'],
                'applet_id' => $this->applet_id,
                'avatar'  => $value['avatarUrl'],
                'city'    => $value['city'],
                'gender'  => $value['gender'],
                'country' => $value['country'],
                'province'=> $value['province']
            ]);
            $value['uid']=$user->user_id;
            $value['openid']=$openid;
            $value['applet_id']=$this->applet_id;
            
        // dump($value['code']);
        // exit;
        // $user=model('UserAddress')->allowField(true)->save($value);
        return $user->user_id;
    }

    // 写入缓存
    /**
     * $key:令牌
     * $value: wxResult , uid , scope
     * @param  [type] $wxResult [description]
     * @return [type]           [description]
     */
    private function saveToCache($wxResult)
    {
        $key = self::generateToken();
        $value = json_encode($wxResult);
        $expire_in = config('setting.token_expire_in');
        //使用 TP5自带的写入缓存模块,cache
        $result = cache($key, $value, $expire_in);

        if (!$result){
            throw new TokenException([
                'msg' => '服务器缓存异常',
                'errorCode' => 10005
            ]);
        }
        return $key;
    }

    private function prepareCachedValue($wxResult,$uid)
    {
        $cachedValue=$wxResult;
        $cachedValue['uid']=$uid;
        $cachedValue['scope']=16;
        return $cachedValue;
    }
    
}

 ?>