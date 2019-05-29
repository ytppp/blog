<?php
/**
 * Created by PhpStorm.
 * User: 65100
 * Date: 2019/1/18
 * Time: 11:48
 */
namespace app\common\model;

use think\Model;

class ArticleAuthor extends Model
{
    // 主键
    protected $pk = 'id';
    // 数据表
    protected $table = 'blog_article_author';
    // 自动时间戳
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time'; // 创建时间
    protected $updateTime = 'update_time'; // 更新时间
    protected $dateFormat = 'Y-m-d h:m';

    // 开启自动设置
    protected $auto = []; // 无论是新增还是更新都要设置的字段
    // 仅新增的有效
    protected $insert = ['create_time', 'status'=>1];
    // 仅更新的时候有效
    protected $update = ['update_time'];
}