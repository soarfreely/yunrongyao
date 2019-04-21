<?php
namespace app\pos\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\pos\model\Company as CompanyModel;
use app\admin\model\Config as ConfigModel;
use app\admin\model\Module as ModuleModel;

/**
 * 系统模块控制器
 * @package app\admin\controller
 */
class System extends Admin
{
    public function index($companyid = 1)
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
            ->setPageTitle('企业信息') // 设置页面标题
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
    /**
     * 系统设置
     * @param string $group 分组
     * @author 蔡伟明 <314013107@qq.com>
     * @return mixed
     */
    public function index1($group = 'base')
    {
        // 保存数据
        if ($this->request->isPost()) {
            $data = $this->request->post();

            if (isset(config('config_group')[$group])) {
                // 查询该分组下所有的配置项名和类型
                $items = ConfigModel::where('group', $group)->where('status', 1)->column('name,type');

                foreach ($items as $name => $type) {
                    if (!isset($data[$name])) {
                        switch ($type) {
                            // 开关
                            case 'switch':
                                $data[$name] = 0;
                                break;
                            case 'checkbox':
                                $data[$name] = '';
                                break;
                        }
                    } else {
                        // 如果值是数组则转换成字符串，适用于复选框等类型
                        if (is_array($data[$name])) {
                            $data[$name] = implode(',', $data[$name]);
                        }
                        switch ($type) {
                            // 开关
                            case 'switch':
                                $data[$name] = 1;
                                break;
                            // 日期时间
                            case 'date':
                            case 'time':
                            case 'datetime':
                                $data[$name] = strtotime($data[$name]);
                                break;
                        }
                    }
                    ConfigModel::where('name', $name)->update(['value' => $data[$name]]);
                }
            } else {
                // 保存模块配置
                if (false === ModuleModel::where('name', $group)->update(['config' => json_encode($data)])) {
                    $this->error('更新失败');
                }
                // 非开发模式，缓存数据
                if (config('develop_mode') == 0) {
                    cache('module_config_'.$group, $data);
                }
            }
            cache('system_config', null);
            // 记录行为
            action_log('system_config_update', 'admin_config', 0, UID, "分组($group)");
            $this->success('更新成功', url('index', ['group' => $group]));
        } else {
            // 配置分组信息
            $list_group = config('config_group');

            // 读取模型配置
            $modules = ModuleModel::where('config', 'neq', '')
                ->where('status', 1)
                ->column('config,title,name', 'name');
            foreach ($modules as $name => $module) {
                $list_group[$name] = $module['title'];
            }

            $tab_list   = [];
            foreach ($list_group as $key => $value) {
                $tab_list[$key]['title'] = $value;
                $tab_list[$key]['url']  = url('index', ['group' => $key]);
            }

            if (isset(config('config_group')[$group])) {
                // 查询条件
                $map['group']  = $group;
                $map['status'] = 1;

                // 数据列表
                $data_list = ConfigModel::where($map)
                    ->order('sort asc,id asc')
                    ->column('name,title,tips,type,value,options,ajax_url,next_items,param,table,level,key,option,ak,format');

                foreach ($data_list as &$value) {
                    // 解析options
                    if ($value['options'] != '') {
                        $value['options'] = parse_attr($value['options']);
                    }
                }

                // 默认模块列表
                if (isset($data_list['home_default_module'])) {
                    $list_module['index'] = '默认';
                    $data_list['home_default_module']['options'] = array_merge($list_module, ModuleModel::getModule());
                }

                // 使用ZBuilder快速创建表单
                return ZBuilder::make('form')
                    ->setPageTitle('系统设置')
                    // ->setTabNav($tab_list, $group)
                    ->setFormItems($data_list)
                    ->fetch();
            } else {
                // 模块配置
                $module_info = ModuleModel::getInfoFromFile($group);
                $config      = $module_info['config'];
                $trigger     = isset($module_info['trigger']) ? $module_info['trigger'] : [];

                // 数据库内的模块信息
                $db_config = ModuleModel::where('name', $group)->value('config');
                $db_config = json_decode($db_config, true);

                // 使用ZBuilder快速创建表单
                return ZBuilder::make('form')
                    ->setPageTitle('模块设置')
                    // ->setTabNav($tab_list, $group)
                    ->addFormItems($config)
                    ->setFormdata($db_config) // 设置表格数据
                    ->setTrigger($trigger) // 设置触发
                    ->fetch();
            }
        }
    }
}