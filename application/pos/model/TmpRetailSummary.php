<?php
namespace app\pos\model;

use think\Model;

class TmpRetailSummary extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__POS_TMP_RETAIL_SUMMARY__';
    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
}
