<?php
/**
 * Created by PhpStorm.
 * User: 65100
 * Date: 2018/11/19
 * Time: 14:46
 */

namespace app\common\validate;

use think\Validate;

class User extends Validate
{
    protected $rule = [
        'name|昵称' => [
            'require'     => 'require',
            'length'      => '1, 10',
            'alphaDash' => 'alphaDash' // 仅允许使用字母,数字,_,-
        ],
        'mobile|电话' => [
            'mobile'  => 'mobile',
            'unique'  => 'blog_user',
        ],
        'email|邮箱' => [
            'email'  => 'email',
            'unique'  => 'blog_user',
        ]
    ];
}