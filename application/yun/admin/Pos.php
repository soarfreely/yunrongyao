<?php
namespace app\pos\admin;

use app\common\controller\Common;
use app\common\builder\ZBuilder;
use app\pos\model\Goods;
use util\Gather;
use util\Collect;
use think\Request;
use think\Cookie;
use think\Db;
use app\admin\controller\Admin;
use app\pos\model\TmpRetailDetail;
use app\pos\model\RetailDetail;
use util\Alipayment;


class Pos extends Admin
{
	/**
	 * 弹出层 商品列表
	 * @Author  <362431947@qq.com>
	 * @Date    2018-08-14
	 * @Version 1.0
	 */
	public function layer()
    {
        $input = input('get.');
        $barcodename = input('barcodename','');
        $number = input('number','');

        $field = "goodsid,barcode,pinyin,goods,retail,categoryid,area";
        if(is_numeric(trim($barcodename))){
            // 是条码
            $where = ['barcode'=>$barcodename];
        }
        else{
            // 助记码
            $where['pinyin'] = ['like',"%$barcodename%"];
        }

        $goodsInfo = model('Goods')->where($where)->field($field)->select();

        $this->assign('goodsInfo',$goodsInfo);

        return $this->fetch('layer');
    }

    // 录入收款商品
    public function pos()
    {
        $collect = Collect::init();
        $this->assign('posid',session('user_auth.posid'));

        if(request()->isAjax()){
            $goodsid = input('post.goodsid',0);
            $number = input('post.number',0);

            $collect->setGoodsid($goodsid)->setNumber($number)->add();

            $this->assign('collect',$collect);

            $last_price = $collect->last_price;
            $amount = $collect->amount;
            return $this->fetch('table',['last_price'=>$last_price,'amount'=>$amount]);
        }
        $this->assign('collect',$collect);

        return  $this->fetch();
    }

    /**
     * 付款|点击付款按钮后
     * @Author  <362431947@qq.com>
     * @Date    2018-08-18
     * @Version 1.0
     */
    public function pay()
    {
        $goods_info = [];
        $goods_list = [];
        $summary['number'] = 0;
        $summary['amount'] = 0;
        $detail = [];
        $res = 'null';

        $response = [];
        if(request()->isAjax()){
            // 款台id
            $posid = session('user_auth.posid');

            $serial = serial();
            $time = time();
            $date = date("Y-m-d H:i:s");
            $uid = UID;

            $field = "NULL id,'$serial' serial,goodsid,prime,retail,vip,price,pay,gross,gross,number, rate,memberid,posid,storeid,companyid,$uid operateid,'$date' create_date,$time create_time,$time update_time";
            Db::startTrans();
            try {

                $detail = Db::name('pos_tmp_retail_detail')->where('posid',1)->limit(1)
                    ->field($field)
                    ->find();

                $res1 = RetailDetail::create($detail);

                $res2 = TmpRetailDetail::where('posid',$posid)->delete();
                if($res1 && $res2){
                    Db::commit();
                    $response = ['code'=>1,'msg'=>'付款成功'];
                }
                else{
                    $response = ['code'=>1,'msg'=>'付款失败'];
                }
            } catch (Exception $e) {
                Db::rollback();
                $response = ['code'=>1,'msg'=>'付款失败'];
            }
        }

        return json($response);
    }
    /**
     * 商户扫描买家付款码支付
     * @Author   <362431947@qq.com>
     * @DateTime 2018-09-01
     * @version  [version]
     */
    public function aliBarcodePay()
    {
        $total_amount = Gather::init()->goods_list['total_goods_price'];
        $total_amount = '0.01';
        if(! $total_amount){
            return json(['code'=>0,'msg'=>'订单金额不允许为空']);
        }
        // if(1){
        if(request()->isAjax()){
            $auto_code = input('post.auto_code');
            if(! $auto_code){
                return json(['code'=>0,'msg'=>'付款码不允许为空']);
            }
            $alipay = new Alipayment();
            $result = $alipay->setTotalAmount($total_amount)->SetAuthCode($auto_code)->index();
            // echo __LINE__,$result['trade_no'];
            // $query = $alipay->query($result['trade_no']);

            // if(10000 == $result['code'] && 'Success' == $result['msg']){
            //     // 支付成功
            //     $res = $this->saveSellInfo();
            //     if($res){
            //         // 清空cookie购物商品
            //         Gather::init()->clear();
            //     }
            //     else{
            //         // 打印日志
            //     }
            // }
            // elseif(10003 == $result['code']) {
            //     // 使用过期(使用过的二维码)也会提示 10003
            //     echo '10003';
            //     // code...
            // }
            // echo 'Query';
            // print_r($query);
            // echo __LINE__,"<br>";
            // print_r($result);
            return $result;
        }
    }

    /**
     * 查询订单交易状态
     */
    public function query()
    {
        $trade_no = input('post.trade_no');
        $alipay = new Alipayment();
        $resp = $alipay->query($trade_no);
        return $resp;
    }


}
