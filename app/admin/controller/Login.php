<?php

declare(strict_types=1);

namespace app\admin\controller;

use app\admin\business\User as UserBusiness;
use think\exception\ValidateException;
use think\Request;
use app\admin\validate\User as ValidateUser;

class Login
{
    public function index()
    {
        return view();
    }

    /**
     * 登录接口
     *
     * @param Request $request 前端 ajax 提交过来的数据
     * @return json 
     */
    public function checkLogin(Request $request)
    {
        // 判断是否是 ajax 和 post 提交
        if (!$request->isAjax() && !$request->isPost())
            return show(0, '非法提交');

        // 接收数据并过滤
        $getUserByForm = $request->post('', '', 'trim');

        // 验证数据的合法性
        try {
            // tp6 验证器验证数据
            validate(ValidateUser::class)->scene('check')->check($getUserByForm);

            // 调用业务层处理数据
            UserBusiness::userLogin($getUserByForm);
        } catch (ValidateException $e) {
            // 返回验证器捕获的错误
            return show(0, $e->getError());
        } catch (\Exception $e) {
            // 返回业务层捕获的错误
            return show(0, $e->getMessage());
        }

        // 所有条件都满足的情况下，返回成功的结果
        return show(1, '登录成功！正在跳转。');
    }
}
