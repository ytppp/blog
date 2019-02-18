<?php
/**
 * Created by PhpStorm.
 * User: 65100
 * Date: 2019/1/23
 * Time: 9:48
 */

namespace app\index\controller;

use app\index\controller\Base;

class About extends Base
{
    public function index()
    {
        return $this->view->fetch('index', ['title'=>'杨庭培的个人博客']);
    }
}