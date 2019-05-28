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
        $map[] = ['type', '=', 1];

        $status = Request::param('status');

        // 封装查询条件
        if ('' != $status) {
            if (1 == $status) {
                $map[] = ['status', '=', 1];
            } else if (0 == $status) {
                $map[] = ['status', '=', 0];
            }
        }
        // 获取数据
        $moodList = MoodModel::field('id, content, position, status, create_time')
            ->where($map)
            ->select();
        if (!is_null($moodList)) {
            $this->addSiteLog('获取心情列表成功', 'api/mood/getMoodList', 1);
            return json([
                'code'  => 1,
                'message' => '获取心情列表成功',
                'data'    => [
                    'mood_list' => $moodList
                ]
            ]);
        } else {
            $this->addSiteLog('获取心情列表失败', 'api/mood/getMoodList', 0);
            return json([
                'code'  => -1,
                'message' => '获取心情列表失败,请检查'
            ]);
        }
    }

    // 删除心情
    public function deleteMood () {
        $moodInfo = Request::param();
        $res = MoodModel::where('id', '=', $moodInfo['id'])->delete();
        if (1 == $res) {
            $this->addSiteLog('删除id为'.$moodInfo['id'].'的心情成功', 'api/mood/deleteMood', 1);
            return json([
                'code'  => 1,
                'message' => '已删除该心情'
            ]);
        } else {
            $this->addSiteLog('删除id为'.$moodInfo['id'].'的心情失败', 'api/mood/deleteMood', 0);
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
            $this->addSiteLog('更改id为'.$moodInfo['id'].'的心情状态成功', 'api/mood/changeMoodStatus', 1);
            return json([
                'code'    => 1,
                'message' => '更改心情状态成功',
            ]);
        } else {
            $this->addSiteLog('更改id为'.$moodInfo['id'].'的心情状态失败', 'api/mood/changeMoodStatus', 0);
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
            // 'id, content, position, status, create_time'
            $this->addSiteLog('增加id为'.$mood->id.'的心情成功', 'api/mood/addMood', 1);
            return json([
                'code'    => 1,
                'message' => '增加心情失败',
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