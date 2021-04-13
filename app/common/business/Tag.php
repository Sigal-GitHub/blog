<?php

declare(strict_types=1);

namespace app\common\business;

use app\common\model\mysql\Tag as MysqlTag;
use think\facade\Db;

class Tag
{
    /**
     * 保存标签
     *
     * @param string $str
     * @return void
     */
    public static function saveTag($str)
    {
        // 拼接数组
        $tags = explode(',', $str);

        // 去除空格
        foreach ($tags as  $key => $value) {
            $tags[$key] = trim($value);
        }
        
        // 调用模型保存标签，如果已存在标签，则跳过
        foreach ($tags as $value) {
            $res = MysqlTag::where('name', $value)->find();
            if (!$res)
                MysqlTag::create(['name' => $value, 'create_time' => time()]);
        }

        return true;
    }

    public static function getTag($id)
    {
        $tags = Db::table('blog_article_tag')->where('article_id',$id)->column('tag_id');
        // halt($tags);
        foreach ($tags as $value) {
            $str[] = Db::table('blog_tag')->where('id',$value)->find();
        }
        halt($str);

    }
}
