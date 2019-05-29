<?php
/**
 * Created by PhpStorm.
 * User: 65100
 * Date: 2019/5/26
 * Time: 1:48
 */

namespace app\api\controller;

use app\api\controller\Base;
use think\facade\Request;
use app\common\model\Mood as MoodModel;
use app\common\validate\Mood as MoodValidate;

class Mood extends Base
{
    // 获取心情列表
    public function getMoodList()
    {
        // 全局查询条件
        $map = [];

        $wordsInfo = Request::param();
        $status = $wordsInfo['status'];
        $map[] = ['type', '=', $wordsInfo['type']];
        // 封装查询条件
        if ('' != $status) {
            if (1 == $status) {
                $map[] = ['status', '=', 1];
            } else if (0 == $status) {
                $map[] = ['status', '=', 0];
            }
        }
        // 获取数据
        $wordsList = MoodModel::field('id, content, position, status, create_time')
            ->where($map)
            ->order('create_time', 'desc')
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
            $this->addSiteLog('获取blog_words表中 type = 1 的列表失败', 'api/mood/getMoodList', 0);
            return json([
                'code'  => -1,
                'message' => '获取列表失败,请检查'
            ]);
        }
    }

    // 删除心情
    public function deleteMood () {
        $moodInfo = Request::param();
        $res = MoodModel::where('id', '=', $moodInfo['id'])->delete();
        if (1 == $res) {
            return json([
                'code'  => 1,
                'message' => '已删除该心情'
            ]);
        } else {
            $this->addSiteLog('删除blog_words表中id='.$moodInfo['id'].'的数据项失败', 'api/mood/deleteMood', 0);
            return json([
                'code'  => -1,
                'message' => '删除心情失败,请检查'
            ]);
        }
    }

    // 更改心情状态
    public function changeMoodStatus () {
        $moodInfo = Request::param();
        $moodInfoUpdate = [
            $moodInfo['key'] => $moodInfo['value']
        ];
        $res = MoodModel::where('id', '=', $moodInfo['id'])->update($moodInfoUpdate);
        if ($res) {
            return json([
                'code'    => 1,
                'message' => '更改心情状态成功',
            ]);
        } else {
            $this->addSiteLog('更改blog_words表中id='.$moodInfo['id'].'的数据项失败', 'api/mood/changeMoodStatus', 0);
            return json([
                'code'    => -1,
                'message' => '更改心情状态失败,请检查',
            ]);
        }
    }

    // 增加心情
    public function addMood () {
        // 初始化数据
        $validate = new MoodValidate;

        $moodInfo = Request::param();

        $rs = $validate->check($moodInfo);
        // 验证数据
        if (!$rs) {
            $this->addSiteLog($validate->getError(), 'api/mood/addMood', 0);
            return json([
                'code'    => -1,
                'message' => $validate->getError()
            ]);
        }
        if ($mood = MoodModel::create($moodInfo)) {
            return json([
                'code'    => 1,
                'message' => '增加心情成功',
                'data'    => [
                    'id'          => $mood->id,
                    'content'     => $mood->content,
                    'position'    => $mood->position,
                    'status'      => $mood->status,
                    'create_time' => $mood->create_time
                ]
            ]);
        } else {
            $this->addSiteLog('增加心情失败', 'api/mood/addMood', 0);
            return json([
                'code'    => -1,
                'message' => '增加心情失败,请检查'
            ]);
        }
    }
}