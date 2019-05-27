<?php
/**
 * Created by PhpStorm.
 * User: 65100
 * Date: 2019/5/28
 * Time: 2:04
 */

namespace app\api\controller;

use app\api\controller\Base;
use think\facade\Request;
use app\common\model\Timeaxis as TimeaxisModel;

class Timeaxis extends Base
{
    // 获取时间轴
    public function getTimeaxisList()
    {
        // 全局查询条件
        $map = [];
        $map[] = ['status', '=', 1];

        // 获取数据
        $timeaxisList = TimeaxisModel::field('id, content, create_time')
            ->where($map)
            ->select();
        if (!is_null($timeaxisList)) {
            return json([
                'code'  => 1,
                'message' => '获取时间轴列表成功',
                'data'    => [
                    'timeaxis_list' => $timeaxisList
                ]
            ]);
        } else {
            return json([
                'code'  => -1,
                'message' => '获取时间轴列表失败,请检查'
            ]);
        }
    }

    // 增加时间轴
    public function addTimeaxis () {

        $timeaxisInfo = Request::param();

        if ($timeaxis = TimeaxisModel::create($timeaxisInfo)) {
            return json([
                'code'    => 1,
                'message' => '增加站点记录成功',
                'data'    => [
                    'id'          => $timeaxis->id,
                    'content'     => $timeaxis->content,
                    'create_time' => $timeaxis->create_time
                ]
            ]);
        } else {
            return json([
                'code'    => -1,
                'message' => '增加站点记录失败,请检查'
            ]);
        }
    }

    public function deleteTimeaxis () {
        $timeaxisId = Request::param('id');
        $res = TimeaxisModel::where('id', '=', $timeaxisId)->delete();
        if (1 == $res) {
            return json([
                'code'  => 1,
                'message' => '已删除该站点记录'
            ]);
        } else {
            return json([
                'code'  => -1,
                'message' => '删除站点记录失败,请检查'
            ]);
        }
    }
}