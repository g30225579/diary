<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Auth::routes();

/**
 * 个人中心
 */
Route::middleware(['auth','private_password'])->prefix('space')->namespace('Space')->group(function(){
    // 首页
    Route::get('/', 'DiaryController@index');

    /**
     * 记事管理
     */
    Route::get('diary', 'DiaryController@index'); //列表页
    Route::get('diary/{id}/view', 'DiaryController@view'); //详情页
    Route::get('diary/create', 'DiaryController@create'); //新增页
    Route::post('diary/store', 'DiaryController@save'); //保存新增
    Route::get('diary/{id}/edit', 'DiaryController@edit'); //编辑页
    Route::post('diary/{id}/update', 'DiaryController@save'); //保存更新
});

Route::middleware('auth')->group(function(){
    // 文件上传 - 获取OSS签名
    Route::get('common/upload/sign', 'Upload\UploadController@getSign');
    // 文件上传成功标记
    Route::get('common/upload/oss_uploaded', 'Upload\UploadController@ossUploaded');

    // 二次验证独立密码
    Route::get('common/private_password', 'Auth\PrivatePasswordController@index');
    Route::post('common/private_password', 'Auth\PrivatePasswordController@check');
});
