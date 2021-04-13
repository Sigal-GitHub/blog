<?php

declare(strict_types=1);

namespace app\admin\middleware;

use think\facade\Session;

class Auth
{
    /**
     * 处理请求 -- 前置中间件，判断是否登录
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        // 判断 Session 是否有 user 的值
        // 这里就是路由中间件的好处。不需要那么复杂的判断
        if (!Session::has('user'))
            return redirect('/login');

        // 如果session没有值，表示没登录
        // 1. 判断是否有session值
        // 2. 判断是否是login页面
        // 3. 判断是否是captcha页面
        // if (empty(session(config('admin.session_user'))) && !preg_match('/login/', $request->pathinfo()) && !preg_match('/captcha/', $request->pathinfo()))
        //     return redirect('/login/index');

        return $next($request);
    }
}
