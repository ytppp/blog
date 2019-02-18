<?php
/**
 * Created by PhpStorm.
 * User: 65100
 * Date: 2019/1/19
 * Time: 9:42
 */

namespace app\common\validate;

use think\Validate;

class ArticleAuthor extends Validate
{
    protected $rule = [
        'name|文章类型' => [
            'require'     => 'require',
            'length'      => '2, 6',
            'unique'      => 'blog_article_author',
            'chsAlpha' => 'chsAlpha' // 仅允许使用汉字、字母
        ]
    ];
}