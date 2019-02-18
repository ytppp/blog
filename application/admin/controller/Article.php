<?php
/**
 * Created by PhpStorm.
 * User: 65100
 * Date: 2019/1/17
 * Time: 11:39
 */

namespace app\admin\controller;

use app\admin\controller\Base;
use app\common\model\ArticleCate;
use think\facade\Request;
use app\common\model\ArticleAuthor as AuthorModel;
use app\common\validate\ArticleAuthor as AuthorValidate;
use app\common\model\ArticleCate as CateModel;
use app\common\validate\ArticleCate as CateValidate;
use app\common\model\Article as ArticleModel;
use app\common\validate\Article as ArticleValidate;
use app\common\model\User as UserModel;
use think\Image;

class Article extends Base
{
    private $artInfo = [];

    public function goArticleListPage()
    {
        // 全局查询条件
        $map = [];

        $this->isAdminLogined();

        // 从前端获取信息
        $keywords = Request::param('keywords');
        $cateId = Request::param('cate_id');
        $authorId = Request::param('author_id');

        // 封装查询条件
        if (!empty($keywords)) {
            $map[] = ['title', 'like', '%'.$keywords.'%'];
        }
        if ('' != $cateId) {
            $map[] = ['cate_id', '=', $cateId];
        }
        if ('' != $authorId) {
            $map[] = ['author_id', '=', $authorId];
        }
        // 获取数据
        // 栏目数据
        $cateList = CateModel::where('status', '=', 1)->field('id, name')->select();
        $authorList = AuthorModel::where('status', '=', 1)->field('id, name')->select();
        $artList = ArticleModel::where($map)->order('create_time', 'desc')->paginate(10);
        // 模板赋值
        $this->view->assign('artList', $artList);
        $this->view->assign('cateList', $cateList);
        $this->view->assign('authorList', $authorList);
        $this->view->assign('keywords', $keywords);
        $this->view->assign('cateId', $cateId);
        $this->view->assign('authorId', $authorId);
        $this->view->assign('empty', "<h3 class='text-center text-danger'>没有文章数据</h3>");
        return $this->view->fetch('list', ['title' => '文章列表']);
    }

    public function goArticleAddPage()
    {
        $this->isAdminLogined();
        // 获取数据
        $authorList = AuthorModel::where('status', '=', 1)->field('id, name')->select();
        $cateList = CateModel::where('status', '=', 1)->field('id, name')->select();
        // 模板赋值
        if (count($cateList) > 0) {
            // 将查询到的栏目信息赋值给模板
            $this->assign('cateList', $cateList);
        } else {
            $this->error('请先添加栏目', 'article/goArticleCateListPage');
        }
        if (count($authorList) > 0) {
            // 将查询到的栏目信息赋值给模板
            $this->assign('authorList', $authorList);
        } else {
            $this->error('请先添加作者', 'article/goArticleAuthorListPage');
        }
        // 渲染模板
        return $this->view->fetch('add', ['title' => '增加文章']);
    }

    public function goArticleCateListPage()
    {
        $this->isAdminLogined();
        // 获取数据
        $cateList = CateModel::field('id, name, status, update_time')
            ->paginate(10);
        // 模板赋值
        $this->view->assign('title', '文章栏目列表');
        $this->view->assign('cateList', $cateList);
        $this->view->assign('empty', "<h3 class='text-center text-danger'>没有栏目数据</h3>");
        // 渲染模板
        return $this->view->fetch('cate');
    }

    // 渲染作者列表界面
    public function goArticleAuthorListPage()
    {
        $this->isAdminLogined();
        // 获取数据
        $authorList = AuthorModel::field('id, name, status, update_time')
            ->paginate(10);
        // 模板赋值
        $this->view->assign('title', '文章作者列表');
        $this->view->assign('authorList', $authorList);
        $this->view->assign('empty', "<h3 class='text-center text-danger'>没有作者数据</h3>");
        // 渲染模板
        return $this->view->fetch('author');
    }

    // 增加修改文章作者
    public function updateArticleAuthor()
    {
        // 初始化数据
        $validate = new AuthorValidate;

        if (!Request::isAjax()) {
            return ['status' => -1, 'msg' => '请求类型错误'];
        }

        $authorInfo = Request::param();

        $rs = $validate->check($authorInfo);
        // 验证数据
        if (!$rs) {
            return ['status' => -1, 'msg' => $validate->getError() ];
        }
        if (isset($authorInfo['id']) && '' != $authorInfo['id']) {
            $res = AuthorModel::where('id', '=', $authorInfo['id'])->update($authorInfo);
            if ($res) {
                return ['status' => 1, 'msg' => '修改文章作者信息成功'];
            } else {
                return ['status' => -1, 'msg' => '修改文章作者信息失败,请检查'];
            }
        } else {
            if (AuthorModel::create($authorInfo)) {
                return ['status'=>1, 'msg'=>'发布文章作者信息成功'];
            } else {
                return ['status'=>-1, 'msg'=>'发布文章作者信息失败,请检查'];
            }
        }
    }

    /*
     * 删除作者信息
     * 即把文章从数据库中删除
     */
    public function deleAuthor()
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
        $res = AuthorModel::where('id', '=', $data['authorId'])->delete();
        if (1 == $res) {
            return ['status'=>1, 'msg'=>'已删除该作者信息'];
        } else {
            return ['status'=>-1, 'msg'=>'删除文章作者信息失败,请检查'];
        }
    }

    public function updateAuthorStatus()
    {
        if (!Request::isAjax()) {
            return ['status'=>-1, 'msg'=>'请求类型错误'];
        }
        $data = Request::param();
        if (1 == $data['status']) {
            $res = AuthorModel::where('id', '=', $data['id'])->update(['status'=>1]);
        } else {
            $res = AuthorModel::where('id', '=', $data['id'])->update(['status'=>0]);
        }

        if (1 == $res) {
            return ['status'=>1, 'msg'=>'切换作者状态成功'];
        } else {
            return ['status'=>-1, 'msg'=>'切换作者状态失败,请检查'];
        }
    }

    public function updateArticleCate()
    {
        // 初始化数据
        $validate = new CateValidate;

        if (!Request::isAjax()) {
            return ['status' => -1, 'msg' => '请求类型错误'];
        }

        $cateInfo = Request::param();

        $rs = $validate->check($cateInfo);
        // 验证数据
        if (!$rs) {
            return ['status' => -1, 'msg' => $validate->getError() ];
        }
        if (isset($cateInfo['id']) && '' != $cateInfo['id']) {
            $res = CateModel::where('id', '=', $cateInfo['id'])->update($cateInfo);
            if ($res) {
                return ['status' => 1, 'msg' => '修改文章作者信息成功'];
            } else {
                return ['status' => -1, 'msg' => '修改文章作者信息失败,请检查'];
            }
        } else {
            if (CateModel::create($cateInfo)) {
                return ['status'=>1, 'msg'=>'发布文章作者信息成功'];
            } else {
                return ['status'=>-1, 'msg'=>'发布文章作者信息失败,请检查'];
            }
        }
    }

    public function deleCate()
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
        $res = CateModel::where('id', '=', $data['cateId'])->delete();
        if (1 == $res) {
            return ['status'=>1, 'msg'=>'已删除该栏目信息'];
        } else {
            return ['status'=>-1, 'msg'=>'删除文章栏目信息失败,请检查'];
        }
    }

    public function updateCateStatus()
    {
        if (!Request::isAjax()) {
            return ['status'=>-1, 'msg'=>'请求类型错误'];
        }
        $data = Request::param();
        if (1 == $data['status']) {
            $res = CateModel::where('id', '=', $data['id'])->update(['status'=>1]);
        } else {
            $res = CateModel::where('id', '=', $data['id'])->update(['status'=>0]);
        }

        if (1 == $res) {
            return ['status'=>1, 'msg'=>'切换栏目状态成功'];
        } else {
            return ['status'=>-1, 'msg'=>'切换栏目状态失败,请检查'];
        }
    }

    // 处理文章增加与修改
    public function saveArticle() {
        // 初始化数据
        $validate = new ArticleValidate;

        if (!Request::isAjax()) {
            return ['status'=>-1, 'msg'=>'请求类型错误'];
        }

        $this->artInfo = Request::param();
        $rs = $validate->check($this->artInfo);
        // 验证数据
        if (!$rs) {
            return ['status' => -1, 'msg' => $validate->getError() ];
        }
        // 验证数据
        if (isset($this->artInfo['id']) && '' != $this->artInfo['id']) {
            $res = ArticleModel::where('id', '=', $this->artInfo['id'])->update($this->artInfo);
            if ($res) {
                return ['status'=>1, 'msg'=>'修改文章成功'];
            } else {
                return ['status'=>-1, 'msg'=>'文章未修改或修改文章失败，请重试'];
            }
        } else {
            if (ArticleModel::create($this->artInfo)) {
                return ['status'=>1, 'msg'=>'发布文章成功'];
            } else {
                return ['status'=>-1, 'msg'=>'发布文章失败，请检查'];
            }
        }
    }

    public function deleArt()
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
        $res = ArticleModel::where('id', '=', $data['artId'])->delete();
        if (1 == $res) {
            return ['status'=>1, 'msg'=>'已删除该作者信息'];
        } else {
            return ['status'=>-1, 'msg'=>'删除文章作者信息失败,请检查'];
        }
    }

    public function updateArtStatus()
    {
        if (!Request::isAjax()) {
            return ['status'=>-1, 'msg'=>'请求类型错误'];
        }
        $data = Request::param();
        if (1 == $data['status']) {
            $res = ArticleModel::where('id', '=', $data['id'])->update(['status'=>1]);
        } else {
            $res = ArticleModel::where('id', '=', $data['id'])->update(['status'=>0]);
        }

        if (1 == $res) {
            return ['status'=>1, 'msg'=>'切换作者状态成功'];
        } else {
            return ['status'=>-1, 'msg'=>'切换作者状态失败,请检查'];
        }
    }

    public function updateArtDraft()
    {
        if (!Request::isAjax()) {
            return ['status'=>-1, 'msg'=>'请求类型错误'];
        }
        $data = Request::param();
        if (1 == $data['isDraft']) {
            $res = ArticleModel::where('id', '=', $data['id'])->update(['is_draft'=>1]);
        } else {
            $res = ArticleModel::where('id', '=', $data['id'])->update(['is_draft'=>0]);
        }

        if (1 == $res) {
            return ['status'=>1, 'msg'=>'切换发表状态成功'];
        } else {
            return ['status'=>-1, 'msg'=>'切换发表状态失败,请检查'];
        }
    }

}