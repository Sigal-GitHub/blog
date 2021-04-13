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

        // 返回layui所需要的数据格式
        return layuiTable(0, '', count($articles), $articles);
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

        // 返回layui所需要的数据格式
        return layuiTable(0, '', count($articles), $articles);
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
     *
     * @param Request $request
     * @return void
     */
    public function uploadImg(Request $request)
    {
        // 获取上传的文件
        $file = $request->file('editormd-image-file');
        $savepath = BusinessArticle::uploadImg($file);
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
        $article = $request->post('', '', 'trim');

        // 调用业务层保存文章
        $article = BusinessArticle::saveArticle($article, $status);

        // halt($article->id);

        if (!empty($article['tags']))
            BusinessTag::saveTag($article['tags']);        // 调用业务层保存标签

        if ($status == 1) {
            return show(1, '文章发布成功！');
        } else {
            return show(1, '保存草稿成功！');
        }
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $article = ModelArticle::find($id);
        // 这里之前定义了获取器，如果前端要判断所属分类，就要获取原始数据
        $article = $article->getData();
        $article['tags']=BusinessTag::getTag($id);
        View::assign('article', $article);

        // BusinessCategory::viewCategory();
        $categories = BusinessCategory::getCategories();
        View::assign('categories', $categories);

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
        $article = $request->put();

        $article['create_time'] = strtotime($article['create_time']);

        ModelArticle::update($article);

        return show(1, '更新文章成功！');
    }

    /**
     * 删除指定的文章
     * // TODO: 连带删除图片未做，如果有留言，怎么处理，更改后的代码应该放在 business 中
     *
     * @param  int  $id
     * @return json
     */
    public function delete($id)
    {
        ModelArticle::destroy($id);

        return show(1, '删除文章成功！');
    }

    /**
     * 批量删除文章
     * // TODO: 连带删除图片未做，如果有留言，怎么处理，更改后的代码应该放在 business 中
     *
     * @param Request $request
     * @return json
     */
    public function allDel(Request $request)
    {
        // 将接收的数据拼接成需要的数据
        $data = explode(',', $request->post('id'));

        // 批量删除
        ModelArticle::destroy($data);

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
