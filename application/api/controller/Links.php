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
use app\common\model\Links as LinksModel;

class Links extends Base
{
    public function getLinksList()
    {
        $map = [];
        $status = Request::param('status');
        if ('' != $status) {
            if (1 == $status) {
                $map[] = ['status', '=', 1];
            } else if (0 == $status) {
                $map[] = ['status', '=', 0];
            }
        }
        $linksList = LinksModel::where($map)->select();
        if (!is_null($linksList)) {
            $this->addSiteLog('获取友情链接列表数据成功', 'api/links/getLinksList', 1);
            return json([
                'code'  => 1,
                'message' => '获取友链列表成功',
                'data'    => [
                    'links_list' => $linksList
                ]
            ]);
        } else {
            $this->addSiteLog('获取友情链接列表数据失败', 'api/links/getLinksList', 0);
            return json([
                'code'  => -1,
                'message' => '获取友链列表失败,请检查'
            ]);
        }
    }

    // 删除友情链接
    public function deleteLinks()
    {
        $id = Request::param('id');
        $res = LinksModel::where('id', '=', $id)->delete();
        if (1 == $res) {
            $this->addSiteLog('删除id='.$id.'的友情链接成功', 'api/links/deleteLinks', 1);
            return json([
                'code'  => 1,
                'message' => '已删除该友情链接'
            ]);
        } else {
            $this->addSiteLog('删除id='.$id.'的友情链接失败', 'api/links/deleteLinks', 0);
            return json([
                'code'  => -1,
                'message' => '删除友情链接失败,请检查'
            ]);
        }
    }

    // 更改友情链接状态
    public function changeLinkStatus()
    {
        $info = Request::param();
        $infoUpdate = [
            $info['key'] => $info['value']
        ];
        $res = LinksModel::where('id', '=', $info['id'])->update($infoUpdate);
        if ($res) {
            $this->addSiteLog('更改blog_links表中id='.$info['id'].'的友情链接的'.$info['key'].'为'.$info['value'], 'api/links/changeLinkStatus', 1);
            return json([
                'code'    => 1,
                'message' => '更改友情链接状态成功',
            ]);
        } else {
            $this->addSiteLog('更改blog_links表中id='.$info['id'].'的友情链接的状态失败', 'api/links/changeLinkStatus', 0);
            return json([
                'code'    => -1,
                'message' => '更改友情链接状态失败,请检查',
            ]);
        }
    }
}