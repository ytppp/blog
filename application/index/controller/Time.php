<?php
/**
 * Created by PhpStorm.
 * User: 65100
 * Date: 2019/1/23
 * Time: 9:42
 */

namespace app\index\controller;

use app\index\controller\Base;
use think\facade\Request;
use app\common\model\Timeaxis as TimeaxisModel;

class Time extends Base
{
    public function index()
    {
        // 获取数据
        $axisList = TimeaxisModel::field('id, content, create_time')
            ->paginate(10);
        // 模板赋值
        $this->view->assign('axisList', $axisList);
        $this->view->assign('empty', "<h3 class='text-center text-danger'>没有数据</h3>");
        // 渲染模板
        return $this->view->fetch('index');
    }
}