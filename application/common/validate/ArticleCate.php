<?php
/**
 * Created by PhpStorm.
 * User: 65100
 * Date: 2019/1/19
 * Time: 17:37
 */

namespace app\common\validate;

use think\Validate;

class ArticleCate extends Validate
{
    protected $rule = [
        'name|文章栏目' => [
            'require'     => 'require',
            'length'      => '2, 15',
            'unique'      => 'blog_article_cate',
            'chsAlpha'    => 'chsAlpha' // 仅允许使用汉字、字母
        ]
    ];
}