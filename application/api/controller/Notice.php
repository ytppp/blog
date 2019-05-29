<?php
/**
 * Created by PhpStorm.
 * User: 65100
 * Date: 2018/11/19
 * Time: 19:16
 */

namespace app\api\controller;

use app\api\controller\Base;
use think\facade\Request;
use app\common\model\Notice as NoticeModel;
use app\common\validate\Notice as NoticeValidate;
use think\Db;

class Notice extends Base
{

    // 获得公告列表
    public function getNoticeList()
    {
        // 全局查询条件
        $map = [];

        $map[] = ['id', '=', 1];
        // 获取数据
        $noticeList = NoticeModel::where($map)->order('create_time', 'desc')->select();
        return json([
            'code'     => 1,
            'message'  => '获取公告成功',
            'data'     => [
                'notice_list' => $noticeList
            ]
        ]);
    }

    // 处理公告修改
    public function modifyNotice() {
        $noticeInfo = Request::param();

        $validate = new NoticeValidate;
        $rs = $validate->check($noticeInfo);
        // 验证数据
        if (!$rs) {
            $this->addSiteLog($validate->getError(), 'api/article/modifyNotice', 0);
            return json([
                'code'    => -1,
                'message' => $validate->getError()
            ]);
        }

        $res = Db::table('blog_notice')->where('id','=', $noticeInfo['id'])->update($noticeInfo);
        if ($res) {
            return json([
                'code'    => 1,
                'message' => '修改公告成功'
            ]);
        } else {
            $this->addSiteLog('更改 id = 1 的公告内容失败', 'api/article/modifyNotice', 0);
            return json([
                'code'    => -1,
                'message' => '修改公告失败'
            ]);
        }
    }

    // 恢复公告
    public function changeNoticeStatus()
    {
        $noticeInfo = Request::param();
        $noticeInfoUpdate = [
            $noticeInfo['key'] => $noticeInfo['value']
        ];
        $res = NoticeModel::where('id', '=', $noticeInfo['id'])->update($noticeInfoUpdate);
        if ($res) {
            return json([
                'code'    => 1,
                'message' => '更改公告状态成功',
            ]);
        } else {
            $this->addSiteLog('更改 id = 1 的公告状态失败', 'api/article/changeArticleStatus', 0);
            return json([
                'code'    => -1,
                'message' => '更改公告状态失败,请检查',
            ]);
        }
    }

}