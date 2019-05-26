<?php
/**
 * Created by PhpStorm.
 * User: 65100
 * Date: 2019/5/26
 * Time: 21:05
 */

namespace app\api\controller;

use app\api\controller\Base;
use think\facade\Request;
use think\Db;

class Links extends Base
{
    public function getLinksList()
    {
        $linksList = Db::table('blog_links')->where('status', '=', 1)->select();
        if (!is_null($linksList)) {
            return json([
                'code'  => 1,
                'message' => '获取友链列表成功',
                'data'    => [
                    'links_list' => $linksList
                ]
            ]);
        } else {
            return json([
                'code'  => -1,
                'message' => '获取友链列表失败,请检查'
            ]);
        }
    }
}