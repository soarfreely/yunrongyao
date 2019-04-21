<?php
namespace app\pos\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\pos\model\Company as CompanyModel;
use util\Tree;
use think\Hook;
/**
 * 企业管理
 */
class Company extends Admin
{
 	/**
     * 企业管理首页
     * @Author  <362431947@qq.com>
     * @Date    2018-07-22T09:12:32+0800
     * @Version 1.0
     */
    public function index()
    {
        cookie('__forward__', $_SERVER['REQUEST_URI']);

        // 获取查询条件
        $map = $this->getMap();

        // 数据列表
        $data_list = CompanyModel::where($map)->paginate();;

        // 分页数据
        $page = $data_list->render();

        // 授权按钮
        // $btn_access = [
        //     'title' => '授权',
        //     'icon'  => 'fa fa-fw fa-key',
        //     'href'  => url('access', ['uid' => '__id__'])
        // ];

        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->setPageTitle('企业管理') // 设置页面标题
            ->setTableName('pos_company') // 设置数据表名
            ->setSearch(['companyid' => 'ID', 'company' => '企业名称']) // 设置搜索参数
            ->addColumns([ // 批量添加列
                ['companyid', 'ID'],
                ['company', '企业名称'],
                ['goods', '企业简称'],
                ['corporate', '法人'],
                ['license', '营业执照'],
                ['phone', '企业联系电话'],
                ['create_time', '创建时间', 'datetime'],
                ['status', '状态', 'switch'],
                ['right_button', '操作', 'btn']
            ])
            ->setPrimaryKey('companyid')
            ->addTopButtons('add,enable,disable,delete') // 批量添加顶部按钮
            // ->addRightButton('custom', $btn_access) // 添加授权按钮
            // ->addRightButton('edit') 
            ->addRightButton('edit',['href' => url('edit', ['companyid' => '__id__'])])
            // ->addRightButton('delete',['href' => url('delete', ['ids' => '__id__'])])
            ->addRightButtons('delete') // 批量添加右侧按钮
            // 批量添加右侧按钮
            ->setRowList($data_list) // 设置表格数据
            ->setPages($page) // 设置分页数据
            ->fetch(); // 渲染页面
    }

     /**
     * 新增
     * @Author  <362431947@qq.com>
     * @Date    2018-07-22T21:18:31+0800
     * @Version [version]
     */
    public function add()
    {
        // 保存数据
        if ($this->request->isPost()) {
            $data = $this->request->post();
            // 验证
            $result = $this->validate($data, 'Company');
            // 验证失败 输出错误信息
            if(true !== $result) $this->error($result);

            if ($company = CompanyModel::create($data)) {
                Hook::listen('company_add', $company);
                // 记录行为
                action_log('company_add', 'company', $company['companyid'], UID);
                $this->success('新增成功', url('index'));
            } else {
                $this->error('新增失败');
            }
        }

        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('新增') // 设置页面标题
            ->addFormItems([ // 批量添加表单项
                ['text', 'company', '企业名称'],
                ['text', 'goods', '简称', '用于支付显示'],
                ['text', 'corporate', '法人',''],
                // ['select', 'role', '角色', '', RoleModel::getTree(null, false)],
                ['text', 'phone', '企业联系电话', ''],
                ['text', 'detail', '详细地址', ''],
                ['image', 'license', '营业执照'],
                ['radio', 'status', '状态', '', ['禁用', '启用'], 1]
            ])
            ->fetch();
    }

    /**
     * 编辑
     * @Author  <362431947@qq.com>
     * @Date    2018-07-22T21:18:52+0800
     * @Version [version]
     * @param   [type]                   $id [description]
     * @return  [type]                       [description]
     */
    public function edit($companyid = null)
    {
        if ($companyid === null) $this->error('缺少参数');

        // 保存数据
        if ($this->request->isPost()) {
            $data = $this->request->post();

            // 验证
            $result = $this->validate($data, 'Company.update');
            // 验证失败 输出错误信息
            if(true !== $result) $this->error($result);

            if (CompanyModel::update($data)) {
                $company = CompanyModel::get($data['companyid']);
                Hook::listen('company_edit', $company);
                // 记录行为
                // action_log('company_edit', 'pos_company', $company['companyid'], UID, get_nickname($user['id']));
                $this->success('编辑成功', cookie('__forward__'));
            } else {
                $this->error('编辑失败');
            }
        }

        // 获取数据
        $info = CompanyModel::where('companyid', $companyid)->find();
        // ->field('password', true)  排除密码字段
        // print_r($info);die;
        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('编辑') // 设置页面标题
            ->addFormItems([ // 批量添加表单项
                ['hidden', 'companyid'], // 编辑 不可缺少
                ['text', 'company', '企业名称'],
                ['text', 'goods', '简称', '用于支付显示'],
                ['text', 'corporate', '法人',''],
                ['text', 'phone', '企业联系电话', ''],
                ['text', 'detail', '详细地址', ''],
                ['image', 'license', '营业执照'],
                ['radio', 'status', '状态', '', ['禁用', '启用'], 1]
            ])
            ->setFormData($info) // 设置表单数据
            ->fetch();
    }
}
