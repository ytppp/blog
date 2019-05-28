<?php
/**
 * Created by PhpStorm.
 * User: 65100
 * Date: 2018/11/19
 * Time: 19:48
 */

namespace app\common\validate;

use think\Validate;

class Notice extends Validate
{
    protected $rule = [
        'name|公告标题' => [
            'require'     => 'require',
            'length'      => '1, 20',
            'unique'      => 'blog_notice',
            'chsDash' => 'chsDash' // 只能是汉字、字母、数字和下划线_及破折号-
        ],
        'content|公告内容' => [
            'require'     => 'require'
        ],
    ];
}