<?php
/**
 * Created by PhpStorm.
 * User: 65100
 * Date: 2018/12/5
 * Time: 9:39
 */

namespace app\admin\controller;

use app\admin\controller\Base;

class Index extends Base
{
    public function index()
    {
        $this->isAdminLogined();
        return $this->view->fetch('index', ['title' => '博客后台管理系统']);
    }
}