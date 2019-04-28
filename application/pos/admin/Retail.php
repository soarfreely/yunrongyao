<?php
namespace app\pos\admin;

use app\common\builder\ZBuilder;
use think\Db;
use app\pos\model\RetailDetail;


class Retail extends AbstractController
{
    /**
     * 每日收款
     * @Author  <362431947@qq.com>
     * @Date    2018-07-22T11:18:09+0800
     * @Version 1.0
     */
    public function index()
    {
        $data_list = model('RetailDetail')
            ->where($this->condition)->paginate();
        // $data_list = model('RetailDetail')
        //     ->join('pos_goods pg','mini_pos_retail_detail.goodsid = pg.goodsid AND mini_pos_retail_detail.company_id = pg.company_id')
        //     ->where('pg.company_id',$company_id)->paginate();

        return ZBuilder::make('table')
            ->addTimeFilter('create_date', date("Y-m-d"), date("Y-m-d")) // 添加时间段筛选
            ->setPageTitle('每日收款') // 设置页面标题
            ->setTableName('pos_retail_summary') // 设置数据表名
            ->setSearch(['serial' => '流水号','operateid'=>'收银员']) // 设置搜索参数
            ->hideCheckBox()
            ->addColumns([ // 批量添加列
                ['__INDEX__', '#'],
                ['serial', '流水号'],
                ['cost', '成本金额'],
                ['amount', '订单金额'],
                ['gross', '毛利金额'],
                ['discount', '优惠金额'],
                ['rate', '毛利率'],
                ['number', '商品数量'],
                ['operateid', '收银员'],
                ['create_time', '创建时间', 'datetime','','n/d H:i:s'],
                // ['right_button', '操作', 'btn']
            ])
            ->setRowList($data_list)
            ->setColumnWidth([
                    '__INDEX__' => 30,
                    'serial' => 150,
                    'cost' => 60,
                    'amount' => 60,
                    'rate' => 60,
                    'number' => 80,
                    'operateid' => 60,
                ])
            ->fetch(); // 渲染页面
    }

    /**
     * 详情
     * @Author  <362431947@qq.com>
     * @Date    2018-07-22T11:17:24+0800
     * @Version 1.0
     */
    public function detail()
    {
        return ZBuilder::make('table')
            ->setPageTitle('销售明细') // 设置页面标题
            ->hideCheckBox()
            ->setTableName('pos_retail_detail') // 设置数据表名
            ->setSearch(['serial' => '流水号','operateid'=>'收银员']) // 设置搜索参数
            ->addColumns([ // 批量添加列
                ['_INDEX_', '#'],
                ['serial', '流水号'],
                ['goodsid', '商品名称'],
                ['prime', '成本单价'],
                ['retail', '零售价'],
                ['number', '数量'],
                ['gross', '毛利'],
                ['rate', '毛利率'],
                ['create_time', '创建时间', 'datetime'],
                // ['status', '状态', 'switch'],
                // ['right_button', '操作', 'btn']
            ])
            ->setColumnWidth([
                    '_INDEX_' => 20,
                    'serial' => 80,
                ])
            ->fetch(); // 渲染页面
    }
    /**
     * 保存流水信息
     * @author <362431947@qq.com>
     * @date   2018-09-02
     */
    public function add()
    {
        $posid = session('user_auth.posid');

        $serial = serial();
        $sql = "INSERT INTO mini_pos_retail_detail
            SELECT * FROM mini_pos_tmp_retail_detail WHERE posid = $posid ";
        Db::startTrans();

        try {
            // 获取,除了user_id,content的其他所有字段
            $tmp_retail = Db::table('mini_pos_retail_detail')->field('id,serial',true)->select();
            $res = RetailDetail::create($tmp_retail);
            if($tmp_retail && $res){
                Db::commit();
                $response = ['code'=>1,'msg'=>'数据保存成功'];
            }
            else{
                Db::rollback();
                // 记录日志;支付成功,流水保存失败
                $response = ['code'=>0,'msg'=>'数据保存失败'];
            }
        } catch (\Exception $e) {
            Db::rollback();
            // 记录日志;支付成功,流水保存失败
            $response = ['code'=>0,'msg'=>'数据保存失败'];
        }

        return json($response);
    }
}
