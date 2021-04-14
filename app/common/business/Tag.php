<?php

declare(strict_types=1);

namespace app\common\business;

use app\common\model\mysql\Tag as MysqlTag;
use think\facade\Db;

class Tag
{
    /**
     * 通过文章保存 or 更新标签
     *
     * @param string $str
     * @return void
     */
    public static function saveTagByArticle($str, $status, $articleId)
    {
        // 处理字符串
        $tags = self::stringToArray($str);

        // 首先判断是新增还是更新 update
        if ($status == 'update') {
            // 如果是更新，直接删除关系表中对应的数据，后面重新添加一次
            Db::table('blog_article_tag')->where('article_id', $articleId)->delete();
        }

        // 调用模型保存标签，如果已存在标签，则跳过
        foreach ($tags as $value) {
            // 根据标签名查找数据库
            $res = MysqlTag::where('name', $value)->find();

            // 如果数据库没值，则先添加到tag表，再添加到关系表
            if (!$res) {
                $tag = MysqlTag::create(['name' => $value]);
                Db::table('blog_article_tag')->insert(['article_id' => $articleId, 'tag_id' => $tag->id]);
            } else {
                // 如果有值，直接添加到关系表
                Db::table('blog_article_tag')->insert(['article_id' => $articleId, 'tag_id' => $res->id]);
            }
        }

        return true;
    }

    /**
     * 文章里获取标签
     *
     * @param string $id
     * @return void
     */
    public static function getTagByArticleId($id)
    {
        // 先查出关系表中该文章 ID 对应的标签 ID 格式：['11','22']
        $tagsId = Db::table('blog_article_tag')->where('article_id', $id)->column('tag_id');

        // 如果数据为空，则文章未设置标签, 返回空
        if (empty($tagsId))
            return '';

        // 如果有数据，则通过查出的 ID 获取标签名，格式：['标签名1','标签名2']
        $res = Db::table('blog_tag')->whereIn('id', $tagsId)->column('name');

        // 转成字符串
        $tags = implode(',', $res);

        return $tags;
    }

    /**
     * 处理字符串，将传过来的字符串转换成数组
     *
     * @param string $str
     * @return array ['11','22']
     */
    public static function stringToArray($str)
    {
        $tags = explode(',', $str);

        // 去除左右空格
        foreach ($tags as  $key => $value) {
            $tags[$key] = trim($value);
        }

        return $tags;
    }

    /**
     * 添加标签
     *
     * @param string $str
     * @return bool
     */
    public static function saveTag($str)
    {
        $tags = self::stringToArray($str);

        foreach ($tags as $value) {
            if (!MysqlTag::where('name', $value)->find())
                MysqlTag::create(['name' => $value]);
        }

        return true;
    }

    /**
     * 删除单个标签 && 批量删除标签
     *
     * @param void $id
     * @return bool
     */
    public static function deleteTag($id)
    {
        // 如果传过来的是数组，则表示是批量删除
        if (is_array($id)) {
            foreach ($id as $value) {
                MysqlTag::destroy($value);
                Db::table('blog_article_tag')->where('tag_id', $value)->delete();
            }
        } else {
            MysqlTag::destroy($id);
            Db::table('blog_article_tag')->where('tag_id', $id)->delete();
        }
        return true;
    }
}
