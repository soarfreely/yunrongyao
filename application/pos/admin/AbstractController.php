<?php
/**
 * Desc:
 * User: <zhangxiang_php@vchangyi.com>
 * Date: 19-4-27 Time: 下午4:59
 */
namespace app\pos\Admin;

use app\admin\controller\Admin;
use think\App;

class AbstractController extends Admin
{
    /**
     * @var array
     */
    protected $condition = [];

    /**
     * AbstractController constructor.
     * @param App|null $app
     */
    public function __construct(App $app = null)
    {
        $this->condition = $this->baseCondition();

        parent::__construct($app);
    }

    /**
     * baseCondition
     * User: <zhangxiang_php@vchangyi.com>
     * @return array
     * Date: 19-4-27 Time: 下午5:17
     */
    private function baseCondition()
    {
        return [
            'company_id' => session('user_auth.company_id'),
        ];
    }
}