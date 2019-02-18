<?php
/**
 * Created by PhpStorm.
 * User: 65100
 * Date: 2019/1/22
 * Time: 20:22
 */

namespace app\common\validate;

use think\Validate;

class Timeaxis extends Validate
{
    protected $rule = [
        'content|公告标题' => [
            'require'     => 'require',
            'unique'      => 'blog_site_timeaxis',
        ],
    ];
}