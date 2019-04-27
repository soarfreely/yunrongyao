<?php

namespace app\pos\model;

/**
 * 商品模型
 * @package app\admin\model
 */
class Goods extends AbstractModel
{
    // 0-进销,1-只销,2,停销,3-停用
    const GOODS_STATUS_DEFAULT = 0;

    /**
     * 只销
     */
    const GOODS_STATUS_SALE = 1;

    /**
     * 停销
     */
    const GOODS_STATUS_OUT = 2;

    /**
     * 停用
     */
    const GOODS_STATUS_STOP = 3;


    /**
     * 设置当前模型对应的完整数据表名称
     *
     * @var string
     */
    protected $table = '__POS_GOODS__';

    /**
     * 自动写入时间戳
     *
     * @var bool
     */
    protected $autoWriteTimestamp = true;

    /**
     * 通过条码或是助记码获取商品信息
     *
     * getGoodsByBarcodeName
     * User: <zhangxiang_php@vchangyi.com>
     * @param string $barcodename
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * Date: 19-4-27 Time: 下午2:41
     */
    public function getGoodsByBarcodeName($barcodename = '')
    {
        $field = "id,barcode,pinyin,goods,retail_price,category_id";
        if (is_numeric($barcodename)) {
            // 是条码
            $where = ['barcode' => $barcodename];
        } else {
            // 助记码
            $where['pinyin'] = ['like', "%$barcodename%"];
        }

        $goodsList = model('Goods')->where($this->condition)->where($where)->field($field)->select();
        if ($goodsList) {
            foreach ($goodsList as $key => & $value) {
                $value = $value->toArray();
            }
        } else {
            $goodsList = [];
        }
        return $goodsList;
    }

    /**
     * 获取商品信息
     * getGoodsByGoodsid
     * User: <zhangxiang_php@vchangyi.com>
     * @param int $goods_id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * Date: 19-4-27 Time: 下午2:08
     */
    public function getGoodsByGoodsid($goods_id = 0)
    {
        $field = "id,barcode,pinyin,goods,retail_price,category_id";

        $where = ['id' => $goods_id];

        $goodsList = model('Goods')->where($this->condition)->where($where)->field($field)->select();
        if ($goodsList) {
            foreach ($goodsList as $key => & $value) {
                $value = $value->toArray();
            }
        } else {
            $goodsList = [];
        }
        return $goodsList;
    }

    /**
     * getGoodsById
     * User: <zhangxiang_php@vchangyi.com>
     * @param int $goods_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * Date: 19-4-27 Time: 下午2:06
     */
    public function getGoodsById($goods_id = 0)
    {
        $field = "id,barcode,pinyin,goods,retail_price,category_id";
        $where = ['id' => $goods_id];

        $goods_list = model('Goods')->where($this->condition)->where($where)->field($field)->find();

        return $goods_list ? $goods_list->toArray() : [];
    }
}
