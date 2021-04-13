<?php

declare(strict_types=1);

namespace app\admin\controller;

use app\common\model\mysql\Link as MysqlLink;
use think\facade\View;
use think\Request;

class Link
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

    public function table()
    {
        $links = MysqlLink::select();

        $count = MysqlLink::count();

        return layuiTable(0, '', $count, $links);
    }

    public function status(Request $request)
    {
        MysqlLink::update($request->put());

        return show(1, '更改状态成功！');
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        return View::fetch();
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $link = $request->post('', '', 'trim');
        MysqlLink::create($link);
        return show(1, '新增友链成功！');
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
        $link = MysqlLink::find($id);
        View::assign('link', $link);
        return View::fetch();
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request)
    {
        //
        $link = $request->put('', '', 'trim');


        if (!empty($link['status'])) {
            $link['status'] = 1;
        } else {
            $link['status'] = 0;
        }
        MysqlLink::update($link);

        return show(1, '编辑友链成功！');
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        MysqlLink::destroy($id);
        return show(1, '删除此友链成功！');
    }

    public function alldel(Request $request)
    {

        $data = explode(',', $request->post('id'));

        MysqlLink::destroy($data);

        return show(1, '批量删除友链成功！');
    }
}
