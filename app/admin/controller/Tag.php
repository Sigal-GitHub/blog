<?php

declare(strict_types=1);

namespace app\admin\controller;

use app\common\model\mysql\Tag as MysqlTag;
use think\facade\View;
use think\facade\Db;
use think\Request;

class Tag
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {

        return View::fetch();
    }

    public function table($page, $limit)
    {
        $tags = MysqlTag::page($page, $limit)->select();

        return layuiTable(0, '', count($tags), $tags);
    }

    public function status(Request $request)
    {
    }
}
