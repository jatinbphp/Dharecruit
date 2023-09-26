<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('/', 'HomeController@index')->name('home');
Auth::routes();

Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/', 'AdminController@index')->name('admin.index');
    Route::get('change-password', 'AdminController@change_password')->name('admin.change-password');
    Route::post('update-password', 'AdminController@update_password')->name('admin.update-password');
    Route::get('create-user', 'AdminController@create_user')->name('admin.create-user');
    Route::post('store-user', 'AdminController@store_user')->name('admin.store-user');
    Route::any('edit-user/{id}', 'AdminController@edit_user')->name('admin.edit-user');
    Route::post('update-user', 'AdminController@update_user')->name('admin.update-user');
    Route::any('users', 'AdminController@users')->name('admin.users');
    Route::any('get_users', 'AdminController@get_users')->name('admin.get_users');
    Route::any('statusUser', 'AdminController@statusUser')->name('admin.statusUser');
    Route::any('destroyUser', 'AdminController@destroyUser')->name('admin.destroyUser');
});
