<?php

declare(strict_types=1);

namespace app\admin\business;

use app\admin\model\User as ModelUser;
use Exception;
use think\facade\Session;

class User
{
    /**
     * 用户登录逻辑
     *
     * @param array $user 控制层接收的用户提交数据
     * @return boolean 成功返回 true，失败捕获错误
     */
    public static function userLogin(array $user): bool
    {
        try {
            // 通过模型获取数据
            $userInfo = ModelUser::getUserInfoByUsername($user['username']);

            // 如果数据为空，捕获错误信息
            if (!$userInfo)
                throw new Exception('账号或密码错误，登录失败!');

            // 判断密码是否一致
            if (md5(md5($user['password']) . config('admin.user_salt')) != $userInfo['password'])
                throw new Exception('账号或密码错误，登录失败!');

            // 判断账号是否冻结状态
            if ($userInfo['status'] == config('admin.user_status.freeze'))
                throw new Exception('账号被冻结，请联系管理员！');
        } catch (\Exception $e) {
            // 保存捕获的信息
            throw new Exception($e->getMessage());
        }

        //TODO: 还未确认是否配置日志

        // 其他信息没问题，将用户信息保存到 session 中
        Session::set('user', $userInfo);

        // 并更新用户表中的最后登录时间和最后登录 ip
        self::updateUserInfoById(config('admin.user_eidt.login'), $userInfo);

        return true;
    }

    /**
     * 根据用户 id 更新用户信息
     *
     * @param string $status 判断场景
     * @param array $userInfo 用户数据
     * @return boolean
     */
    public static function updateUserInfoById(string $status, $userInfo): bool
    {
        if ($status == config('admin.user_eidt.login')) {
            // 更新用户信息，如最后登录时间等...
            $userData = [
                'id' => $userInfo['id'],
                'last_login_ip' => request()->ip(),
                'last_login_time' => time()
            ];

            // 调用模型更新数据
            ModelUser::updateUserInfoById($userData);
        }

        return true;
    }
}
