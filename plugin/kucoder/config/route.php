<?php

use Webman\Route;
use support\Request;

/* *
 * kucoder路由 仅为示例 kucoder及kucoder的插件默认使用webman的默认路由 即/app/插件/[应用]/[子目录]/控制器/方法
 */
$controller_suffix = config('plugin.kucoder.app.controller_suffix');

Route::any('/kcadmin/{controller}/{action}',
    function (Request $request, string $controller, string $action) use ($controller_suffix) {
        //实际控制器类
        $controller_class = "plugin\\kucoder\\app\\admin\\controller\\" . ucfirst($controller) . $controller_suffix;
        //路由基础路径 用来鉴权
        $request->route_base_path = '/kcadmin/';
        //路由插件
        $request->plugin = 'kucoder';
        //路由应用
        $request->app = 'admin';
        //路由控制器
        $request->controller = $controller;
        //路由方法
        $request->action = $action;
        //访问控制器方法
        $classInstance = new $controller_class;
        return call_user_func([$classInstance, $action], $request);
    })->middleware([]);

//{dir}为控制器子目录
Route::any('/kcadmin/{dir}/{controller}/{action}',
    function (Request $request, string $dir, string $controller, string $action) use ($controller_suffix) {
        //实际控制器类
        $controller_class = "plugin\\kucoder\\app\\admin\\controller\\" . $dir . "\\" . ucfirst($controller) . $controller_suffix;
        //路由基础路径 用来鉴权
        $request->route_base_path = '/kcadmin/';
        //路由插件
        $request->plugin = 'kucoder';
        //路由应用
        $request->app = 'admin';
        //路由控制器
        $request->controller = $controller;
        //路由方法
        $request->action = $action;
        //访问控制器方法
        $classInstance = new $controller_class;
        return call_user_func([$classInstance, $action], $request);
    })->middleware([]);

Route::group('/kucoder', function () {
    //上传接口
    Route::any('/upload', [kucoder\controller\UploadController::class, 'upload']);
});

//当路由不存在时返回一个json数据，这在webman作为api接口时非常实用
Route::fallback(function () {
    kc_dump('kucoder路由不存在:'.request()->path().'  '.request()->uri());
    // kc_dump('注册的所有路由',Route::getRoutes());
    return json(['code' => 404, 'msg' => 'kucoder：页面 not found']);
});