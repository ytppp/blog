<?php
/**
 * Created by PhpStorm.
 * User: 65100
 * Date: 2019/1/22
 * Time: 21:43
 */

namespace app\admin\controller;

use app\admin\controller\Base;
use think\facade\Request;
use app\common\model\Mood as MoodModel;
use app\common\validate\Mood as MoodValidate;
use app\common\model\User as UserModel;

class Mood extends Base
{
    public function getMoodList()
    {
        $this->isAdminLogined();
        // 获取数据
        $moodList = MoodModel::field('id, content, status, update_time')
            ->where('type', '=', '1')
            ->paginate(10);
        // 模板赋值
        $this->view->assign('title', '心情列表');
        $this->view->assign('moodList', $moodList);
        $this->view->assign('empty', "<h3 class='text-center text-danger'>没有数据</h3>");
        // 渲染模板
        return $this->view->fetch('mood');
    }

    public function updateMood()
    {
        // 初始化数据
        $validate = new MoodValidate;

        if (!Request::isAjax()) {
            return ['status' => -1, 'msg' => '请求类型错误'];
        }

        $moodInfo = Request::param();

        $rs = $validate->check($moodInfo);
        // 验证数据
        if (!$rs) {
            return ['status' => -1, 'msg' => $validate->getError() ];
        }
        if (isset($moodInfo['id']) && '' != $moodInfo['id']) {
            $res = MoodModel::where('id', '=', $moodInfo['id'])->update($moodInfo);
            if ($res) {
                return ['status' => 1, 'msg' => '修改心情成功'];
            } else {
                return ['status' => -1, 'msg' => '修改心情失败,请检查'];
            }
        } else {
            if (MoodModel::create($moodInfo)) {
                return ['status'=>1, 'msg'=>'发布心情成功'];
            } else {
                return ['status'=>-1, 'msg'=>'发布心情失败,请检查'];
            }
        }
    }

    public function deleMood()
    {
        if (!Request::isAjax()) {
            return ['status'=>-1, 'msg'=>'请求类型错误'];
        }
        // 从前台获取数据
        $data = Request::param();
        // 查询条件
        $map[] = ['id', '=', $data['adminId']];
        $map[] = ['password', '=', sha1($data['adminPsw'])];
        $userResult = UserModel::where($map)->find();
        if (is_null($userResult)) {
            return ['status'=>-1, 'msg'=>'密码错误'];
        }
        $res = MoodModel::where('id', '=', $data['moodId'])->delete();
        if (1 == $res) {
            return ['status'=>1, 'msg'=>'已删除该心情'];
        } else {
            return ['status'=>-1, 'msg'=>'删除心情失败,请检查'];
        }
    }

    public function updateMoodStatus()
    {
        if (!Request::isAjax()) {
            return ['status'=>-1, 'msg'=>'请求类型错误'];
        }
        $data = Request::param();
        if (1 == $data['status']) {
            $res = MoodModel::where('id', '=', $data['id'])->update(['status'=>1]);
        } else {
            $res = MoodModel::where('id', '=', $data['id'])->update(['status'=>0]);
        }

        if (1 == $res) {
            return ['status'=>1, 'msg'=>'切换心情状态成功'];
        } else {
            return ['status'=>-1, 'msg'=>'切换心情状态失败,请检查'];
        }
    }
}