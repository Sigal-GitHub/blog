<?php

use think\facade\Route;

// 用户登录
Route::rule('login', 'login/index');
// 登录检测
Route::post('checklogin', 'login/checklogin');

Route::group(
    '/',
    function () {
        // 后台首页
        Route::get('/', 'index/index');                                  // 主页
        Route::get('console', 'index/console');                          // 欢迎页
        Route::get('loginOut', 'index/loginOut');                        // 退出接口

        // 分类模块
        Route::get('category', 'category/index');                        // 分类列表首页
        Route::get('category/table', 'category/table');                  // 获取所有分类接口
        Route::get('category/create', 'category/create');                // 分类创建页
        Route::post('category/save', 'category/save');                   // 分类保存接口
        Route::delete('category/delete/:id', 'category/delete');         // 分类删除接口
        Route::post('category/sort', 'category/sort');                   // 分类排序接口
        Route::get('category/edit/:id', 'category/edit');                // 分类编辑页
        Route::put('category/update/:id', 'category/update');            // 更新接口
        Route::post('category/alldel', 'category/alldel');               // 批量删除接口

        // 文章模块
        Route::get('article', 'article/index');                          // 文章列表页
        Route::get('article/create', 'article/create');                  // 写文章页
        Route::post('article/uploadcover', 'article/uploadcover');       // 封面上传接口
        Route::post('article/save/:status', 'article/save');             // 文章保存接口
        Route::get('article/table', 'article/table');                    // 获取所有文章接口
        Route::get('article/edit/:id', 'article/edit');                  // 文章编辑页
        Route::get('article/draft', 'article/draft');                    // 文章草稿页
        Route::get('article/drafttable', 'article/drafttable');          // 获取所有草稿接口
        Route::delete('article/delete/:id', 'article/delete');           // 文章删除接口
        Route::post('article/alldel', 'article/alldel');                 // 文章批量删除接口
        Route::put('article/updatestatus', 'article/updatestatus');      // 状态更新接口
        Route::get('article/search', 'article/search');                  // 标题搜索接口
        Route::post('article/uploadimg', 'article/uploadimg');           // 图片上传接口
        Route::put('article/update/:id', 'article/update');              // 文章更新接口

        // 友链模块
        Route::get('link', 'link/index');                                // 友链列表页
        Route::get('link/table', 'link/table');                          // 友链获取接口
        Route::get('link/create', 'link/create');                        // 友链创建页
        Route::post('link/save', 'link/save');                           // 友链保存接口
        Route::put('link/status', 'link/status');                        // 友链状态
        Route::delete('link/delete/:id', 'link/delete');                 // 友链删除接口
        Route::post('link/alldel', 'link/alldel');                       // 批量删除接口
        Route::get('link/edit/:id', 'link/edit');                        // 友链编辑接口
        Route::put('link/update/:id', 'link/update');                    // 友链更新接口

        // 标签模块
        Route::get('tag', 'tag/index');                                  // 标签列表页
        Route::get('tag/table', 'tag/table');                            // 获取所有标签接口
        Route::get('tag/create', 'tag/create');                          // 标签创建页
        Route::post('tag/save', 'tag/save');                             // 标签保存接口
        Route::delete('tag/delete/:id', 'tag/delete');                   // 标签删除接口
        Route::post('tag/alldel', 'tag/alldel');                         // 标签批量删除接口
        Route::post('tag/rname', 'tag/rname');                           // 标签重命名接口
    }
)->middleware(\app\admin\middleware\Auth::class);
