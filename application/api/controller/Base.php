<?php
/**
 * Created by PhpStorm.
 * User: 65100
 * Date: 2019/3/18
 * Time: 8:57
 */

namespace app\api\controller;

use think\Controller;
use app\common\model\Sitelog as SitelogModel;

class Base extends Controller
{
    /**
     * 初始化方法
     */
    protected function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
    }

    // 写入站点记录
    public function addSiteLog($logText, $api, $success)
    {
        $siteInfo = [
            'log' => $logText,
            'api' => $api,
            'is_success' => $success,
            'status' => 1,
        ];
        SitelogModel::create($siteInfo);
    }
}