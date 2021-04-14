<?php

declare(strict_types=1);

namespace app\home\controller;

use app\common\business\Article as BusinessArticle;
use app\common\model\mysql\Category;
use app\common\model\mysql\Article;
use app\common\model\mysql\Link;
use app\common\model\mysql\Tag;
use think\facade\Db;
use think\facade\View;

class Base
{
    public function __construct()
    {
        $this->getArticle();
        $this->getCategory();
        $this->getCommend();
        $this->getLink();
        $this->getTag();
    }

    public static function getCategory()
    {
        // 获取分类
        $categories = Category::where('pid', 0)->order('sort','desc')->select();
        View::assign('categories', $categories);
    }

    public static function getArticle()
    {
        // $articles = BusinessArticle::getAritcles(1, 3, '=', 1);
        // $articles = Db::table('blog_article')->where('status', 1)->paginate(2);
        $articles = Db::table('blog_article')->where('status', 1)->select();

        View::assign('articles', $articles);
    }

    public static function getCommend()
    {
        $recommend = Article::where('is_recommend', 1)->select();
        View::assign('recommend', $recommend);
    }

    public static function getLink()
    {
        $links = Link::where('status', 1)->select();
        View::assign('link', $links);
    }

    public static function getTag()
    {
        $tags = Tag::select();
        View::assign('tag', $tags);
    }
}
