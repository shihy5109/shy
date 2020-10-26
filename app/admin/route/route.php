<?php
/**
 * Created by Shy
 * Date 2020/10/13
 * Time 13:45
 */
use \think\facade\Route;
//Route::rule('add','admin/index/add','GET|POST')->cache(120); //添加

//index
Route::rule('login','admin/index/login'); //测试
Route::rule('index','admin/index/index');
Route::rule('add','admin/index/add','POST');
Route::rule('delete','admin/index/delete','GET');
Route::rule('status','admin/index/status','GET');
Route::rule('photo','admin/index/photo','POST'); //上传图片
Route::rule('unzip','admin/index/unzip','POST');  //上传压缩包
Route::rule('excel_get','admin/index/excel_get'); //excel导入
Route::rule('excel_put','admin/index/excel_put'); //excel导出
Route::rule('test','admin/index/test','GET|POST');

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
Route::rule('menu/log','admin/menu/log'); //管理员日志


//categories
Route::rule('categories/index','admin/categories/index');
Route::rule('categories/add','admin/categories/add');
Route::rule('categories/update','admin/categories/update');
Route::rule('categories/delete','admin/categories/delete');
Route::rule('categories/sort','admin/categories/sort');
