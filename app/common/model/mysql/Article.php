<?php

declare(strict_types=1);

namespace app\common\model\mysql;

use think\Model;

/**
 * @mixin \think\Model
 */
class Article extends Model
{
    /**
     * 分类获取器 -- 将获取的分类 id 自动转换成分类名
     *
     * @param array $value
     * @return array
     */
    public function getCidAttr($value)
    {
        // 获取分类，结果是多维数组
        $category = Category::field('id,name')->select()->toArray();

        // 多维数组改成一维数组
        $cid = array_column($category, 'name', 'id');

        // 将需要的值添加进去
        $cid += ['0' => '未分类'];

        // 返回获取器需要的值
        return $cid[$value];
    }

    /**
     * 发布时间获取器 -- 自动格式化发布时间
     *
     * @param array $value
     * @return string
     */
    public function getReleaseTimeAttr($value)
    {
        $releaseTime = date('Y-m-d H:i:s', $value);
        return $releaseTime;
    }

    /**
     * 封面图获取器 -- 自动拼接封面图地址
     *
     * @param array $value
     * @return string
     */
    public function getCoverAttr($value)
    {
        if ($value) {
            return config('admin.cover_url') . $value;
        }
    }

    /**
     * 搜索器
     */
    public static function searchTitleAttr($query, $value, $data)
    {
        $query->where('title', 'like', '%' . $value . '%');
    }
}
