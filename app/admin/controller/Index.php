<?php

declare(strict_types=1);

namespace app\admin\controller;

use think\facade\Session;
use think\facade\View;

class Index
{
    public function index()
    {
        return View::fetch();
    }

    public function console()
    {

        return View::fetch();
    }

    public function loginOut()
    {
        // Session 用不用助手函数都一样，助手函数也是调用 Session 类的
        Session::delete('user');
        return redirect('/login');
    }
}
