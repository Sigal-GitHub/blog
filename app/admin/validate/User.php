<?php

declare(strict_types=1);

namespace app\admin\validate;

use think\Validate;

class User extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'username' => 'require|length:4,20|token',
        'password' => 'require|length:6,20',
        'captcha'  => 'captcha'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'username.require' => '用户名不能为空！',
        'username.length'  => '用户名长度必须是4-20位！',
        'password.require' => '用户密码不能为空！',
        'password.length'  => '用户密码长度必须是6-20位！',
        'captcha'          => '验证码错误！',
        'username.token'   => '页面失效，请刷新！'
    ];

    protected $scene = [
        'check'  => ['username', 'password','captcha'],
        'modify' => ['password']
    ];
}
