<?php

declare(strict_types=1);

namespace app\common\business;

use app\common\model\mysql\Category as ModelCategory;
use Exception;
use think\facade\View;

class Category
{
    /**
     * 获取排序好的分类
     *
     * @param integer $page    页码
     * @param integer $limit   每页数量
     * @return array           排序好的分类
     */
    public static function getCategories(int $page = null, int $limit = null)
    {
        if ($page == null && $limit == null) {
            // 获取数据
            $categories = ModelCategory::order(['sort' => 'desc', 'id'])->select();
        } else {
            // 获取数据
            $categories = ModelCategory::order(['sort' => 'desc', 'id'])->page($page, $limit)->select();
        }

        $categories = self::levelCategory($categories, 0, -1, $sortCategory);

        return $sortCategory;
    }

    /**
     * 无限极分类业务代码
     *
     * @param array $categories
     * @param integer $pid
     * @param integer $level
     * @param array $arr
     * @return void
     */
    public static function levelCategory($categories,  $pid = 0,  $level = -1, &$arr = [])
    {
        $level += 1;

        $str = $level == 0 ? '' : '|';

        foreach ($categories as $value) {
            if ($pid == $value['pid']) {
                $tmp_arr = array();

                $tmp_arr['id'] = $value['id'];
                $tmp_arr['name'] = $str . str_repeat("---", $level) . $value['name'];
                $tmp_arr['pid'] = $value['pid'];
                $tmp_arr['sort'] = $value['sort'];
                $tmp_arr['desc'] = $value['desc'];

                $arr[] = $tmp_arr;
                self::levelCategory($categories, $value['id'], $level, $arr);
                unset($value);
            }
        }
    }

    public static function delCategory($id)
    {
        // 判断是否是数字，如果是，则单个删除，否则批量删除
        if (is_int($id)) {
            try {
                // 首先判断是否有下级分类，有的话不能删除
                if (ModelCategory::where('pid', $id)->find())
                    throw new Exception('此分类有下级分类，请先删除下级分类！');

                //TODO: 判断分类下面是否有文章

                ModelCategory::destroy($id);
            } catch (\Exception $e) {
                throw new Exception($e->getMessage());
            }
        } else {
            try {
                foreach ($id as $value) {
                    if (ModelCategory::where('pid', $value)->find())
                        throw new Exception('所选分类中有下级分类，请重新确认！');

                    //TODO: 判断分类下面是否有文章

                }

                ModelCategory::destroy($id);
            } catch (\Exception $e) {
                throw new Exception($e->getMessage());
            }
        }

        return true;
    }
}
