<?php

declare(strict_types=1);

namespace app\admin\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class User extends Model
{
    /**
     * 根据用户名查找用户信息
     *
     * @param string $username
     * @return void  返回 Null 或对象实例
     */
    public static function getUserInfoByUsername(string $username)
    {
        $userInfo = self::where('username', $username)->find();

        if ($userInfo == null)
            return false;

        return $userInfo;
    }

    /**
     * 根据用户 ID 更新用户信息
     *
     * @param array $userData 需要更新的用户信息，包含 id
     * @return void 返回 Null 或对象实例
     */
    public static function updateUserInfoById(array $userData)
    {
        $res = self::update($userData);

        if ($res == null)
            return false;

        return $res;
    }
}
