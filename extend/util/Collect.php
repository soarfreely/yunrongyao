<?php
namespace util;

use think\Db;
use app\pos\model\Goods;
use app\pos\model\TmpRetailDetail;
// use app\pos\model\Goods

// 收款类

class Collect
{

    private static $instance  = NULL;  // 实例

    public $goodsid      = 0;      // 商品id
    public $number       = 1;      // 购买数量
    public $last_price   = 0.00;   // 最后录入商品的单价（零售价）
    public $amount       = 0.00;   // 订单总金额
    public $order_memory = [];     // 录入商品未收款时/按照款台id存储


    public function __construct()
    {
        $this->getOrder();
        // 获取订单列表
        
    }
    /**
     * 实例化 当前类
     * @Author   <362431947@qq.com>
     * @Datetime 2018-08-14
     * @Version  1.0
     */
    public static function init($goodsid = 0,$number = 0)
    {
        if(! self::$instance instanceof self){
            self::$instance = new self;
        }

        // self::$instance->posid   = session('user_auth.posid');

        if($goodsid) self::$instance->goodsid = $goodsid;
        if($number)  self::$instance->number  = $number;

        return self::$instance;
    }

    /**
     * 设置商品id
     * @Author  <362431947@qq.com>
     * @Date    2018-08-14
     * @Version [ver1.0
     */
    public function setGoodsid($goodsid = NULL)
    {
        if($goodsid) $this->goodsid = $goodsid;

        return $this;
    }
    /**
     * 设置购买数量
     * @Author  <362431947@qq.com>
     * @Date    2018-08-14
     * @Version 1.0
     * @param   integer    $number 购买数量
     */
    public function setNumber($number = 1)
    {
        $this->number = $number;

        return $this;
    }
    /**
     * 获取款台id
     * @Author  <362431947@qq.com>
     * @Date    2018-08-14
     * @Version 1.0
     */
    public function getPosid()
    {
        return 1;
        // 通过记录客户端网卡地址，获取款台id;在登录时获取款台信息，存入session
    }
    /**
     * 点击确认,将商品添加至销售列表
     * @Author   <362431947@qq.com>
     * @Datetime 2018-08-14
     * @Version  1.0
     * @param    integer             $goodsid 商品id
     * @param    integer             $number  购买数量
     */
    public function add()
    {
        $field = 'goodsid,prime,retail,companyid';
        $goods_info = Goods::where('goodsid',$this->goodsid)->field($field)->find($this->goodsid);

        $tmp_detail = [
            'number'      => $this->number,
            'create_date' => date("Y-m-d H:i:s"),
            'posid'       => session('user_auth.posid'),
        ];
        
        $data = array_merge($goods_info->toArray(),$tmp_detail);

        if(TmpRetailDetail::create($data)){
            
            $this->getOrder();
        }


        return $this;
    }

    /**
     * 获取订单列表
     * @Author  <362431947@qq.com>
     * @Date    2018-08-14
     * @Version 1.0
     */
    public function getOrder()
    {
        $posid = session('user_auth.posid');

        $order_memory = model('TmpRetailDetail')
            ->join('pos_goods g',' mini_pos_tmp_retail_detail.goodsid = g.goodsid')
            ->where('mini_pos_tmp_retail_detail.posid',$posid)
            // ->field('serial')
            ->select();
        
            if(is_array($order_memory) && $order_memory){
            $this->amount = 0;
            foreach ($order_memory as $key => $goods) {
                // 总金额入库；最后一个商品价格，从数据库获取
                
                $amount = ($goods->retail * $goods->number);
                if($amount) $this->amount += ($goods->retail * $goods->number);

                if($goods->retail) $this->last_price = $goods->retail;  
                // 最后一个商品的价格
            }
        }


        $this->order_memory[$posid] = array_reverse($order_memory);

        return $this;
    }
    /**
     * 获取最后一个商品的信息
     * @Author  <362431947@qq.com>
     * @Date    2018-08-14
     * @Version 1.0
     */
    public function getLastGoods()
    {
        $goods = Goods::get($this->goodsid);

        return $goods;
    }
}
