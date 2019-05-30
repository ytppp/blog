<?php
/**
 * Created by PhpStorm.
 * User: 65100
 * Date: 2019/1/20
 * Time: 10:05
 */

namespace app\common\model;


namespace app\common\model;

use think\Model;

class Article extends Model
{
    // 主键
    protected $pk = 'id';
    // 数据表
    protected $table = 'blog_article';
    // 自动时间戳
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time'; // 创建时间
    protected $updateTime = 'update_time'; // 更新时间
    protected $dateFormat = 'Y-m-d h:m';

    // 开启自动设置
    protected $auto = []; // 无论是新增还是更新都要设置的字段
    // 仅新增的有效
    protected $insert = [
        'create_time',
        'status'   => 1,
        'is_draft' => 1,
        'is_top'   => 0,
        'is_hot'   => 0,
        'pv'       => 1,
        'save_num' => 1,
        'like_num' => 1
    ];
    // 仅更新的时候有效
    protected $update = ['update_time'];

    /*protected function getStatusAttr($value, $data)
    {
        $arr = [0 => '私密', 1 => '公开'];
        return $arr[$data['status']];
    }

    protected function getIsDraftAttr($value, $data)
    {
        $arr = [0 => '发表', 1 => '草稿'];
        return $arr[$data['is_draft']];
    }

    protected function getIsTopAttr($value, $data)
    {
        $arr = [0 => '非置顶', 1 => '置顶'];
        return $arr[$data['is_top']];
    }

    protected function getIsHotAttr($value, $data)
    {
        $arr = [0 => '非热门', 1 => '热门'];
        return $arr[$data['is_hot']];
    }*/

    // 关联ArticleCate模型
    public function articleCate()
    {
        return $this->hasOne('ArticleCate', 'id', 'cate_id');
    }
}