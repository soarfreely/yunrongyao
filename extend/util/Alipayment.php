<?php

namespace util;

use Yansongda\Pay\Pay;
use Yansongda\Pay\Log;
use Yansongda\Pay\Gateways\Alipay;

class Alipayment
{
    private $total_amount = '';
    private $auth_code = '';

    protected $config = [
        'app_id' => '2016080700186444',
        'notify_url' => 'http://yansongda.cn/notify.php',
        'return_url' => 'http://yansongda.cn/return.php',
        'ali_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAreCAYuHR5EiW5ANWQx0F84Th+hvYcaHEWCo298G/MciTivB1g3j4gFII5kPXDhY5EIMA0U6o/Ir0NeglglZ1lX1XPj9OyLfcDHEQtsmaGk4UEr5I1CCcMfM/fQAFZR0HOURoY2bW1VJP3nd7wvOFmcWUST93ecfqipowtVdHUkdrHvX4oKfh4AVGAeUi9K8j48zIoqz9lwjAgihaJlst9Ozztjj47/Zdh/aBm7tv/ISKT5AipkWqKl+UiWwa78fAXbsWDBnyPGh/bnkMUknlc5vULkBIcO0yhf8EwWOeMZ9oIr+6bYqKfbjcocrpbM9v4zdd9poxxLu5fld5wsQ0sQIDAQAB",
        // 加密方式： **RSA2**
        'private_key'=>"MIIEowIBAAKCAQEArhOJwQK2HAK44UXbsyaMZJCjDffk/jM7VI0oB6H6MDCMywMcDjCEonhcgGL63wAFcn1/L4W1HKQ/sxUrTWa2GNpBOIdnNe+mkC9Vc6UxDBSoqVmrM/0fGW3Ubx89tRPZZXBnbVNHBXfz62CiFerZUf/YvFB8U0O5RW/tHSoKZJ5FtxUKB+zXbilyzDd+TsPpPD9wSldfn1vIIdI1ykQvaJxRCwhEH1SbuPJ5216nAOy9LH7d5tzA+Eo4daorL9207LPTla0ALcdV/inxtPD9TX/GZ9wPqq1UZhySqX+CgaqajJNdd+NdD+7POPC6ikz2rG+KAnO3bVw8T0defJU1TwIDAQABAoIBAGVOQZDt/wV2IBoaCa3M9U9hTUNHzSCSuCiQrYcjoPKCjq1m9eDcI5RZ75tX/x/gZ0sl9eq2KZytidSysSyuZbM48VPwmQQuoASxR02yCUU6kJ1d7eCnon2uCT9SMxs6nf4G2GorWN253V5SMoIG1sp91qhhPrlGZIAgjyG14a6D55rN8uCciWlczR+aIXfz/GwTptUO1rNuaONqY2+Q93PWfYC21A8vWiKhCOoF1V8uR4TI1f2YYMdwzM1ZmwywnGnlqBIdyHKtmVcFQuPjy4DZjfz9oksrbAOGW1q5zRY/tIC/uiKN4BdaPNDOjPXdemwmCF/AEhsA4KfAodAVeGECgYEA3VJrvh3Y8ixcv3m/2AOHBRerTGC4cT8Iw/Zj8l3NG5Ufj5m34P+/0jczTddRwZ8BmVhFXGzeu6+Q5TjG0z9vlz2baOkjSz/dKOtbOjk06rV4eguHEYixSgpxQSUhdnw16uqTA0G535t5SR85UNhTmeoeJvPy/gfoA8QH2XFeVhMCgYEAyVoE1qZljhE+JTXn15LcGZkFGVs7dZHhCt6YZXRUPBfpNuITRmHLHppmMwCz5cBmi08pTCqBRExx/Le6/NO0XvZq9mUU7PV/xoj6F0w4wsvCJPvXWMSKV74NiZ+A3uODmGfmV/6jr7vcPrCiyAmyzXwbLSjrMVWlprLr5zJq+1UCgYEAtgC/hZp5+70rkHKIpxVPhYqPXcH0K3zQsoX+byNgNpyNVcPtiOQIVND8Kbk2DGm8IOSMNZN6HxIjr7zfFH2IQPFyyfVfBtTABJR3cwv0TkdpicPNEUg9s0ufExl7yTogBM1elEEKn631MYKx2Z/sMBhtL09RtkG+NMqSQeiO0W0CgYApK1bB7NPm8G+cfCEjWsvWEAuS26yUpXc2Gb1fQSHYB6uPpTn7shMt9rPeyc3+tBBDe7Qd9rLnnBnSal65QzAmGoxUROa6bJfrqCY6jUMt0SupFDU72TPQLPPnp81ZxXsqtlPKui4NyIOH7eXasOD2UuHbofo05CYyp3LEmvJ04QKBgCNOfpUONr/9yL9ro42tnD9fBCxiap1M1bbsErM5LeH3f1hOMvgabQLEP48m+TKo3+RwP7+tszBMlMYGBmVcciVq8s/hbsOSH/e6a7tCnUX+b5fIFZamRyTsmJz/+8CgwnlK6h2fxrfa/TA4+2am/4Vldp3FMDpW2NzkfZQaE+Qw",
        // optional
        'log' => [
            'file' => './logs/alipay.log',
            'level' => 'debug'
        ],
        'mode' => 'dev', // optional,设置此参数，将进入沙箱模式
    ];
    /**
     * 设置订单总金额
     * @author <362431947@qq.com>
     * @date   2018-06-24
     * @param  string      $total_amount
     */
    public function setTotalAmount($total_amount = '0.00')
    {
        if($total_amount) $this->total_amount = $total_amount;
        return $this;
    }
    /**
     * 设置付款码
     * @author <362431947@qq.com>
     * @date   2018-06-24
     * @param  string     $auth_code
     */
    public function SetAuthCode($auth_code = '')
    {
        if($auth_code) $this->auth_code = $auth_code;
        return $this;
    }
    public function index()
    {
        // 'auth_code' => '28490016326344595',
        // 'total_amount' => '0.01',
        $order = [
            'out_trade_no' => 'alipaydev'.time(),
            'total_amount' => $this->total_amount,
            'subject' => 'test subject - 测试',
            'auth_code' => $this->auth_code,
        ];

        $alipay = Pay::alipay($this->config)->pos($order);
        // 刷卡支付,商户扫描买家付款码支付
        // $alipay = Pay::alipay($this->config)->scan($order);
        // 扫描商户二维码支付
        /* $alipay = Pay::alipay($this->config)->web($order);return $alipay->send();*/
        // pc网页支付

        return $alipay;
        // echo $qr = $alipay->qr_code;
        // echo "<pre>";
        // print_r($alipay);
    }

    public function return1()
    {
        $data = Pay::alipay($this->config)->verify(); // 是的，验签就这么简单！

        // 订单号：$data->out_trade_no
        // 支付宝交易号：$data->trade_no
        // 订单总金额：$data->total_amount
    }

    public function notify()
    {
        $alipay = Pay::alipay($this->config);

        try{
            $data = $alipay->verify(); // 是的，验签就这么简单！

            // 请自行对 trade_status 进行判断及其它逻辑进行判断，在支付宝的业务通知中，只有交易通知状态为 TRADE_SUCCESS 或 TRADE_FINISHED 时，支付宝才会认定为买家付款成功。
            // 1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号；
            // 2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额）；
            // 3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）；
            // 4、验证app_id是否为该商户本身。
            // 5、其它业务逻辑情况

            Log::debug('Alipay notify', $data->all());
        } catch (Exception $e) {
            // $e->getMessage();
        }

        return $alipay->success()->send();
    }
    /**
     * 统一订单查询
     * @author <362431947@qq.com>
     * @date   2018-06-30
     * @param  [type]     $trade_no
     * @return [type]
     */
    public function query($trade_no)
    {
        $res = Pay::alipay($this->config)->find(['trade_no'=>$trade_no]);
        // 查询结果必须判断订单,当前交易状态;  支付成功:TRADE_SUCCESS
        return $res;
    }
}
