<?php
/**
 * Created by PhpStorm.
 * User: 65100
 * Date: 2019/1/22
 * Time: 21:45
 */

namespace app\common\validate;

use think\Validate;

class Mood extends Validate
{
    protected $rule = [
        'content|内容' => [
            'require' => 'require'
        ],
    ];
}