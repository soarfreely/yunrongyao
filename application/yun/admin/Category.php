<?php
namespace app\pos\admin;

use app\common\controller\Common;
use app\common\builder\ZBuilder;
use app\pos\model\Category as CategoryModel;
// use think\Db;
// use think\Loader;
// use think\helper\Hash;
use app\admin\controller\Admin;
use util\Tree;
// use think\Hook;

class Category extends Admin
{
    /**
     * 添加
     */
    public function  add($parentid = 0)
    {
        // 保存数据
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $data['operateid'] = session('user_auth.uid');
            $data['companyid'] = session('user_auth.companyid');
            $data['create_time'] = time();
            $data['update_time'] = time();
            // 添加数据
            if ($category = CategoryModel::create($data)) {
                $this->success('新增成功', url('index'));
            } else {
                $this->error('新增失败');
            }
        }
        // $data_list = CategoryModel::order('sort,categoryid desc')->column('categoryid,category,parentid');
        $data_list = CategoryModel::where('companyid',session('user_auth.companyid'))->order('sort,categoryid desc')->column('categoryid,category,parentid');

        $data_list1 = Tree::config(['id'=>'categoryid','title'=>'category','pid'=>'parentid']);
        $data_list1 = Tree::toList($data_list);
        $result = [];
        foreach ($data_list1 as $menu) {
            $result[$menu['categoryid']] = $menu['title_display'];
        }
        // dump($result);exit;

        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('新增节点')
            ->addFormItems([
                ['select', 'parentid', '所属分类', '所属上级分类', $result,$parentid],
                ['text', 'category', '分类'],
            ])
            ->addText('sort', '排序', '', 100)
            ->setTrigger('auto_create', '1', 'child_node', false)
            ->fetch();
    }

    /**
     * list() 方法
     * @return [type] [description]
     */
    public function index()
    {

        // 获取节点数据
        $data_list = CategoryModel::order('sort,categoryid desc')->select();
        if($data_list){
            foreach ($data_list as $key =>& $value) {
                $value = $value->toArray();
                // $value['pid'] = $value['parentid'];
                // $value['id'] = $value['categoryid'];
                $value['title'] = $value['category'];
                $value['icon'] = '';
                $value['url_value'] = '';
                $value['module'] = '';
                $value['status'] = 1;
                // unset($value['parentid']);
                // unset($value['categoryid']);
                // unset($value['category']);
            }
        }
        $max_level = $this->request->get('max', 0);
        // $menus = $this->getNestMenu($data_list, $max_level);
        // print_r(['a'=>$menus]);die;
        $this->assign('menus', $this->getNestMenu($data_list, $max_level));
        $this->assign('role_list', []);
        $this->assign('module_list', []);

        // $this->assign('tab_nav', ['tab_list' => $tab_list, 'curr_tab' => '']);
        $this->assign('page_title', '节点管理');
        return $this->fetch('category/categorylist');
    }
    /**
     * 编辑
     * @author xiang  @date 2018-04-14
     */
    public function edit($categoryid = null)
    {
        if ($categoryid === null) $this->error('缺少参数');

        // 保存数据
        if ($this->request->isPost()) {
            $data = $this->request->post();
            if (CategoryModel::update($data)) {
                $this->success('编辑成功', 'index');
            } else {
                $this->error('编辑失败');
            }
        }

        // 获取数据
        $info = CategoryModel::where('categoryid',$categoryid)->find();

        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('编辑') // 设置页面标题
            ->addFormItems([ // 批量添加表单项
                ['text', 'category', '分类', '必填，分类名称'],
                ['text', 'sort', '排序', '分类排序'],
            ])
            ->setFormData($info) // 设置表单数据
            ->addHidden('categoryid')
            ->fetch();
    }
    /**
     * 删除
     * @author xiang  @date 2018-04-14
     */
    public function delete($categoryid = [])
    {
        if (CategoryModel::destroy($categoryid)) {
            $this->success('删除成功', 'index');
        }
        else{
            $this->error('删除失败');
        }
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
                    $result .= '<li id="'.$menu['categoryid'].'" data-jstree=\''.json_encode($option).'\'>'.$menu['category'].$this->buildJsTree($menu['child'], $user).'</li>';
                } else {
                    $result .= '<li id="'.$menu['categoryid'].'" data-jstree=\''.json_encode($option).'\'>'.$menu['category'].'</li>';
                }
            }
        }

        return '<ul>'.$result.'</ul>';
    }

    private function getNestMenu($lists = [], $max_level = 0, $parentid = 0, $curr_level = 1)
    {
        $result = '';
        foreach ($lists as $key => $value) {
            if ($value['parentid'] == $parentid) {
                $disable  = $value['status'] == 0 ? 'dd-disable' : '';

                // 组合节点
                $result .= '<li class="dd-item dd3-item '.$disable.'" data-id="'.$value['categoryid'].'">';
                $result .= '<div class="dd-handle dd3-handle">拖拽</div><div class="dd3-content"><i class="'.$value['icon'].'"></i> '.$value['title'];
                if ($value['url_value'] != '') {
                    $result .= '<span class="link"><i class="fa fa-link"></i> '.$value['url_value'].'</span>';
                }
                $result .= '<div class="action">';
                $result .= '<a href="'.url('add', ['module' => $value['module'], 'parentid' => $value['categoryid']]).'" data-toggle="tooltip" data-original-title="新增子节点"><i class="list-icon fa fa-plus fa-fw"></i></a><a href="'.url('edit', ['categoryid' => $value['categoryid']]).'" data-toggle="tooltip" data-original-title="编辑"><i class="list-icon fa fa-pencil fa-fw"></i></a>';
                // if ($value['status'] == 0) {
                //     // 启用
                //     $result .= '<a href="javascript:void(0);" data-ids="'.$value['categoryid'].'" class="enable" data-toggle="tooltip" data-original-title="启用"><i class="list-icon fa fa-check-circle-o fa-fw"></i></a>';
                // } else {
                //     // 禁用
                //     $result .= '<a href="javascript:void(0);" data-ids="'.$value['categoryid'].'" class="disable" data-toggle="tooltip" data-original-title="禁用"><i class="list-icon fa fa-ban fa-fw"></i></a>';
                // }
                $result .= '<a href="'.url('delete', ['categoryid' => $value['categoryid'], 'table' => 'admin_menu']).'" data-toggle="tooltip" data-original-title="删除" class="ajax-get confirm"><i class="list-icon fa fa-times fa-fw"></i></a></div>';
                $result .= '</div>';

                if ($max_level == 0 || $curr_level != $max_level) {
                    unset($lists[$key]);
                    // 下级节点
                    $children = $this->getNestMenu($lists, $max_level, $value['categoryid'], $curr_level + 1);
                    if ($children != '') {
                        $result .= '<ol class="dd-list">'.$children.'</ol>';
                    }
                }

                $result .= '</li>';
            }
        }
        return $result;
    }
    /**
     * 获取嵌套式节点
     * @param array $lists 原始节点数组
     * @param int $pid 父级id
     * @param int $max_level 最多返回多少层，0为不限制
     * @param int $curr_level 当前层数
     * @author 蔡伟明 <314013107@qq.com>
     * @return string
     */
    private function getNestMenu0($lists = [], $max_level = 0, $pid = 0, $curr_level = 1)
    {
        $result = '';
        foreach ($lists as $key => $value) {
            if ($value['pid'] == $pid) {
                $disable  = $value['status'] == 0 ? 'dd-disable' : '';

                // 组合节点
                $result .= '<li class="dd-item dd3-item '.$disable.'" data-id="'.$value['id'].'">';
                $result .= '<div class="dd-handle dd3-handle">拖拽</div><div class="dd3-content"><i class="'.$value['icon'].'"></i> '.$value['title'];
                if ($value['url_value'] != '') {
                    $result .= '<span class="link"><i class="fa fa-link"></i> '.$value['url_value'].'</span>';
                }
                $result .= '<div class="action">';
                $result .= '<a href="'.url('add', ['module' => $value['module'], 'pid' => $value['id']]).'" data-toggle="tooltip" data-original-title="新增子节点"><i class="list-icon fa fa-plus fa-fw"></i></a><a href="'.url('edit', ['id' => $value['id']]).'" data-toggle="tooltip" data-original-title="编辑"><i class="list-icon fa fa-pencil fa-fw"></i></a>';
                // if ($value['status'] == 0) {
                //     // 启用
                //     $result .= '<a href="javascript:void(0);" data-ids="'.$value['id'].'" class="enable" data-toggle="tooltip" data-original-title="启用"><i class="list-icon fa fa-check-circle-o fa-fw"></i></a>';
                // } else {
                //     // 禁用
                //     $result .= '<a href="javascript:void(0);" data-ids="'.$value['id'].'" class="disable" data-toggle="tooltip" data-original-title="禁用"><i class="list-icon fa fa-ban fa-fw"></i></a>';
                // }
                $result .= '<a href="'.url('delete', ['id' => $value['id'], 'table' => 'admin_menu']).'" data-toggle="tooltip" data-original-title="删除" class="ajax-get confirm"><i class="list-icon fa fa-times fa-fw"></i></a></div>';
                $result .= '</div>';

                if ($max_level == 0 || $curr_level != $max_level) {
                    unset($lists[$key]);
                    // 下级节点
                    $children = $this->getNestMenu($lists, $max_level, $value['id'], $curr_level + 1);
                    if ($children != '') {
                        $result .= '<ol class="dd-list">'.$children.'</ol>';
                    }
                }

                $result .= '</li>';
            }
        }
        return $result;
    }
}
