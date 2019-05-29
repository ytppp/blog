<?php
/**
 * Created by PhpStorm.
 * User: 65100
 * Date: 2019/4/17
 * Time: 21:53
 */

namespace app\api\controller;

use app\api\controller\Base;
use think\Db;
use think\facade\Request;
use app\common\model\Article as ArticleModel;
use app\common\validate\Article as ArticleValidate;
use app\common\model\ArticleCate as CateModel;
use app\common\validate\ArticleCate as CateValidate;

class Article extends Base
{
    /**
     * 处理文章增加与修改
     * @return \think\response\Json
     */
    public function saveArticle() {
        // 初始化数据
        $validate = new ArticleValidate;

        $artInfo = Request::param();
        $rs = $validate->check($artInfo);
        // 验证数据
        if (!$rs) {
            $this->addSiteLog($validate->getError(), 'api/article/saveArticle', 0);
            return json(['status' => -1, 'message' => $validate->getError()]);
        }
        // 验证数据
        if (isset($artInfo['id']) && '' != $artInfo['id']) {
            $res = ArticleModel::where('id', '=', $artInfo['id'])->update($artInfo);
            if ($res) {
                $this->addSiteLog('修改文章成功', 'api/article/saveArticle', 1);
                return json(['code'=>1, 'message'=>'修改文章成功']);
            } else {
                $this->addSiteLog('文章未修改或修改文章失败', 'api/article/saveArticle', 0);
                return json(['code'=>-1, 'message'=>'文章未修改或修改文章失败，请重试']);
            }
        } else {
            if (ArticleModel::create($artInfo)) {
                $this->addSiteLog('发布文章成功', 'api/article/saveArticle', 1);
                return json(['code'=>1, 'message'=>'发布文章成功']);
            } else {
                $this->addSiteLog('发布文章失败', 'api/article/saveArticle', -1);
                return json(['code'=>-1, 'message'=>'发布文章失败，请检查']);
            }
        }
    }

    /**
     * 获取文章详情
     * @return \think\response\Json
     */
    public function getArticleDetail () {
        $id = Request::param('id');
        $art = ArticleModel::where('id', '=', $id)->find();
        if ($art) {
            $this->addSiteLog('获取文章详情成功', 'api/article/getArticleDetail', 1);
            return json([
                'code'  => 1,
                'message' => '获取文章详情成功',
                'data'    => $art
            ]);
        } else {
            $this->addSiteLog('获取文章详情失败', 'api/article/getArticleDetail', 0);
            return json([
                'code'  => -1,
                'message' => '获取文章详情失败'
            ]);
        }
    }

    /**
     * 获得文章列表
     * @return \think\response\Json
     */
    public function getArticleList()
    {
        // 全局查询条件
        $map = [];

        // 获取信息
        $keywords = Request::param('keywords');
        $cate_id = Request::param('cate_id');
        $author_id = Request::param('author_id');
        $status = Request::param('status');
        $is_draft = Request::param('is_draft');
        $is_top = Request::param('is_top');
        $is_hot = Request::param('is_hot');

        // 封装查询条件
        if (!empty($keywords)) {
            $map[] = ['title', 'like', '%'.$keywords.'%'];
        }
        if ('' != $cate_id) {
            $map[] = ['cate_id', '=', $cate_id];
        }
        if ('' != $author_id) {
            $map[] = ['author_id', '=', $author_id];
        }
        if ('' != $status) {
            $map[] = ['status', '=', $status];
        }
        if ('' != $is_draft) {
            $map[] = ['is_draft', '=', $is_draft];
        }
        if ('' != $is_top) {
            $map[] = ['is_top', '=', $is_top];
        }
        if ('' != $is_hot) {
            $map[] = ['is_hot', '=', $is_hot];
        }
        $artList = ArticleModel::where($map)->with('articleCate')->order('create_time', 'desc')->select();
        foreach ($artList as $art) {
            $art['cate_name'] = $art->articleCate->name;
            unset($art->articleCate);
            unset($art->article_cate);
        }
        if (!is_null($artList)) {
            $this->addSiteLog('管理员获取文章列表成功', 'api/article/getArticleList', 1);
            return json([
                'code'  => 1,
                'message' => '获取文章列表成功',
                'data'    => [
                    'art_list' => $artList
                ]
            ]);
        } else {
            $this->addSiteLog('管理员获取文章列表失败', 'api/article/getArticleList', 0);
            return json([
                'code'  => -1,
                'message' => '获取文章列表失败'
            ]);
        }
    }

    /**
     * 处理文章栏目增加
     * @return \think\response\Json
     */
    public function addArticleCate()
    {
        // 初始化数据
        $validate = new CateValidate;

        $cateInfo = Request::param();

        $rs = $validate->check($cateInfo);
        // 验证数据
        if (!$rs) {
            $this->addSiteLog('$validate->getError()', 'api/article/addArticleCate', 0);
            return json([
                'code'    => -1,
                'message' => $validate->getError()
            ]);
        }
        if ($cate = CateModel::create($cateInfo)) {
            $this->addSiteLog('增加栏目成功', 'api/article/addArticleCate', 1);
            return json([
                'code'    => 1,
                'message' => '增加栏目成功',
                'data'    => [
                    'id' => $cate->id,
                    'name' => $cate->name
                ]
            ]);
        } else {
            $this->addSiteLog('增加栏目失败', 'api/article/addArticleCate', 0);
            return json([
                'code'    => -1,
                'message' => '增加栏目失败'
            ]);
        }
    }

    /**
     * 删除文章栏目
     * @return \think\response\Json
     */
    public function deleteArticleCate()
    {
        $data = Request::param();
        $res = CateModel::where('id', '=', $data['cateId'])->delete();
        if (1 == $res) {
            $this->addSiteLog('栏目信息删除成功', 'api/article/deleteArticleCate', 1);
            return json([
                'code'  => 1,
                'message' => '已删除该栏目'
            ]);
        } else {
            $this->addSiteLog('删除栏目信息失败', 'api/article/deleteArticleCate', 0);
            return json([
                'code'  => -1,
                'message' => '删除栏目失败,请检查'
            ]);
        }
    }

    /**
     * 获取文章栏目列表
     * @return \think\response\Json
     */
    public function getArticleCateList()
    {
        // 获取数据
        $cateList = CateModel::field('id, name')
            ->select();
        if (!is_null($cateList)) {
            $this->addSiteLog('获取栏目列表成功', 'api/article/getArticleCateList',  1);
            return json([
                'code'  => 1,
                'message' => '获取栏目列表成功',
                'data'    => [
                    'cate_list' => $cateList
                ]
            ]);
        } else {
            $this->addSiteLog('获取栏目列表失败', 'api/article/getArticleCateList', 0);
            return json([
                'code'  => 0,
                'message' => '获取栏目列表失败'
            ]);
        }
    }

    /**
     * 删除文章
     * @return \think\response\Json
     */
    public function deleteArticle ()
    {
        $data = Request::param();
        $res = ArticleModel::where('id', '=', $data['id'])->delete();
        if (1 == $res) {
            $this->addSiteLog('删除文章信息成功', 'api/article/deleteArticle', 1);
            return json([
                'code'  => 1,
                'message' => '已删除该文章'
            ]);
        } else {
            $this->addSiteLog('删除文章信息失败', 'api/article/deleteArticle', 0);
            return json([
                'code'  => -1,
                'message' => '删除文章失败,请检查'
            ]);
        }
    }

    /**
     * 更改文章状态
     * @return \think\response\Json
     */
    public function changeArticleStatus () {
        $articleInfo = Request::param();
        $articleInfoUpdate = [
            $articleInfo['key'] => $articleInfo['value']
        ];
        $res = ArticleModel::where('id', '=', $articleInfo['id'])->update($articleInfoUpdate);
        if ($res) {
            $this->addSiteLog('更改文章状态成功', 'api/article/changeArticleStatus', 1);
            return json([
                'code'    => 1,
                'message' => '更改文章状态成功',
            ]);
        } else {
            $this->addSiteLog('更改文章状态失败', 'api/article/changeArticleStatus', 0);
            return json([
                'code'    => -1,
                'message' => '更改文章状态失败,请检查',
            ]);
        }
    }

    // 查出每种分类下的文章数
    public function getArticleNumByCate()
    {
        $cateNumInfo = [];
        // 获取所有栏目
        $cateList = CateModel::field('id, name')
            ->select();
        // 遍历栏目列表，获得每种栏目下的文章数
        foreach ($cateList as $key => $value) {
            $num = ArticleModel::where('cate_id', '=', $value['id'])->count();
            array_push($cateNumInfo, [
                'id'   => $value['id'],
                'name' => $value['name'],
                'num'  => $num
            ]);
        }
        if (!is_null($cateNumInfo)) {
            $this->addSiteLog('获取文章分类下的文章数目成功', 'api/article/getArticleNumByCate', 1);
            return json([
                'code'    => 1,
                'message' => '获取每种分类下的文章数目成功',
                'data'    => [
                    'cate_article_obj' => $cateNumInfo
                ]
            ]);
        } else {
            $this->addSiteLog('获取文章分类下的文章数目失败', 'api/article/getArticleNumByCate', 1);
            return json([
                'code'    => 1,
                'message' => '获取每种分类下的文章数目失败'
            ]);
        }

    }
}