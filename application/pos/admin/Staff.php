<?php 

namespace app\pos\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\user\model\Role as RoleModel;
use app\pos\model\Staff as StaffModel;
use app\user\model\User as UserModel;
use think\Hook;
use think\Db;

class Staff extends Admin 
{
	 /**
     * 企业员工列表
     * @author <362431947@qq.com>
     * @date   2018-07-15
     */
    public function index()
    {
        cookie('__forward__', $_SERVER['REQUEST_URI']);

        // 获取查询条件
        $map = $this->getMap();
        // ::where($map)->
        
        if(isset($map)){

        }
        // 数据列表
        // $data_list = $staff->index();//->paginate();
        $data_list = Db::table('mini_admin_user')
            ->alias('u')
            ->join('pos_staff s','u.id = s.uid')
            ->where($map)
            // ->order('sort,id desc')
            ->paginate();
        // 分页数据
        $page = $data_list->render();

        // 授权按钮
        $btn_access = [
            'title' => '授权',
            'icon'  => 'fa fa-fw fa-key',
            'href'  => url('access', ['uid' => '__id__'])
        ];

        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->setPageTitle('员工管理') // 设置页面标题
            ->setTableName('admin_user') // 设置数据表名
            ->setSearch(['username' => '用户名', 'email' => '邮箱']) // 设置搜索参数
            // ->setSearch(['uid' => 'ID', 'username' => '用户名', 'email' => '邮箱']) // 设置搜索参数
            ->addColumns([ // 批量添加列
                ['id', 'ID'],
                ['username', '用户名'],
                ['role', '角色', 'select', RoleModel::getTree(null, false)],
                ['email', '邮箱'],
                ['mobile', '手机号'],
                // ['storeid',  '门店','select',  ['a','b']],
                ['create_time', '创建时间', 'datetime'],
                ['status', '状态', 'switch'],
                ['right_button', '操作', 'btn']
            ])
            // ->setPrimaryKey('uid')
            ->addTopButtons('add,enable,disable,delete') // 批量添加顶部按钮
            ->addRightButton('custom', $btn_access) // 添加授权按钮
            ->addRightButtons('edit,delete') // 批量添加右侧按钮
            ->setRowList($data_list) // 设置表格数据
            ->setPages($page) // 设置分页数据
            ->fetch(); // 渲染页面
    }

    /**
     * 员工添加
     * @Author  <362431947@qq.com>
     * @Date    2018-07-26
     * @Version [version]
     */
    public function add()
    {
        if(1 == session('user_auth.uid')){
            // 超级管理员
            $this->error('超级管理员不允许添加企业员工');
        }
        // 保存数据
        if ($this->request->isPost()) {
            $data = $this->request->post();
            // 验证
            $result = $this->validate($data, 'Staff');
            // 验证失败 输出错误信息
            if(true !== $result) $this->error($result);

            $data['companyid'] = UserModel::getFieldById(session('user_auth.uid'),'companyid');
            if ($user = UserModel::create($data)) {
                $data['uid'] = $user['id'];
                StaffModel::create($data);
                Hook::listen('user_add', $user);
                // 记录行为
                action_log('user_add', 'admin_user', $user['id'], UID);
                $this->success('新增成功', url('index'));
            } else {
                $this->error('新增失败');
            }
        }

        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('新增') // 设置页面标题
            ->addFormItems([ // 批量添加表单项
                ['text', 'username', '姓名'],
                ['text', 'phone', '电话', ''],
                ['select', 'role', '角色', '', RoleModel::getTree(null, false)],
                ['select', 'storeid', '门店', '', RoleModel::getTree(null, false)],
                ['radio', 'status', '状态', '', ['禁用', '启用'], 1]
            ])
            ->fetch();
    }
    /**
     * 员工删除
     * @Author  <362431947@qq.com>
     * @Date    2018-07-26
     * @Version [version]
     * @param   [type]             $uid [description]
     * @return  [type]                  [description]
     */
    public function edit($id = null)
    {
        if ($id === null) $this->error('缺少参数');

        // 保存数据
        if ($this->request->isPost()) {
            $data = $this->request->post();

            // 验证
            $result = $this->validate($data, 'Staff.update');
            // 验证失败 输出错误信息
            if(true !== $result) $this->error($result);

            if (UserModel::update($data)) {
                StaffModel::update($data);
                $user = UserModel::get($data['id']);
                $staff = StaffModel::where(['uid'=>$data['id']])->find();
                Hook::listen('user_edit', $user);
                Hook::listen('staff_edit', $staff);
                // 记录行为
                // action_log('company_edit', 'pos_company', $company['companyid'], UID, get_nickname($user['id']));
                $this->success('编辑成功', cookie('__forward__'));
            } else {
                $this->error('编辑失败');
            }
        }

        // 获取数据
        $info = UserModel::where('id', $id)->find();
        // ->field('password', true)  排除密码字段

        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('编辑') // 设置页面标题
            ->addFormItems([ // 批量添加表单项
                ['hidden', 'id'], // 编辑 不可缺少
                ['text', 'username', '姓名'],
                ['text', 'phone', '电话', ''],
                // ['text', 'nickname', ''],
                ['select', 'role', '角色', '', RoleModel::getTree(null, false)],
                // ['select', 'storeid', '门店', '', ['a','b']],
                // ['text', 'storeid', '门店',                 ['image', 'avatar', '头像'],
                ['radio', 'status', '状态', '', ['禁用', '启用'], 1]
            ])
            ->setFormData($info) // 设置表单数据
            ->fetch();
    }
}