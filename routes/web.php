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
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MoiController;
use App\Http\Controllers\Admin\RequirementController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PVCompanyController;
use App\Http\Controllers\Admin\SubmissionController;
use App\Http\Controllers\Admin\BDMSubmissionController;
use App\Http\Controllers\Admin\CommonController;
use App\Http\Controllers\Admin\InterviewController;
use App\Http\Controllers\Admin\VisaController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\EmployeeController;
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
    Route::get('logout', [LoginController::class,'logout']);
    /*Route::auth();
    Route::get('/', [DashboardController::class,'index'])->name('dashboard');*/

    Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');

    /*IMAGE UPLOAD IN SUMMER NOTE*/
    Route::post('image/upload', [ImageController::class,'upload_image']);

    Route::resource('profile_update', ProfileUpdateController::class);

    /* PERMISSION MANAGEMENT */
    Route::resource('permission', PermissionController::class);

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

    /* CATEGORY MANAGEMENT */
    Route::post('category/assign', [CategoryController::class,'assign'])->name('category.assign');
    Route::post('category/unassign', [CategoryController::class,'unassign'])->name('category.unassign');
    Route::resource('category', CategoryController::class);

    /* MOI MANAGEMENT */
    Route::post('moi/assign', [MoiController::class,'assign'])->name('moi.assign');
    Route::post('moi/unassign', [MoiController::class,'unassign'])->name('moi.unassign');
    Route::resource('moi', MoiController::class);

    /* PV COMPANY MANAGEMENT */
    Route::post('pv_company/assign', [PVCompanyController::class,'assign'])->name('pv_company.assign');
    Route::post('pv_company/unassign', [PVCompanyController::class,'unassign'])->name('pv_company.unassign');
    Route::resource('pv_company', PVCompanyController::class);

    /* REQUIREMENTS MANAGEMENT */
    Route::post('requirement/changeStatus/{id}', [RequirementController::class,'changeStatus'])->name('requirement.changeStatus');
    Route::post('requirement/assign', [RequirementController::class,'assign'])->name('requirement.assign');
    Route::post('requirement/unassign', [RequirementController::class,'unassign'])->name('requirement.unassign');
    Route::post('get_pocName', [RequirementController::class,'get_pocName'])->name('get_pocName');
    Route::post('get_pvDetails', [RequirementController::class,'get_pvDetails'])->name('get_pvDetails');
    Route::post('get_candidate', [CommonController::class,'getCandidate'])->name('get_candidate');
    Route::post('candidate_update', [RequirementController::class,'candidateUpdate'])->name('candidate.update');
    Route::get('my_requirement', [RequirementController::class,'myRequirement'])->name('my_requirement');
    Route::resource('requirement', RequirementController::class);
    Route::post('get_requirement', [CommonController::class,'getRequirement'])->name('get_requirement');
    Route::post('requirement/removeDocument/{id}', [RequirementController::class,'removeDocument'])->name('requirement.removeDocument');
    Route::post('get_submission', [CommonController::class,'getSubmissionData'])->name('get_submission');
    Route::get('requirement/repostReqirement/{id}', [RequirementController::class,'repostRequirement'])->name('requirement.repost');
    Route::post('requirement/saveRepostRequirement/{id}', [RequirementController::class,'saveRepostRequirement'])->name('requirement.saveRepostRequirement');

    /* SUBMISSION MANAGEMENT */
    Route::get('submission/new/{id}', [SubmissionController::class,'submissionAdd'])->name('submission.newAdd');
    Route::post('submission/assign/{id}', [SubmissionController::class,'assignSubmission'])->name('submission.assign');
    Route::get('my_requirements', [SubmissionController::class,'myRequirement'])->name('my_submission');
    Route::resource('submission', SubmissionController::class);
    Route::post('submission/getAlreadyAddedUserDetail/', [SubmissionController::class,'getAlreadyAddedUserDetail'])->name('submission.alreadyAddedUserDetail');
    Route::post('emp_name', [SubmissionController::class,'getEmpName'])->name('emp_name');
    Route::post('get_emp_details', [SubmissionController::class,'getEmpDetail'])->name('get_emp_details');
    Route::post('submission/checkPvCompany', [SubmissionController::class,'checkPvCompany'])->name('checkPvCompany');

    /* BDM SUBMISSION MANAGEMENT */
    Route::resource('bdm_submission', BDMSubmissionController::class);
    Route::post('bdm_submission/changePvStatus/{id}', [BDMSubmissionController::class,'changePvStatus'])->name('requirement.changePvStatus');
    Route::post('pv_reject_reason_update', [BDMSubmissionController::class,'pvRejectReasonUpdate'])->name('pv_reject_reason_update.update');
    Route::post('get_update_submission_data', [BDMSubmissionController::class,'getUpdateSubmissionData'])->name('get_update_submission_data');
    Route::post('update_submission_data', [BDMSubmissionController::class,'updateSubmissionData'])->name('update_submission_data');

    /* INTERVIEW MANAGEMENT */
    Route::resource('interview', InterviewController::class);
    Route::post('interview/changeInterviewStatus/{id}', [InterviewController::class,'changeInterviewStatus']);
    Route::post('interview/getCandidatesName/', [InterviewController::class,'getCandidatesName'])->name('interview.getCandidatesName');
    Route::post('interciew/getCandidateData', [InterviewController::class,'getCandidateData'])->name('interview.getCandidateData');
    Route::post('interview/removeDocument/{id}', [InterviewController::class,'removeDocument'])->name('interview.removeDocument');

    /* VISa MANAGEMENT */
    Route::post('visa/assign', [VisaController::class,'assign'])->name('visa.assign');
    Route::post('visa/unassign', [VisaController::class,'unassign'])->name('visa.unassign');
    Route::resource('visa', VisaController::class);

    /* SETTING MANAGEMENT */
    Route::resource('setting', SettingController::class);

    /* SETTING MANAGEMENT */
    Route::post('user/assign', [EmployeeController::class,'assign'])->name('employee.assign');
    Route::post('user/unassign', [EmployeeController::class,'unassign'])->name('employee.unassign');
    Route::resource('employee', EmployeeController::class);

    Auth::routes();
});

Route::get('/',[LoginController::class,'showAdminLoginForm'])->name('admin.login-view');
Route::get('/admin',[LoginController::class,'showAdminLoginForm'])->name('admin.login-view');
Route::post('/admin',[LoginController::class,'adminLogin'])->name('admin.login');
