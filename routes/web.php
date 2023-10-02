<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ImageController;
use App\Http\Controllers\Admin\ProfileUpdateController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BDMUserController;
use App\Http\Controllers\Admin\RecruiterUserController;
use App\Http\Controllers\Admin\TlBdmUserController;
use App\Http\Controllers\Admin\TlRecruiterUserController;
use App\Http\Controllers\Auth\RegisterController;
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
Route::prefix('admin')->middleware('auth:admin')->group(function () {
//Route::group(['prefix' => 'admin',  'admin/home'], function () {
    /*Route::get('logout', [LoginController::class,'logout']);
    Route::auth();
    Route::get('/', [DashboardController::class,'index'])->name('dashboard');*/

    Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');

    Route::get('/contactUs', [DashboardController::class,'contactUs'])->name('contactUs');
    Route::get('/contactUs/msg', [DashboardController::class,'contactUsMsg'])->name('contactUsMsg');
    Route::delete('/contactUs/{id}', [DashboardController::class,'contactUs_destroy'])->name('contactUsDelete');

    /*IMAGE UPLOAD IN SUMMER NOTE*/
    Route::post('image/upload', [ImageController::class,'upload_image']);

    Route::resource('profile_update', ProfileUpdateController::class);

    /* USER MANAGEMENT */
    Route::post('user/assign', [UserController::class,'assign'])->name('user.assign');
    Route::post('user/unassign', [UserController::class,'unassign'])->name('user.unassign');
    Route::resource('user', UserController::class);

    /* BDM USER MANAGEMENT */
    Route::post('bdm_user/assign', [BDMUserController::class,'assign'])->name('bdm_user.assign');
    Route::post('bdm_user/unassign', [BDMUserController::class,'unassign'])->name('bdm_user.unassign');
    Route::resource('bdm_user', BDMUserController::class);

    /* RECRUITER USER MANAGEMENT */
    Route::post('recruiter_user/assign', [RecruiterUserController::class,'assign'])->name('recruiter_user.assign');
    Route::post('recruiter_user/unassign', [RecruiterUserController::class,'unassign'])->name('recruiter_user.unassign');
    Route::resource('recruiter_user', RecruiterUserController::class);

    /* TL RECRUITER USER MANAGEMENT */
    Route::post('tl_recruiter_user/assign', [TlRecruiterUserController::class,'assign'])->name('tl_recruiter_user.assign');
    Route::post('tl_recruiter_user/unassign', [TlRecruiterUserController::class,'unassign'])->name('tl_recruiter_user.unassign');
    Route::resource('tl_recruiter_user', TlRecruiterUserController::class);

    /* TL BDM USER MANAGEMENT */
    Route::post('tl_bdm_user/assign', [TlBdmUserController::class,'assign'])->name('tl_bdm_user.assign');
    Route::post('tl_bdm_user/unassign', [TlBdmUserController::class,'unassign'])->name('tl_bdm_user.unassign');
    Route::resource('tl_bdm_user', TlBdmUserController::class);

    Auth::routes();
});

Route::get('/',[LoginController::class,'showAdminLoginForm'])->name('admin.login-view');
Route::get('/admin',[LoginController::class,'showAdminLoginForm'])->name('admin.login-view');
Route::post('/admin',[LoginController::class,'adminLogin'])->name('admin.login');

