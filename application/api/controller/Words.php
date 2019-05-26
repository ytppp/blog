<?php
/**
 * Created by PhpStorm.
 * User: 65100
 * Date: 2019/5/26
 * Time: 20:45
 */

namespace app\api\controller;

use app\api\controller\Base;
use think\facade\Request;
use think\Db;

class Words extends Base
{
    public function getWordsList()
    {
        $wordsInfo = Request::param();
        $wordsList = Db::table('blog_words')
            ->where('type', '=', $wordsInfo['type'])
            ->select();
        if (!is_null($wordsList)) {
            return json([
                'code'  => 1,
                'message' => '获取列表成功',
                'data'    => [
                    'words_list' => $wordsList
                ]
            ]);
        } else {
            return json([
                'code'  => -1,
                'message' => '获取列表失败,请检查'
            ]);
        }
    }
}