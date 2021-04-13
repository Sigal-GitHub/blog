<?php

declare(strict_types=1);

namespace app\common\business;

use app\common\model\mysql\Article as ModelArticle;

class Article
{
    /**
     * 获取所有文章
     *
     * @param integer $page
     * @param integer $limit
     * @param string $mark
     * @return array
     */
    public static function getAritcles($page, $limit, $mark, $status)
    {
        $articles = ModelArticle::withoutField('content')
            ->where('status', $mark, $status)
            ->page($page, $limit)
            ->select();

        return $articles;
    }

    /**
     * 保存文章
     *
     * @param array $article
     * @param string $status
     * @return bool
     */
    public static function saveArticle($article, $status)
    {
        // 如果是发布，则使用自定义时间或当前时间，如果是草稿，则不需要发布时间，等待发布的时候再添加上去
        if ($status == 1) {
            $article['release_time'] = $article['release_time'] ? strtotime($article['release_time']) : time();
        } else {
            $article['release_time'] = $article['release_time'] ? strtotime($article['release_time']) : '';
        }

        $article['status'] = $status;

        // 保存文章
        $res = ModelArticle::create($article);

        return $res;
    }

    /**
     * 处理上传的封面图 ---- 制作缩略图
     *
     * @param object $file 图片对象
     * @return string   返回图片保存的路径
     */
    public static function cover($file)
    {
        // 先保存完整图片到 public 目录
        $savepath = \think\facade\Filesystem::disk('public')->putFile('/cover', $file);

        // 调用 image 类制作缩略图并覆盖原图
        $image = \think\Image::open($file);              // 先要打开图片
        $image->thumb(250, 150)->save(root_path() . 'public/storage/' . $savepath);
        // 这里 tp5 的上传就可以直接使用 save($savepath);

        return $savepath;
    }

    /**
     * 保存文章内容中的图片
     *
     * @param object $file
     * @return string
     */
    public static function uploadImg($file)
    {
        $savepath = \think\facade\Filesystem::disk('public')->putFile('/img', $file);

        return $savepath;
    }

    /**
     * 更新文章状态
     *
     * @param array $data
     * @return bool
     */
    public static function updateStatus($data)
    {
        // 接收的数据格式是： ["id" => "25", "is_top" => "1"]
        // 更改成：[0 => "id",1 => "is_top"]
        $key = array_keys($data);

        // 确认所需要更新的字段名
        $field = $key[1];

        $article = ModelArticle::find($data['id']);

        $article->$field = $data[$field];

        // 更新的时候关闭自动时间戳
        $article->isAutoWriteTimestamp(false)->save();

        return true;
    }

    /**
     * 标题搜索
     *
     * @param string $data
     * @return void
     */
    public static function searchTitle($str)
    {
        return ModelArticle::withSearch(['title'], [
            'title'  => $str['title'],
            'status' => 1
        ])->withoutField(['desc', 'content'])
            ->where('status', '<>', 2)
            ->select();
    }
}
