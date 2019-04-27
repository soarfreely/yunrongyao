<?php
namespace app\pos\model;

use think\Model;

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
     * @param integer $id 分类id
     * @Date    2018-08-06
     * @return array
     * @Version [version]
     */
    public function ChildrenCategory($id)
    {
    	$category_list = [];

    	if(! $id){
    		return [];
    	}

		$category_ids = $this::where('parent_id',$id)->column('id');
    	if($category_ids){
    		foreach ($category_ids as $key => $cateId) {
    			$list = $this->ChildrenCategory($cateId);
    			$category_list = array_merge($category_list,$list);
    		}
    	}

    	return array_unique(array_merge($category_ids,$category_list,[$id]));
    }
}
