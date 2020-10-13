<?php
use \think\facade\Route;


//Route::rule('add','admin/index/add','GET|POST')->cache(120); //添加
//index
Route::rule('login','admin/index/login'); //测试
Route::rule('index','admin/index/index','GET|POST'); //添加
Route::rule('add','admin/index/add','POST');
Route::rule('delete','admin/index/delete','GET');
Route::rule('status','admin/index/status','GET');
Route::rule('photo','admin/index/photo');
Route::rule('excel_get','admin/index/excel_get');
Route::rule('excel_put','admin/index/excel_put');
Route::rule('test','admin/index/test','GET|POST'); //测试

//rbac
Route::rule('rbac/index','admin/rbac/index');
Route::rule('rbac/update','admin/rbac/update');
Route::rule('rbac/delete','admin/rbac/delete');
Route::rule('rbac/authority','admin/rbac/authority');


//menu
Route::rule('menu/index','admin/menu/index');
Route::rule('menu/update','admin/menu/update');
Route::rule('menu/delete','admin/menu/delete');
Route::rule('menu/authority','admin/menu/authority');
