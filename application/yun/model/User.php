<?php 

namespace app\pos\model;

use app\pos\model\Staff;
use think\Model;

class User extends Model 
{
	protected $tableName = "__ADMIN_USER__";

	// tp5.1
	// public function staffInfo()
	// {
	// 	return $this->hasOne('Staff');
	// }
}