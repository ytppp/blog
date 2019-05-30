<?php
/**
 * Created by PhpStorm.
 * User: 65100
 * Date: 2019/1/20
 * Time: 10:06
 */

namespace app\common\validate;

use think\Validate;

class Article extends Validate
{
    protected $rule = [
        'title|标题' => [
            'require'     => 'require',
            'length'      => '2, 30',
            'unique'      => 'blog_article',
        ],
        'avatar|封面图片' => [
            'require' => 'require',
        ],
        'cate_id|栏目' => [
            'require' => 'require',
        ],
        'author_id|作者' => [
            'require' => 'require',
        ],
        'content|内容' => [
            'require' => 'require'
        ],
        'url_address|转载地址' => [
            'url' => 'url'
        ]
    ];
}