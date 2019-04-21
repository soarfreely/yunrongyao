<?php
namespace app\pos\model;

use think\Model;
use think\Request;

/**
 * 商品模型
 * @package app\admin\model
 */
class Goods extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__POS_GOODS__';
    // 自动写入时间戳
    protected $autoWriteTimestamp = true;

    /**
     * 通过条码或是助记码获取商品信息
     * @author xiang  @date 2018-04-28
     * @param  string $barcodename
     */
    public function getGoodsByBarcodeName($barcodename = '')
    {
        $field = "goodsid,barcode,pinyin,goods,retail_price,categoryid";
        if(is_numeric($barcodename)){
            // 是条码
            $where = ['barcode'=>$barcodename];
        }
        else{
            // 助记码
            $where['pinyin'] = ['like',"%$barcodename%"];
        }

        $goodsList = model('Goods')->where($where)->field($field)->select();
        if($goodsList){
            foreach ($goodsList as $key =>& $value) {
                $value = $value->toArray();
            }
        }
        else{
            $goodsList = [];
        }
        return $goodsList;
    }
    /**
     * 获取商品信息
     * @author xiang  @date 2018-04-30
     * @param  integer goodsid
     * @return [type]
     */
    public function getGoodsByGoodsid($goodsid = 0)
    {
        $field = "goodsid,barcode,pinyin,goods,retail_price,categoryid";

        $where = ['goodsid'=>$goodsid];

        $goodsList = model('Goods')->where($where)->field($field)->select();
        if($goodsList){
            foreach ($goodsList as $key =>& $value) {
                $value = $value->toArray();
            }
        }
        else{
            $goodsList = [];
        }
        return $goodsList;
    }
    public function getGoodsById($goodsid = 0)
    {
        $field = "goodsid,barcode,pinyin,goods,retail_price,categoryid";
        $where = ['goodsid'=>$goodsid];

        $goods_list = model('Goods')->where($where)->field($field)->find();

        return $goods_list ? $goods_list->toArray() : [];
    }
}
