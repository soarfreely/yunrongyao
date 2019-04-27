<?php

namespace app\pos\admin;

use app\common\builder\ZBuilder;
use app\admin\controller\Admin;
use app\pos\model\Goods as GoodsModel;
use app\pos\model\Category as CategoryModel;
use util\Tree;

class Goods extends Admin
{
    /**
     * 商品添加
     * @Author  <362431947@qq.com>
     * @Date    2018-08-06
     * @Version [version]
     * @param   integer $parentid [description]
     */
    public function add()
    {
        // $a = Collect::init();
        // 总金额入库；最后一个商品价格，从数据库获取

        // 保存数据
        if ($this->request->isPost()) {
            $data = $this->request->post();

            $data['companyid'] = session('user_auth.companyid');
            $data['operateid'] = session('user_auth.uid');

            if ($goods = GoodsModel::create($data)) {

                $this->success('新增成功', url('index'));
            } else {
                $this->error('新增失败');
            }
        }
        $data_list = CategoryModel::where('companyid', session('user_auth.companyid'))->order('sort,categoryid desc')->column('categoryid,category,parentid');
        Tree::config(['id' => 'categoryid', 'title' => 'category', 'pid' => 'parentid']);
        $data_list = Tree::toList($data_list);

        $result = [];
        foreach ($data_list as $menu) {
            $result[$menu['categoryid']] = $menu['title_display'];
        }
        $js = <<<JS
JS;
        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('新增')// 设置页面标题
            // ->hideCheckbox()
            ->addFormItems([ // 批量添加表单项
                ['text', 'barcode', '条码'],
                ['text', 'goods', '商品名称'],
                ['text', 'pinyin', '拼音码', '', '', '', 'readonly disabled'],
            ])
            ->addFormItems([
                ['select', 'categoryid', '所属分类', '', $result, $categoryid = 0],
            ])
            ->addFormItems([ // 批量添加表单项
                ['text', 'specs', '规格'],
                ['text', 'status', '商品销售状态'],
                ['text', 'unitid', '单位', ''],
                ['text', 'last', '采购价'],
                ['text', 'retail', '零售价', ''],
                ['text', 'retail_rate', '零售毛利率', '', '', '', 'readonly disabled'],
                ['text', 'supplierid', '供货商'],
            ])
            ->addDate('date', '生产日期')
            ->addNumber('month', '有效期', '与有效日期，二选一填写', '1', '1', '', 1)
            ->addDate('expire', '有效日期', '与有效期，二选一填写')
            ->addFormItems([ // 批量添加表单项
                ['text', 'maker', '生产厂家', ''],
                ['text', 'area', '产地', ''],
                // ['image', 'avatar', '头像'],
                // ['radio', 'status', '状态', '', ['禁用', '启用'], 1]
            ])
            ->layout([
                'goods'      => '12|12|6|6',
                'barcode'    => '12|12|6|6',
                'pinyin'     => '12|12|3|3',
                'specs'      => '12|12|3|3',
                'categoryid' => '12|12|3|3',
                'status'     => '12|12|3|3',

                'unitid'      => '12|12|3|3',
                'last'        => '12|12|3|3',
                'retail'      => '12|12|3|3',
                'retail_rate' => '12|12|3|3',

                'supplierid' => '12|12|6|6',
                'date'       => '12|12|6|6',

                'month'  => '12|12|6|6',
                'expire' => '12|12|6|6',

                'maker' => '12|12|6|6',
                'area'  => '12|12|6|6',
            ])
            // ->js('test')
            //引入js
            ->setExtraJs($js)
            // 设置额外js
            ->fetch();
    }


    /**
     * 商品管理首页
     *
     * index
     * User: <zhangxiang_php@vchangyi.com>
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * Date: 19-4-27 Time: 下午3:51
     */
    public function index()
    {
        $map = ['status' => GoodsModel::GOODS_STATUS_DEFAULT];

        if ($this->request->isAjax()) {
            $param = $this->request->param();

            $category_id = $param['category_id'];

            $child_category_id = [];
            if ($category_id) {
                $child_category_id = CategoryModel::get($category_id)->ChildrenCategory($category_id);
            }
            $data_list = GoodsModel::whereIn('category_id', $child_category_id)->where($map)->select();

            if ($data_list) {
                foreach ($data_list as $key => & $value) {
                    $value = $value->toArray();
                }
            }
            $data_list = $data_list ? $data_list : [];

            $this->assign('goodsList', $data_list);
            return $this->fetch('ajaxlist');
        } else {
            $category = CategoryModel::where('company_id', session('user_auth.company_id'))
                ->order('sort,id')->column('id,parent_id,category,sort');

            // 配置相关字段
            Tree::config(['title' => 'category', 'pid' => 'parent_id']);
            $category = Tree::toLayer($category);

            $goods_list = GoodsModel::where($map)->select();
            if ($goods_list) {
                foreach ($goods_list as $key => & $value) {
                    $value = $value->toArray();
                }
            }

            $category = $this->buildJsTree($category);
            $this->assign('goods_list', $goods_list);
            $this->assign('category', $category);
            $this->assign('page_title', '商品管理');

            return $this->fetch('category/goods_list');
        }
    }

    /**
     * 删除
     * @Author  <362431947@qq.com>
     * @Date    2018-08-08
     * @Version 1.0
     */
    public function remove()
    {
        $goodsid = input('param.goodsid');

        if (db('pos_goods')->delete($goodsid)) {
            $this->success('删除成功', 'index');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * 编辑
     * @Author  <362431947@qq.com>
     * @Date    2018-08-08
     * @Version [version]
     * @param   [type]             $goodsid [description]
     * @return  [type]                      [description]
     */
    public function edit($goodsid = null, $categoryid = 0)
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();

            $data['companyid'] = session('user_auth.companyid');
            $data['operateid'] = session('user_auth.uid');

            // dump($data);
            if (GoodsModel::update($data)) {

                $this->success('编辑成功', url('index'));
            } else {
                $this->error('编辑失败');
            }
        }
        // die;
        // 获取数据
        $info = GoodsModel::where('id', $goodsid)->find();

        $data_list = CategoryModel::where('companyid', session('user_auth.companyid'))->order('sort,categoryid desc')->column('categoryid,category,parentid');
        Tree::config(['id' => 'categoryid', 'title' => 'category', 'pid' => 'parentid']);
        $data_list = Tree::toList($data_list);

        $result = [];
        foreach ($data_list as $menu) {
            $result[$menu['categoryid']] = $menu['title_display'];
        }

        $js = <<<JS
JS;
        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('新增')// 设置页面标题
            // ->hideCheckbox()
            ->addFormItems([ // 批量添加表单项
                ['text', 'barcode', '条码'],
                ['text', 'goods', '商品名称'],
                ['text', 'pinyin', '拼音码', '', '', '', 'readonly disabled'],
            ])
            ->addFormItems([
                ['select', 'categoryid', '所属分类', '', $result, $categoryid],
            ])
            ->addFormItems([ // 批量添加表单项
                ['text', 'specs', '规格'],
                ['text', 'status', '商品销售状态'],
                ['text', 'unitid', '单位', ''],
                ['text', 'last', '采购价'],
                ['text', 'retail', '零售价', ''],
                ['text', 'retail_rate', '零售毛利率', '', '', '', 'readonly disabled'],
                ['text', 'supplierid', '供货商'],
            ])
            ->addDate('date', '生产日期')
            ->addNumber('month', '有效期', '与有效日期，二选一填写', '1', '1', '', 1)
            ->addDate('expire', '有效日期', '与有效期，二选一填写')
            ->addFormItems([ // 批量添加表单项
                ['text', 'maker', '生产厂家', ''],
                ['text', 'area', '产地', ''],
                // ['image', 'avatar', '头像'],
                // ['radio', 'status', '状态', '', ['禁用', '启用'], 1]
            ])
            ->layout([
                'goods'      => '12|12|6|6',
                'barcode'    => '12|12|6|6',
                'pinyin'     => '12|12|3|3',
                'specs'      => '12|12|3|3',
                'categoryid' => '12|12|3|3',
                'status'     => '12|12|3|3',

                'unitid'      => '12|12|3|3',
                'last'        => '12|12|3|3',
                'retail'      => '12|12|3|3',
                'retail_rate' => '12|12|3|3',

                'supplierid' => '12|12|6|6',
                'date'       => '12|12|6|6',

                'month'  => '12|12|6|6',
                'expire' => '12|12|6|6',

                'maker' => '12|12|6|6',
                'area'  => '12|12|6|6',
            ])
            ->setFormData($info)// 设置表单数据
            ->addHidden('id')
            // ->js('test')
            //引入js
            ->setExtraJs($js)
            // 设置额外js
            ->fetch();

        //     if ($goodsid === null) $this->error('缺少参数');

        //     // 保存数据
        //     if ($this->request->isPost()) {
        //         $data = $this->request->post();
        //         if (CategoryModel::update($data)) {
        //             $this->success('编辑成功', 'index');
        //         } else {
        //             $this->error('编辑失败');
        //         }
        //     }

        //     // 获取数据
        //     $info = GoodsModel::where('goodsid',$goodsid)->find();

        //     // 使用ZBuilder快速创建表单
        //     return ZBuilder::make('form')
        //         ->setPageTitle('编辑') // 设置页面标题
        //         ->addFormItems([ // 批量添加表单项
        //             ['text', 'category', '分类', '必填，分类名称'],
        //             ['text', 'sort', '排序', '分类排序'],
        //             // ['goodsid', 'ID'],
        //             // ['goods', '商品名称'],
        //             // ['pinyin', '拼音码'],
        //             // ['categoryid', '分类'],
        //             // ['sort', '排序'],
        //             // ['userid', '操作员'],
        //             // ['create_time', '创建时间', 'datetime']
        //         ])
        //         ->setFormData($info) // 设置表单数据
        //         ->addHidden('categoryid')
        //         ->fetch();
    }

    /**
     * 商品详情
     * @Author  <362431947@qq.com>
     * @Date    2018-08-11
     * @Version 1.0
     */
    public function detail()
    {
        $goodsid = input('param.goodsid');

        if (!intval($goodsid)) {
            $this->error('缺少参数');
        }
        $goods = GoodsModel::get($goodsid);

        $this->assign('goods', $goods);

        return $this->fetch('goods/detail');
    }


    /**
     * 构建jstree代码
     * @param array $menus 菜单节点
     * @param array $user 用户信息
     * @author 蔡伟明 <314013107@qq.com>
     * @return string
     */
    private function buildJsTree($menus = [], $user = [])
    {
        $result = '';
        if (!empty($menus)) {
            $option = [
                'opened'   => true,
                'selected' => false,
                'icon'     => '',
            ];
            foreach ($menus as $menu) {
                if (isset($menu['child'])) {
                    $result .= '<li id="' . $menu['id'] . '" data-jstree=\'' . json_encode($option) . '\'>' . $menu['category'] . $this->buildJsTree($menu['child'], $user) . '</li>';
                } else {
                    $result .= '<li id="' . $menu['id'] . '" data-jstree=\'' . json_encode($option) . '\'>' . $menu['category'] . '</li>';
                }
            }
        }

        return '<ul>' . $result . '</ul>';
    }

}
