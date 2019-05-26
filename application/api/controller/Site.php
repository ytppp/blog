<?php
/**
 * Created by PhpStorm.
 * User: 65100
 * Date: 2019/5/26
 * Time: 16:33
 */

namespace app\api\controller;

use app\admin\controller\Base;
use think\facade\Request;
use think\Db;

class Site extends Base
{
    /**
     * 更新站点信息
     * @return \think\response\Json
     */
    public function updateAllSiteInfo()
    {
        $siteInfo = Request::param();
        $res = Db::table('blog_site')
            ->where('id', '=', 1)
            ->update($siteInfo);
        if ($res) {
            return json([
                'code'    => 1,
                'message' => '更新站点所有信息成功'
            ]);
        } else {
            return json([
                'code'    => -1,
                'message' => '更新站点所有信息失败,请检查'
            ]);
        }
    }

    /**
     * 获取站点信息列表
     * @return \think\response\Json
     */
    public function getSiteInfo()
    {
        $reg_num = Db::table('blog_user')->where('is_admin', '=', 0)->count();
        $online_num = Db::table('blog_user')->where([
            'is_online' => 1,
            'is_admin'  => 0
        ])->count();
        $article_num = Db::table('blog_article')->where('is_draft', '=', 1)->count();
        $words_num = Db::table('blog_words')->where('type', '=', 2)->count();
        $feedback_num = Db::table('blog_words')->where('type', '=', 0)->count();
        $mood_num = Db::table('blog_words')->where('type', '=', 1)->count();
        $site_data = [
            'reg_num' => $reg_num,
            'online_num' => $online_num,
            'article_num' => $article_num,
            'words_num' => $words_num,
            'feedback_num' => $feedback_num,
            'moods_num' => $mood_num,
        ];
        $siteInfo = Db::table('blog_site')
            ->where('id', '=', 1)
            ->find();
        if ($siteInfo) {
            return json([
                'code'  => 1,
                'message' => '获取站点信息成功',
                'data'    => [
                    'site_info' => $siteInfo,
                    'site_data' => $site_data
                ]
            ]);
        } else {
            return json([
                'code'    => -1,
                'message' => '获取站点信息失败,请检查'
            ]);
        }
    }

    /**
     * 更改站点状态
     * @return \think\response\Json
     */
    public function changeSiteStatus()
    {
        $siteInfo = Request::param();
        $res = Db::table('blog_site')->where('id', '=', 1)->update($siteInfo);
        if ($res) {
            return json([
                'code'    => 1,
                'message' => '更改站点状态成功',
            ]);
        } else {
            return json([
                'code'    => -1,
                'message' => '更改站点状态失败,请检查',
            ]);
        }
    }

    /**
     * 获得一个或多个站点状态
     * @return \think\response\Json
     */
    public function getSiteStatus()
    {
        $siteInfo = Request::param();
        $res = Db::table('blog_site')->field($siteInfo['key'])->where('id', '=', 1)->find();
        if ($res) {
            return json([
                'code'    => 1,
                'message' => '获取站点状态成功',
                'data'    => $res
            ]);
        } else {
            return json([
                'code'    => -1,
                'message' => '获取站点状态失败,请检查',
            ]);
        }
    }

    /**
     * 更改一个站点状态
     * @return \think\response\Json
     */
    public function setSiteStatus ()
    {
        $siteInfo = Request::param();
        $res = Db::table('blog_site')->where('id', '=', 1)->update([$siteInfo['key'] => $siteInfo['value']]);
        if ($res) {
            return json([
                'code'    => 1,
                'message' => '更改站点状态成功',
            ]);
        } else {
            return json([
                'code'    => -1,
                'message' => '更改站点状态失败,请检查',
            ]);
        }
    }
}