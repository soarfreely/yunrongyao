<?php
namespace app\pos\model;

use think\Model;
use think\Request;

/**
 * 分类模型
 * @package app\admin\model
 */
class Category extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__POS_CATEGORY__';

    /**
     * 通过分类id获取该分类的子分类
     * @Author  <362431947@qq.com>
     * @Date    2018-08-06
     * @Version [version]
     */
    public function ChildrenCategory($categoryid)
    {
    	$category_list = [];
    	$categoryid_array = [];

    	if(! $categoryid){
    		return [];
    	}

		$categoryid_array = $this::where('parentid',$categoryid)->column('categoryid');
    	if($categoryid_array){
    		$list = [];
    		foreach ($categoryid_array as $key => $cateid) {
    			$list = $this->ChildrenCategory($cateid);
    			$category_list = array_merge($category_list,$list);
    		}
    	}

    	return array_unique(array_merge($categoryid_array,$category_list,[$categoryid]));
    }
}
