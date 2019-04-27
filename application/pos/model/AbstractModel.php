<?php
/**
 * Desc:
 * User: <zhangxiang_php@vchangyi.com>
 * Date: 19-4-27 Time: 下午5:06
 */
namespace app\pos\model;

class AbstractModel extends \think\Model
{
    protected $condition = [];

    public function __construct($data = [])
    {
        $this->condition = $this->basicCondition();

        parent::__construct($data);
    }

    /**
     * basicCondition
     * User: <zhangxiang_php@vchangyi.com>
     * Date: 19-4-27 Time: 下午4:55
     */
    private function basicCondition()
    {
        return  [
            'company_id' => session('user_auth.company_id'),
        ];
    }

}