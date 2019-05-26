<?php
/**
 * Created by PhpStorm.
 * User: 65100
 * Date: 2018/12/5
 * Time: 9:39
 */

namespace app\admin\controller;

use app\admin\controller\Base;
use think\facade\Session;
use think\facade\Request;
use app\common\model\User as UserModel;

class User extends Base
{
    // 前往管理员登录页面
    public function goLoginPage()
    {
        return $this->view->fetch('login', [
            'title'=>'管理员登录',
            'keywords'=>'管理员登录',
            'description' => '个人博客管理系统的登录界面'
        ]);
    }

    // 管理员登录检查
    public function checkAdminLogin()
    {
        $userResult = null;
        $map = [];
        // 自定义验证规则
        $rule = [
            'email|邮箱' => [
                'email'  => 'email',
            ],
            'mobile|手机' => [
                'mobile'  => 'mobile',
            ],
            'password|密码' => [
                'length'    => '6, 20',
                'alphaDash' => 'alphaDash', // 仅允许使用字母,数字,_,-
            ]
        ];
        // 获取数据
        $data = Request::param();
        $res = $this->validate($data, $rule); // 验证
        if (true !== $res) {
            return json(['status' => -1, 'msg' => $res]);
        }

        // 查询条件
        $map[] = ['password', '=', sha1($data['password'])];
        $map[] = ['mobile', '=', $data['mobile']];
        $userResult = UserModel::where($map)->find();

        if (!is_null($userResult)) {
            UserModel::where('id', '=', $userResult['id'])->update(['is_login'=>1]);
            Session::set('admin_id', $userResult['id']);
            return json(['status' => 1, 'msg' => '登录成功']);
        } else {
            return json(['status' => -1, 'msg' => '账号或密码错误，请重新登录']);
        }
    }

    // 注销登录
    public function logout()
    {
        $id = Session::get('admin_id');
        UserModel::where('id', '=', $id)->update(['is_login'=>0]);
        Session::clear();
        $this->success('已成功退出管理系统', 'admin/user/goLoginPage');
    }
}