<?php

declare(strict_types=1);

namespace app\admin\validate;

use think\Validate;

class Image extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'image' => 'fileSize:2097152|fileExt:jpg,png,jpeg,gif',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'image.fileSize' => '图片大小不能超过 2MB！',
        'image.fileExt'  => '图片格式错误！'
    ];

    // protected $scene = [
    //     'cover'  => ['username', 'password','captcha'],
    //     'modify' => ['password']
    // ];
}
