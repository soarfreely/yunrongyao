<?php 

namespace app\pos\validate;

use think\Validate;

/**
 * 企业验证器
 */
class Company extends Validate 
{
	/**
	 * 定义规则
	 */
	protected $rule = [
		'company|企业名称'    => 'require',
		'goods|企业简称'      => 'require',
		'corporate|企业法人'  => 'require',
		// 'license|营业执照'    => 'require',
		'phone|电话'         => 'require',
		'detail|地址'        => 'require',
	];

	/**
	 * 定义提示消息
	 */
	protected $message = [
		'company.require'    => '请输入企业名称',
		'goods.require'      => '请输入简称',
		'corporate.require'  => '请输入企业法人',
		// 'license.require'    => '请上传营业执照',
		'phone.require'      => '请输入联系电话',
		'detail.detail'      => '请输入详细地址',

	];

	/**
	 * 定义场景
	 */
	protected $scene = [
		'update'  => ['phone','detail'],
	];
}