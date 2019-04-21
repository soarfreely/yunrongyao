<?php

namespace app\pos\validate;

use think\Validate;


class Staff extends Validate
{
    //定义验证规则
    protected $rule = [
        'username|用户名' => 'require',
        'role|角色'      => 'require',
        // 'email|邮箱'     => 'email|unique:admin_user',
        'mobile|手机号'   => 'regex:^1\d{10}|unique:admin_user',
    ];

    //定义验证提示
    protected $message = [
        'username.require' => '请输入用户名',
        'email.require'    => '邮箱不能为空',
        'email.email'      => '邮箱格式不正确',
        'email.unique'     => '该邮箱已存在',
        'password.require' => '密码不能为空',
        'password.length'  => '密码长度6-20位',
        'mobile.regex'     => '手机号不正确',
    ];

    //定义验证场景
    protected $scene = [
        //更新
        'update'  =>  ['email', 'password' => 'length:6,20', 'mobile', 'role'],
        //登录
        // 'signin'  =>  ['username' => 'require', 'password' => 'require'],
    ];
}
