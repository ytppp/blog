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
use app\common\model\Mood as MoodModel;

class Mood extends Base
{
    public function index()
    {
        $moodList = MoodModel::field('content, position, status, create_time')
            ->where('type', '=', '1')
            ->paginate('10');
        $this->view->assign('moodList', $moodList);
        $this->view->assign('empty', "<h3 class='text-center text-danger'>没有数据</h3>");
        return $this->view->fetch('index', ['title'=>'杨庭培的个人博客']);
    }
}