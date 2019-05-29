<?php
/**
 * Created by PhpStorm.
 * User: 65100
 * Date: 2019/5/26
 * Time: 16:33
 */

namespace app\api\controller;

use app\api\controller\Base;
use think\facade\Request;
use think\Db;
use app\common\model\Sitelog as SitelogModel;
use app\common\model\SiteAbout as SiteAboutModel;

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
            $this->addSiteLog('更新站点信息成功', 'api/site/updateAllSiteInfo', 1);
            return json([
                'code'    => 1,
                'message' => '更新站点所有信息成功'
            ]);
        } else {
            $this->addSiteLog('更新站点信息失败', 'api/site/updateAllSiteInfo', 0);
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
            $this->addSiteLog('获取站点信息成功', 'api/site/getSiteInfo', 1);
            return json([
                'code'  => 1,
                'message' => '获取站点信息成功',
                'data'    => [
                    'site_info' => $siteInfo,
                    'site_data' => $site_data
                ]
            ]);
        } else {
            $this->addSiteLog('获取站点信息失败', 'api/site/getSiteInfo', 0);
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
            $this->addSiteLog('更改站点状态成功', 'api/site/changeSiteStatus', 1);
            return json([
                'code'    => 1,
                'message' => '更改站点状态成功',
            ]);
        } else {
            $this->addSiteLog('更改站点状态失败', 'api/site/changeSiteStatus', 0);
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
            $this->addSiteLog('获取'.$siteInfo['key'].'站点状态成功', 'api/site/getSiteStatus', 1);
            return json([
                'code'    => 1,
                'message' => '获取站点状态成功',
                'data'    => $res
            ]);
        } else {
            $this->addSiteLog('获取'.$siteInfo['key'].'站点状态失败', 'api/site/getSiteStatus', 0);
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
            $this->addSiteLog('更改 name = '.$siteInfo['key'].' 站点状态失败', 'api/site/setSiteStatus', 0);
            return json([
                'code'    => -1,
                'message' => '更改站点状态失败,请检查',
            ]);
        }
    }

    // 获得站点记录列表
    public function getSitelogList()
    {
        // 全局查询条件
        $map = [];

        $map[] = ['status', '=', 1];
        // 获取数据
        $sitelogList = SitelogModel::where($map)->order('create_time', 'desc')->select();
        if (!is_null($sitelogList)) {
            return json([
                'code'    => 1,
                'message' => '获取站点记录列表成功',
                'data'    => [
                    'sitelog_list' => $sitelogList
                ]
            ]);
        } else {
            return json([
                'code'    => -1,
                'message' => '获取站点记录列表失败,请检查'
            ]);
        }
    }

    // 获得站点关于信息
    public function getSiteAboutInfo()
    {
        // 全局查询条件
        $map = [];

        $map[] = ['status', '=', 1];
        $map[] = ['id', '=', 1];
        // 获取数据
        $siteAboutObj = SiteAboutModel::where($map)->find();
        if (!is_null($siteAboutObj)) {
            return json([
                'code'    => 1,
                'message' => '获取站点关于信息成功',
                'data'    => [
                    'site_about_obj' => $siteAboutObj
                ]
            ]);
        } else {
            $this->addSiteLog('获取 id = 1 的站点关于信息失败', 'api/site/getSiteAboutInfo', 0);
            return json([
                'code'    => -1,
                'message' => '获取站点关于信息失败,请检查'
            ]);
        }
    }

    // 储存站点关注信息
    public function saveSiteAboutInfo()
    {
        $siteAboutInfo = Request::param();
        $res = SiteAboutModel::where('id', '=', $siteAboutInfo['id'])->update($siteAboutInfo);
        if ($res) {
            $this->addSiteLog('更改站点关于信息成功', 'api/site/saveSiteAboutInfo', 1);
            return json([
                'code'    => 1,
                'message' => '更改站点关于信息成功',
            ]);
        } else {
            $this->addSiteLog('更改站点关于信息失败', 'api/site/saveSiteAboutInfo', 0);
            return json([
                'code'    => -1,
                'message' => '更改站点关于信息失败,请检查',
            ]);
        }
    }
}