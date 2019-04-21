<?php
namespace app\pos\model;

use think\Model;
use think\Request;

/**
 * 企业模型
 * @package app\admin\model
 */
class Company extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__POS_COMPANY__';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;

}
