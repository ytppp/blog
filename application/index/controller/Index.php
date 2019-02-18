<?php
namespace app\index\controller;

use think\facade\Request;
use app\index\controller\Base;
use app\common\model\ArticleCate as ArticleCateModel;
use app\common\model\ArticleAuthor as ArticleAuthorModel;
use app\common\model\Article as ArticleModel;
use app\common\model\Notice as NoticeModel;

class Index extends Base
{
    public function index()
    {
        $map = [];
        $map[] = ['status', '=', '1'];
        $map[] = ['is_draft', '=', '0'];
        // 从前端获取信息
        $keywords = Request::param('keywords');
        $cateId = Request::param('cateId');
        $authorId = Request::param('authorId');
        // 封装查询条件
        if (!empty($keywords)) {
            $map[] = ['title', 'like', '%'.$keywords.'%'];
        }
        if ('' != $cateId && -1 != $cateId) {
            $map[] = ['cate_id', '=', $cateId];
        }
        if ('' != $authorId && -1 != $authorId) {
            $map[] = ['author_id', '=', $authorId];
        }
        $artList = ArticleModel::where($map)->order('create_time', 'desc')->paginate(5);
        $notice = NoticeModel::field('name, content')->where('status', '=', 1)->find();
        if (is_null($notice)) {
            $this->view->assign('notice', 0);
        } else {
            $this->view->assign('notice', $notice);
        }
        $this->view->assign('artList', $artList);
        $this->getCateList();
        $this->getAuthorList();
        $this->view->assign('empty', "<h3 class='text-center text-danger'>没有数据</h3>");
        return $this->view->fetch('index', ['title'=>'杨庭培的个人博客']);
    }

    public function getCateList()
    {
        $cateList = ArticleCateModel::field('id, name')->where('status', '=', '1')->select();
        $this->view->assign('cateTitle', '栏目');
        $this->view->assign('cateList', $cateList);
    }

    public function getAuthorList()
    {
        $authorList = ArticleAuthorModel::field('id, name')->where('status', '=', '1')->select();
        $this->view->assign('authorTitle', '作者');
        $this->view->assign('authorList', $authorList);
    }

    public function getArtDetail()
    {
        $id = Request::param('id');
        $art = ArticleModel::get(function ($query) use ($id) {
            $query->where([
                ['id', '=', $id],
                ['status', '=', 1],
                ['is_draft', '=', 0]
            ])->setInc('pv', 1);
        });
        $this->getCateList();
        $this->getAuthorList();
        $this->view->assign('art', $art);
        return $this->view->fetch('detail', ['title'=>'杨庭培的个人博客']);
    }
}
