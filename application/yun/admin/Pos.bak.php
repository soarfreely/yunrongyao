<?php
namespace app\pos\admin;

use app\common\controller\Common;
use app\common\builder\ZBuilder;
use app\pos\model\Goods;
use util\Gather;
// use app\user\model\Role as RoleModel;
// use app\user\model\Message as MessageModel;
// use think\Cache;
// use think\Db;
use think\Request;
use think\Cookie;
// use think\helper\Hash;
use app\admin\controller\Admin;
// use app\user\model\User as UserModel;
// use app\admin\model\Module as ModuleModel;
// use app\admin\model\Access as AccessModel;
// use util\Tree;
// use think\Hook;
use util\Alipayment;

class Pos extends Admin
{
    /**
     * 弹出层
     * @author xiang  @date 2018-04-22
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
    /**
     * 合计金额计算
     * @author <362431947@qq.com>
     * @date   2018-06-10
     */
    public function total()
    {
        $payInfo = [];
        $prepay = 0;
        if(request()->isAjax()){
            // $goodsid = input('post.goodsid');
            // $number = input('post.number');
            $prepay = input('post.prepay');

            $payInfo = Gather::init()->summary($prepay);
            return json($payInfo);
        }
    }
    // 录入收款商品
    public function pos()
    {
        $gather = Gather::init();
        if(request()->isAjax()){
            $goodsid = input('post.goodsid');
            $number = input('post.number');

            $gather->add($goodsid,$number);

            $this->assign('gather',$gather);
            $this->assign('last_goods_price',$gather->goods_list['last_goods_price']);
            $this->assign('total_goods_price',$gather->goods_list['total_goods_price']);
            // echo __LINE__;
            return $this->fetch('table');
        }
        // echo __LINE__;
        $this->assign('gather',$gather);
        return $this->fetch('pos');
    }
    /**
     * 收款
     * @author <362431947@qq.com>
     * @date   2018-05-31
     */
    public function pay()
    {
        $goods_info = [];
        $goods_list = [];
        $summary['number'] = 0;
        $summary['amount'] = 0;
        $detail = [];
        $res = null;
        if(request()->isAjax()){
            // 保存销售信息
            $res = $this->saveSellInfo();
        }


        if($res){
            // 清空cookie购物商品
            Gather::init()->clear();
            return json(['code'=>1,'msg'=>'付款成功']);
        }
        else{
            return json(['code'=>0,'msg'=>'付款失败']);
        }
    }
    /**
     * 销售明细入库
     * @author <362431947@qq.com>
     * @date   2018-06-24
     */
    public function saveSellInfo()
    {
        $summary = [
            'number'=>0,
            'amount'=>0
        ];
        $detail = [];
        $goods_list = unserialize(cookie('goods_list'));
        $goods_info = $goods_list['detail'];
        if($goods_info){
            foreach ($goods_info as $key => $value) {
                $summary['number'] += $value['number'];
                $summary['amount'] += $value['retail_price'];
                $detail[$key]['retail'] = $value['retail_price'];
                $detail[$key]['number'] = $value['number'];
            }
        }
        model('RetailSummary')->save($summary);
        $res = model('RetailDetail')->saveAll($detail);
        if($res){
            // 清空cookie购物商品
            Gather::init()->clear();
            // $this->redirect('pos/pos/pos');
        }
        else{
            // 打印日志
        }
    }
    /**
     * 销售入库
     * @var [type]
     */
    public function gather()
    {
        // $test = new Alipayment();
        // $test->index();
        // $input = input();
        // return input('post.');
        // if(request()->isAjax()){
        //     $amount = input('post.amount');
        //
        //     return $amount;
            // print_r(['amount'=>$input]);
        //     // if($amount){
        //     //     $goodsInfo = model('Goods')->getGoodsByGoodsid($amount);
        //     // }
        // }
    }
    /**
     * 商户扫描买家付款码支付
     * @author <362431947@qq.com>
     * @date   2018-06-24
     * @return [type]
     */
    public function aliBarcodePay()
    {
        // return json(input('post.'));
        $total_amount = Gather::init()->goods_list['total_goods_price'];
        $total_amount = '0.01';
        if(! $total_amount){
            return json(['code'=>0,'msg'=>'订单金额不允许为空']);
        }
        if(1){
        // if(request()->isAjax()){
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
    public function query()
    {
        $trade_no = input('post.trade_no');
        $alipay = new Alipayment();
        $resp = $alipay->query($trade_no);
        return $resp;
    }
}
