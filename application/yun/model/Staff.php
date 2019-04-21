<?php 
namespace app\pos\model;

use think\model;
use think\Db;
use app\pos\model\User;

class Staff extends Model 
{
	protected $table = "__POS_STAFF__";

	// tp5.1
	// public function userInfo()
	// {
	// 	return $this->belongsTo('User','uid');
	// }
	/**
	 * 员工列表
	 * @Author  <362431947@qq.com>
	 * @Date    2018-07-24
	 * @Version [version]
	 * @return  [type]             [description]
	 */
	public function index()
	{
		$uid = session('user_auth.uid');
		$companyid = session('user_auth.companyid');

		$where = " au.companyid = $companyid ";
		if(1 == $uid){
			// 超级管理员 
			$where = "1 = 1";
		}
		$where .= " AND founder = 0 ";

		$sql = "SELECT au.*,ps.storeid 
				FROM mini_admin_user au 
				JOIN mini_pos_staff ps ON au.id = ps.uid
				WHERE $where";
		$staff  = Db::query($sql);

		return $staff;
	}

	public function staff()
	{
		$staff = $this::where('companyid',session('user_auth.companyid'))->select();
		return $staff;

	}
}
