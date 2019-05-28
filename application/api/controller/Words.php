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
use app\common\model\Words as WordsModel;

class Words extends Base
{
    public function getWordsList()
    {
        $wordsInfo = Request::param();
        $wordsList = WordsModel::where('type', '=', $wordsInfo['type'])
            ->order('create_time', 'desc')
            ->select();
        if (!is_null($wordsList)) {
            $this->addSiteLog('获取blog_words表中'.$wordsInfo['type'].'列表成功', 'api/words/getWordsList', 1);
            return json([
                'code'  => 1,
                'message' => '获取列表成功',
                'data'    => [
                    'words_list' => $wordsList
                ]
            ]);
        } else {
            $this->addSiteLog('获取blog_words表中'.$wordsInfo['type'].'列表失败', 'api/words/getWordsList', 0);
            return json([
                'code'  => -1,
                'message' => '获取列表失败,请检查'
            ]);
        }
    }
}