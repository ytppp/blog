<?php
/**
 * Created by PhpStorm.
 * User: 65100
 * Date: 2019/3/18
 * Time: 8:59
 */

namespace app\api\controller;

use app\api\controller\Base;
use think\facade\Request;
use app\common\model\User as UserModel;
use think\facade\Session;
use app\common\validate\User as UserValidate;

class User extends Base
{
    /**
     * 用户注册
     */
    public function handleUserReg () {
        $validate = new UserValidate;

        // 获取数据
        $userInfoData = Request::param();
        $rs = $validate->check($userInfoData);
        // 验证数据
        if (!$rs) {
            $this->addSiteLog($validate->getError(), 'api/user/handleUserReg', 0);
            return json(['code' => -1, 'message' => $validate->getError()]);
        }

        if ($userInfo = UserModel::create($userInfoData)) {
            $result = UserModel::get($userInfo->id);
            $this->addSiteLog('id为'.$result->id.'姓名为'.$result->name.'的用户注册成功', 'api/user/handleUserReg', 1);
            UserModel::where('id', '=', $result->id)->update(['is_login'=>1]);
            Session::set('user_id', $result->id);
            Session::set('user_name', $result->name);
            Session::set('user_avatar', $result->avatar);
            return json(['code'=>1, 'message'=>'注册成功']);
        } else {
            $this->addSiteLog('用户注册失败', 'api/user/handleUserReg', 0);
            return json(['code' => -1, 'message' => '注册失败，请检查']);
        }
    }

    /**
     * 用户使用邮箱登录检查
     * @return \think\response\Json
     */
    public function checkUserLogin()
    {
        $userResult = null;
        $map = [];
        // 自定义验证规则
        $rule = [
            'email|邮箱' => [
                'email'  => 'email',
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
            return json(['code' => -1, 'message' => $res]);
        }
        // 查询条件
        $map[] = ['email', '=', $data['email']];
        $map[] = ['password', '=', sha1($data['password'])];
        $userResult = UserModel::where($map)->find();
        unset($userResult['password']);
        if (!is_null($userResult)) {
            $this->addSiteLog('email为'.$data['email'].'的用户已登录', 'api/user/checkUserLogin', 1);
            UserModel::where('id', '=', $userResult['id'])->update(['is_login'=>1]);
            Session::set('admin_id', $userResult['id']);
            return json([
                'code' => 1,
                'message' => '登录成功',
                'user_info' => $userResult
            ]);
        } else {
            $this->addSiteLog('email为'.$data['email'].'的用户登录失败', 'api/user/checkUserLogin', 0);
            return json(['code' => -1, 'message' => '账号或密码错误，请重新登录']);
        }
    }

    // 用户使用验证码登录
    public function checkUserLoginByCheckNum () {}

    // 获取用户列表
    public function getUserList () {
        // 全局查询条件
        $map = [];

        $map[] = ['is_admin', '=', 0];
        // 获取信息
        $keywords = Request::param('keywords');
        $status = Request::param('status');

        // 封装查询条件
        if (!empty($keywords)) {
            $map[] = ['name', 'like', '%'.$keywords.'%'];
        }
        if ('' != $status) {
            if (1 == $status) {
                $map[] = ['status', '=', 1];
            } else if (0 == $status) {
                $map[] = ['status', '=', 0];
            }
        }
        // 获取数据
        $userList = UserModel::where($map)->order('create_time', 'desc')->select();
        if (!is_null($userList)) {
            $this->addSiteLog('获取用户列表成功', 'api/user/getUserList', 1);
            return json([
                'code'  => 1,
                'message' => '获取用户列表成功',
                'data'    => [
                    'user_list' => $userList
                ]
            ]);
        } else {
            $this->addSiteLog('获取用户列表失败', 'api/user/getUserList', 0);
            return json([
                'code'  => -1,
                'message' => '获取用户列表失败,请检查'
            ]);
        }
    }

    // 更改用户状态
    public function changeUserStatus () {
        $userInfo = Request::param();
        $userInfoUpdate = [
            $userInfo['key'] => $userInfo['value']
        ];
        $res = UserModel::where('id', '=', $userInfo['id'])->update($userInfoUpdate);
        if ($res) {
            $this->addSiteLog('更改blog_user表中id='.$userInfo['id'].'的用户的'.$userInfo['key'].'状态='.$userInfo['value'], 'api/user/changeUserStatus', 1);
            return json([
                'code'    => 1,
                'message' => '更改用户状态成功',
            ]);
        } else {
            $this->addSiteLog('更改blog_user表中id='.$userInfo['id'].'的用户的'.$userInfo['key'].'状态失败', 'api/user/changeUserStatus', 0);
            return json([
                'code'    => -1,
                'message' => '更改用户状态失败,请检查',
            ]);
        }
    }

    // 删除用户
    public function deleteUser () {
        $userInfo = Request::param();
        $res = UserModel::where('id', '=', $userInfo['id'])->delete();
        if (1 == $res) {
            $this->addSiteLog('已删除id为'.$userInfo['id'].'的用户', 'api/user/deleteUser', 1);
            return json([
                'code'  => 1,
                'message' => '已删除该用户'
            ]);
        } else {
            $this->addSiteLog('删除id为'.$userInfo['id'].'的用户失败', 'api/user/deleteUser', 0);
            return json([
                'code'  => -1,
                'message' => '删除用户失败,请检查'
            ]);
        }
    }
}