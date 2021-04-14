<?php

declare(strict_types=1);

namespace app\admin\controller;

use app\admin\validate\Image;
use app\common\business\Article as BusinessArticle;
use app\common\business\Category as BusinessCategory;
use app\common\business\Tag as BusinessTag;
use app\common\model\mysql\Article as ModelArticle;
use think\exception\ValidateException;
use think\facade\View;
use think\facade\Db;
use think\Request;

class Article
{
    /**
     * 写文章页
     *
     * @return \think\Response
     */
    public function create()
    {
        View::assign('categories', BusinessCategory::getCategories());
        return View::fetch();
    }

    /**
     * 文章列表页
     *
     * @return \think\Response
     */
    public function index()
    {
        return View::fetch();
    }

    /**
     * 文章列表接口 
     * 
     * 文章 status 0 隐藏 1 发布 2 草稿
     *
     * @param string $page
     * @param string $limit
     * @return json
     */
    public function table($page, $limit)
    {
        // 获取所有 status != 2 的文章
        $articles = BusinessArticle::getAritcles((int)$page, (int)$limit, '<>', 2);

        // 文章除草稿外的总数
        $count = ModelArticle::where('status', '<>', 2)->count();

        // 返回layui所需要的数据格式
        return layuiTable(0, '', $count, $articles);
    }

    /**
     * 草稿列表页
     *
     * @return \think\Response
     */
    public function draft()
    {
        return View::fetch();
    }

    /**
     * 草稿列表接口
     *
     * @param string $page
     * @param string $limit
     * @return json
     */
    public function draftTable($page, $limit)
    {
        // 获取所有 status = 2 的文章
        $articles = BusinessArticle::getAritcles((int)$page, (int)$limit, '=', 2);

        // 草稿的总数
        $count = ModelArticle::where('status', 2)->count();

        // 返回layui所需要的数据格式
        return layuiTable(0, '', $count, $articles);
    }


    /**
     * 封面图上传接口
     *
     * @param Request $request
     * @return json
     */
    public function uploadCover(Request $request)
    {
        // 获取图片对象
        $file = $request->file('cover_pic');

        // 验证图片格式与大小
        try {
            // check(['image' => $file]) 为什么需要一个数组，因为验证器里配置的字段是 image
            // 不组成数组的话，验证器无效
            validate(Image::class)->check(['image' => $file]);
        } catch (ValidateException $e) {
            // 返回 layui 需要的错误格式
            $data = layuiImage(1, $e->getMessage());

            return $data;
        }

        // 调用业务层处理图片
        $savepath = BusinessArticle::cover($file);

        // 返回 LayUI 上传控件所需要的数据格式
        return layuiImage(0, '上传图片成功！', $savepath);
    }

    /**
     * 保存文章内容中的图片
     * // TODO: 把本地更改成阿里云 OSS 上传
     *
     * @param Request $request
     * @return void
     */
    public function uploadImg(Request $request)
    {
        // 获取上传的文件
        $file = $request->file('editormd-image-file');

        // 调用业务层处理上传图片
        $savepath = BusinessArticle::uploadImg($file);

        // 拼接格式返回
        return json(['url' => '../storage/' . $savepath, 'success' => 1, 'message' => '图片上传成功!']);
    }

    /**
     * 保存新文章接口
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request, $status)
    {
        // 获取前端数据
        $article = $request->post('', '', 'trim');

        // 调用业务层保存文章
        $res = BusinessArticle::saveArticle($article, $status);

        // 如果 tags 不为空，则保存标签
        if ($article['tags'])
            BusinessTag::saveTagByArticle($article['tags'], '', $res->id);        // 调用业务层保存标签

        return $status == 1 ? show(1, '文章发布成功！') : show(1, '保存草稿成功！');
    }

    /**
     * 显示编辑资源表单页
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        // 通过id获取文章
        $article = BusinessArticle::getAritcleById($id);
        // 获取标签
        $article['tags'] = BusinessTag::getTagByArticleId($id);
        View::assign('article', $article);

        // 分类
        View::assign('categories', BusinessCategory::getCategories());

        return View::fetch();
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function update(Request $request)
    {
        // 获取前端数据
        $article = $request->put('', '', 'trim');

        // 将发布时间转换成时间戳保存  关于 create_time 和 update_time，开启了自动时间戳，不用管
        $article['release_time'] = strtotime($article['release_time']);

        // 如果tags 有值，则需要更新
        if ($article['tags'])
            BusinessTag::saveTagByArticle($article['tags'], 'update', $article['id']);

        // article 数组中包含 id，直接更新
        ModelArticle::update($article);

        return show(1, '更新文章成功！');
    }

    /**
     * 删除指定的文章
     *
     * @param  int  $id
     * @return json
     */
    public function delete($id)
    {
        // 直接删除文章
        ModelArticle::destroy($id);

        // 删除文章与标签关系表的数据
        Db::table('blog_article_tag')->where('article_id', $id)->delete();

        // TODO: 连带删除留言未做，处理完需要把这里的代码转移到 business 中

        return show(1, '删除文章成功！');
    }

    /**
     * 批量删除文章
     *
     * @param Request $request
     * @return json
     */
    public function allDel(Request $request)
    {
        // 将接收的数据拼接成需要的数据
        $data = explode(',', $request->post('id'));

        // 根据文章 ID 批量删除文章
        ModelArticle::destroy($data);

        // 根据文章 ID 循环删除 关系表中对应的数据
        foreach ($data as $value) {
            Db::table('blog_article_tag')->where('article_id', $value)->delete();
        }

        // TODO: 连带删除留言未做，处理完需要把这里的代码转移到 business 中

        return show(1, '批量删除文章成功!');
    }

    /**
     * 更新文章状态
     *
     * @param Request $request
     * @return json
     */
    public function updateStatus(Request $request)
    {
        $data = $request->put();

        BusinessArticle::updateStatus($data);

        return show(1, '更新状态成功！');
    }

    /**
     * 标题搜索
     *
     * @param Request $request
     * @return void
     */
    public function search(Request $request)
    {
        $str = $request->get('', '', 'trim');

        $res = BusinessArticle::searchTitle($str);

        return layuiTable(0, '', count($res), $res);
    }
}
