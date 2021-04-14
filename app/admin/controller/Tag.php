<?php

declare(strict_types=1);

namespace app\admin\controller;

use app\common\business\Tag as BusinessTag;
use app\common\model\mysql\Tag as MysqlTag;
use think\facade\View;
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

    /**
     * 获取标签接口
     *
     * @param string $page
     * @param string $limit
     * @return json
     */
    public function table($page, $limit)
    {
        $tags = MysqlTag::page($page, $limit)->order('id', 'desc')->select();

        $count = MysqlTag::count();

        return layuiTable(0, '', $count, $tags);
    }

    /**
     * 添加标签页
     *
     * @return void
     */
    public function create()
    {
        return View::fetch();
    }

    /**
     * 添加标签接口 
     *
     * @param Request $request
     * @return json
     */
    public function save(Request $request)
    {
        // 判断请求方式
        if (!$request->isAjax() && !$request->isPost())
            return show(0, '非法提交！');

        $tags = $request->post('name', '', 'trim');

        BusinessTag::saveTag($tags);

        return show(1, '标签添加成功！');
    }

    /**
     * 删除单个标签
     *
     * @param string $id
     * @return json
     */
    public function delete($id)
    {
        // 判断请求方式
        if (!request()->isAjax() && !request()->isDelete())
            return show(0, '非法提交！');

        BusinessTag::deleteTag($id);

        return show(1, '删除此标签成功！');
    }

    /**
     * 批量删除标签
     *
     * @param Request $request
     * @return json
     */
    public function allDel(Request $request)
    {
        // 判断请求方式
        if (!$request->isAjax() && !$request->isPost())
            return show(0, '非法提交！');

        $arrayId = explode(',', $request->post('id'));

        BusinessTag::deleteTag($arrayId);

        return show(1, '批量删除标签成功！');
    }

    /**
     * 修改标签名
     *
     * @param Request $request
     * @return json
     */
    public function rname(Request $request)
    {
        $tag = $request->post(['id', 'name']);
        MysqlTag::update($tag);
        return show(1, '标签名修改成功！');
    }
}
