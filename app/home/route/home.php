<?php

use think\facade\Route;

// 用户登录
Route::get('/', 'index/index');
// 用户提交
// Route::post('checklogin', 'login/checklogin');
Route::get('article/:id','index/article')->ext('html');

