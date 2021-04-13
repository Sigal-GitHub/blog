<?php

declare(strict_types=1);

namespace app\home\controller;

use app\common\model\mysql\Article;
use think\facade\View;

class Index extends Base
{
    public function index()
    {
        // $articles = $this->getArticle();
        // return view('index', ['list' => $articles]);
        return View::fetch();
    }

    public function article($id)
    {

        $article = Article::find($id);

        // 判断文章是否删除，或者是否隐藏
        if ($article['status'] == 0)
            return '已消失不见！';
        View::assign('article', $article);

        self::prevArticle($id);
        self::nextArticle($id);

        return View::fetch();
    }

    public static function nextArticle($id)
    {
        $next = Article::where('id', '<', $id)->limit(1)->find();
        View::assign('next', $next);
    }

    public static function prevArticle($id)
    {
        $prev = Article::where('id', '>', $id)->limit(1)->find();
        View::assign('prev', $prev);
    }
}
