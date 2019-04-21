<?php
namespace util;

/**
 * 收款类
 */
class Gather
{
    public $goods_list = [];
    // 销售的商品列表
    public $last_goods_price = 0.0;
    // 最后一个商品的价格
    public $total_goods_price = 0.0;
    // 总金额
    public static $instance = null;

    public function __construct()
    {
        $this->getGoodsList();
    }
    /**
     * 实例化 当前类
     * @author <362431947@qq.com>
     * @date   2018-06-10
     */
    public static function init()
    {
        if(! self::$instance instanceof self){
            self::$instance = new self;
        }
        return self::$instance;
    }
    /**
     * 点击确认,将商品添加至销售列表
     * @author 362431947@qq.com  @date 2018-05-28
     */
    public function add($goodsid,$number)
    {
        $goods_list = [];
        $retail_price = 0;

        if($goodsid){
            $goodsid = intval($goodsid);
            $goods_list = model('Goods')->getGoodsById($goodsid);
            $goods_list['number'] = intval($number);
        }
        if($goods_list){
            $this->goods_list['detail'][] = $goods_list;
            $retail_price = $goods_list['retail_price'];
        }
        // 更新最后一个商品的价格
        $this->goods_list['last_goods_price'] = $retail_price;

        // 计算商品的总价
        $total_price = $retail_price * $number;
        $this->goods_list['total_goods_price'] += $total_price;

        $this->save();
        // 保存商品信息到cookid
        return $this;
    }
    /**
     * 保存商品列表信息到cookie
     * @author <362431947@qq.com>
     * @date   2018-06-10
     */
    public function save()
    {
        cookie('goods_list',serialize($this->goods_list));
    }
    /**
     * 从cookie中获取购买商品的信息列表
     * @author <362431947@qq.com>
     * @date   2018-06-10
     */
    public function getGoodsList()
    {
        $this->goods_list = unserialize(cookie('goods_list'));

        if(! isset($this->goods_list['total_goods_price'])){
            $this->goods_list['total_goods_price'] = 0;
        }
        if(! isset($this->goods_list['last_goods_price'])){
            $this->goods_list['last_goods_price'] = 0;
        }
        return $this;
        // $this->goods_list = Cookie::has('goods_list') ? unserialize(cookie('goods_list')) : [];
    }
    /**
     * 商品金额合计
     * @author <362431947@qq.com>
     * @date   2018-06-10
     * @param  float      $prepay  预支付,已支付金额
     */
    public function summary($prepay = 0.0)
    {
        $change = 0;
        $this->getGoodsList();
        $total_price = isset($this->goods_list['total_goods_price']) ? $this->goods_list['total_goods_price'] : 0;
        if($prepay){
            $change = $prepay - $total_price;
        }
        return [
            'amount' => $total_price, //应付,合计金额
            'prepay' => $prepay, // 已付,实付金额
            'change' => $change, // 找零金额
        ];
    }
    /**
     * 清空购物列表
     * @author <362431947@qq.com>
     * @date   2018-06-13
     */
    public function clear()
    {
        cookie('goods_list',null);
    }
    // public function setLastGoodsPrice($price)
    // {
    //     $this->last_goods_price = $price;
    // }
    // public function setTotalGoodsPrice($price)
    // {
    //     $this->total_goods_price += $price;
    // }
}
