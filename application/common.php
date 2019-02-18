<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
use think\Db;

// 查询文章栏目名
if (!function_exists('getArtCateName')) {
    function getArtCateName ($id) {
        return Db::table('blog_article_cate')
            ->where('id', '=', $id)
            ->value('name');
    }
}

// 查询文章作者名
if (!function_exists('getArtAuthorName')) {
    function getArtAuthorName ($id) {
        return Db::table('blog_article_author')
            ->where('id', '=', $id)
            ->value('name');
    }
}

// 过滤文章长度
if (!function_exists('filterArtContent')) {
    function filterArtContent ($content) {
        return mb_substr(strip_tags($content), 0, 30) . '......';
    }
}