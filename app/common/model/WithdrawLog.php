<?php
namespace app\common\model;
use think\Model;
use app\common\model\OrdersGoods;
use app\common\model\Distribution;
use app\common\model\GoodsDistribution;
use think\Db;

class WithdrawLog extends Model
{
    protected $table = 'distribution_withdraw_log';
    protected $pk = 'id';
    protected $autoWriteTimestamp = 'datetime';

    /**
     * 函数名：weixinPay
     * 函数说明：后台佣金打款
     * @param $applet_id
     * @param $openid
     * @param $money
     * @return int|string
     * author：liu_sd
     */
    public function weixinPay($applet_id,$openid,$money)
    {
        $payment = db('applets')->where(['applet_id'=>$applet_id])->find();
        $certs = array('cert' => $payment['cert_file'], 'key' => $payment['key_file'], 'root' => $payment['root_file']);
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
        $pars = array();
        $pars['mch_appid'] = $payment['wechat_id'];
        $pars['mchid'] = $payment['mer_num'];
        $pars['nonce_str'] = random(32);
        $pars['partner_trade_no'] = $this->createNo();
        $pars['openid'] = $openid;
        $pars['check_name'] = 'NO_CHECK';
        $pars['amount'] = $money;
        $pars['desc'] = '现金提现';
        $pars['spbill_create_ip'] = gethostbyname($_SERVER['HTTP_HOST']);
        ksort($pars, SORT_STRING);
        $string1 = '';
        foreach ($pars as $k => $v )
        {
            $string1 .= $k . '=' . $v . '&';
        }
        $string1 .= 'key=' . $payment['mer_key'];
        $pars['sign'] = strtoupper(md5($string1));
        $xml = array2xml($pars);
        $extras = array();
        $errmsg = '未上传完整的微信支付证书，请到联系管理员上传!';

        if (empty($certs['cert']) || empty($certs['key']) || empty($certs['root']))
        {
            return json_encode(['code'=>-1,'message'=>$errmsg,'data'=>'']);
        }

        $certfile = $payment['cert'];
        $keyfile = $payment['key'];
        $rootfile = $payment['root'];

        $extras['cert'] = $certfile;
        $extras['key'] = $keyfile;
        $extras['root'] = $rootfile;

        $resp = http_post($url, $xml, $extras);
        $arr = json_decode(json_encode(simplexml_load_string($resp['content'], 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        if (($arr['return_code'] == 'SUCCESS') && ($arr['result_code'] == 'SUCCESS'))
        {
            return json_encode(['code'=>1,'message'=>'打款成功','data'=>'']);
        }
        if ($arr['return_msg'] == $arr['err_code_des'])
        {
            $error = $arr['return_msg'];
        }
        else
        {
            $error = $arr['return_msg'] . ' | ' . $arr['err_code_des'];
        }
        return json_encode(['code'=>-1,'message'=>$error,'data'=>'']);
    }


    /**
     * 函数名：delWithdrawLog
     * 函数说明：把某个订单的打款申请记录删除
     * @param $order_id
     * @return bool
     * author：liu_sd
     */
    public function delWithdrawLog($order_id)
    {
        $where = array();
        $where['order_id'] = $order_id;
        $res = $this->where($where)->update(['is_deleted'=>'Y']);
        if ($res===false)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

}