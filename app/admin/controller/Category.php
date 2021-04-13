<?php

declare(strict_types=1);

namespace app\admin\controller;

use app\common\business\Category as BusinessCategory;
use app\common\model\mysql\Category as ModelCategory;
use think\Request;
use think\facade\View;

class Category
{
    /**
     * 显示分类列表
     *
     * @return \think\Response
     */
    public function index()
    {
        // 渲染页面
        return View::fetch();
    }

    /**
     * 获取分类接口
     *
     * @param sting $page
     * @param sting $limit
     * @return json
     */
    public function table($page, $limit)
    {
        // 调用业务层获取已经排序好的分类
        $categories = BusinessCategory::getCategories((int)$page, (int)$limit);

        // 返回数据
        return layuiTable(0,'', ModelCategory::count(),$categories);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        // 获取分类
        $categories = BusinessCategory::getCategories();

        // 赋值模板
        View::assign('categories', $categories);

        // 渲染页面
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
        //TODO: 需要验证数据的合法性
        if (!$request->isAjax() && !$request->isPost())
            return show(0, '非法提交！');

        // 获取数据
        $category = $request->param('', '', 'trim');

        // 静态方法保存新分类，返回模型实例
        ModelCategory::create($category);

        return show(1, '添加分类成功！');
    }

    /**
     * 显示编辑分类表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        // 根据 ID 获取当前需要编辑的分类
        $category = ModelCategory::find($id);

        // 获取全部分类
        $categories = BusinessCategory::getCategories();

        View::assign('category', $category);
        View::assign('categories', $categories);

        return View::fetch();
    }

    /**
     * 保存更新的分类
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request)
    {
        ModelCategory::update($request->param('', '', 'trim'));
        return show(1, '更新分类成功！');
    }

    /**
     * 删除指定分类
     *
     * @param  string  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        try {
            BusinessCategory::delCategory((int)$id);
        } catch (\Exception $e) {
            return show(0, $e->getMessage());
        }

        return show(1, '删除分类成功！');
    }

    /**
     * 批量删除分类
     *
     * @param Request $request
     * @return void
     */
    public function alldel(Request $request)
    {
        // 将传过来的
        $data = explode(',', $request->post('id'));

        try {
            BusinessCategory::delCategory($data);
        } catch (\Exception $e) {
            return show(0, $e->getMessage());
        }

        return show(1, '批量删除分类成功！');
    }

    /**
     * 分类排序接口
     *
     * @param Request $request
     * @return json
     */
    public function sort(Request $request)
    {
        //TODO: 需要验证所输入的数据是否是数字
        $category = $request->param(['sort', 'id']);

        ModelCategory::update($category);

        return show(1, '更新排序成功！');
    }
}
