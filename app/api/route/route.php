<?php
/**
 * Created by Shy
 * Date 2020/10/13
 * Time 13:45
 */
use \think\facade\Route;
//Route::rule('add','admin/index/add','GET|POST')->cache(120); //添加


Route::rule('order/addCart','api/order/addCart');
Route::rule('order/delCart','api/order/delCart');

