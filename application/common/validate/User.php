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
            'chsAlphaNum' => 'chsAlphaNum' // 仅允许使用汉字、字母、数字
        ],
        'mobile|电话' => [
            'require' => 'require',
            'mobile'  => 'mobile',
            'unique'  => 'blog_user',
        ]
    ];
}