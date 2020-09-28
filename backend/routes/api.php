<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => ['auth:api']], function()
{
    Route::get('/logout', 'Auth\AuthenticationController@logout');
    Route::get('/user/info', 'UserController@AuthenticatedUserInfo');
    Route::resource('/user', 'UserController');
    Route::resource('/document', 'DocumentController');
    Route::resource('/user_position', 'UserPositionController');
    Route::resource('/pages', 'PageController');
    Route::resource('/pre-existing', 'PreExistingController');
    Route::resource('/procedures_type','ProcedureTypeController');
    Route::resource('/procedures','ProcedureController');
    Route::resource('/payment-method','PaymentMethodController');
    Route::resource('/provider','ProviderController');
    Route::resource('/doctor','DoctorController');
    Route::resource('/member','MemberController');
    // Route::get('/member/transaction-history/{id}','MemberController@getTransactionHistory');
    Route::resource('/diagnosis_list','DiagnosisListController');
    Route::post('/company/{id}','CompanyController@update');
    Route::resource('/company','CompanyController');
    Route::resource('/company_deployment','CompanyDeploymentController');
    Route::resource('/coverage_plan','CoveragePlanController');
    Route::resource('/benefit_type','BenefitTypeController');
    Route::resource('/payment_mode','PaymentModeController');
    Route::resource('/specialization','SpecializationController');
    Route::resource('/availment', 'AvailmentController');
    Route::resource('/cal','CalController');
    Route::resource('/cal_member','CalMemberController');
    Route::resource('/cal_status','CalStatusController');
    Route::resource('/cal_paid_count','CalPaidCountController');
    Route::resource('/audit_trail','AuditTrailController');
    Route::resource('/payment-term','PaymentTermController');
    Route::resource('/bank','BankController');
    Route::resource('/payable','PayableController');
    Route::resource('/payable/mark-close','PayableMainController');
    Route::resource('/member_company','MemberCompanyController');
    
    Route::resource('/count', 'GlobalController');
    
    // Member Query Selector
    Route::get('/show/member/{id}','ModelQueryController\MemberQueryController@show');
    Route::get('/show/member/transaction-history/{id}','ModelQueryController\MemberQueryController@getTransactionHistory');
    Route::get('/select/member/company','ModelQueryController\MemberQueryController@getCompanies');
    // Company Query Selector
    Route::get('/show/company/{id}','ModelQueryController\CompanyQueryController@show');
    Route::get('/select/company_list','ModelQueryController\CompanyQueryController@getAllCompanies');
    // Coverage Plan Query Selector
    Route::get('/select/coverage-plan','ModelQueryController\CoveragePlanQueryController@getAll');
    // Payable Query Selector
    Route::get('/select/payable/provider/{id}/availment','ModelQueryController\PayableQueryController@getProviderAvailment');
    Route::get('/select/payable/provider','ModelQueryController\PayableQueryController@getProvider');
    Route::get('/select/payable/provider_create','ModelQueryController\PayableQueryController@getProviderCreate');
    // Cal Query Selector
    Route::get('/show/cal/{id}','ModelQueryController\CalQueryController@show');
    // Availment Query Selector
    Route::get('/select/availment/member/coverage-plan/{id}/benefit-type','ModelQueryController\AvailmentQueryController@getMemberCoveragePlanBenefit');
    Route::get('/select/availment/coverage-plan/benefit-type/{id}/procedure','ModelQueryController\AvailmentQueryController@getBenefitTypeProcedure');
    Route::get('/select/availment/provider/{id}/payee-and-doctor','ModelQueryController\AvailmentQueryController@getProviderPayeeAndDoctor');
    Route::get('/show/availment/member/{id}/availment','ModelQueryController\AvailmentQueryController@getMemberAvailment');
    Route::get('/select/availment/member','ModelQueryController\AvailmentQueryController@getMemberAvailmentOptions');
    
    // Route::get('/report/availment-summary','ReportsController@index');
    
    Route::post('/availment/auto-save','AvailmentAutoSaveController@store');
    Route::get('/availment/auto-save/{id}','AvailmentAutoSaveController@show');
});

Route::post('/diagnosis_list/select','DiagnosisListController@selectExceptThisId');

Route::get('/export_file','Uploads\ProviderUploadController@export');
Route::post('/export_file/fail','Uploads\ProviderUploadController@exportFail');
Route::post('/import','Uploads\ProviderUploadController@import');

Route::get('/export_file/procedure','Uploads\ProcedureUploadController@export');
Route::post('/export_file/fail/procedure','Uploads\ProcedureUploadController@exportFail');
Route::post('/import/procedure','Uploads\ProcedureUploadController@import');
Route::get('/upload/fail/procedure','Uploads\ProcedureUploadController@exportFail');

Route::get('/export_file/diagnosis_list','Uploads\DiagnosisListUploadController@export');
Route::post('/import/diagnosis_list','Uploads\DiagnosisListUploadController@import');

//importation of cal
Route::post('/import/cal_member','Uploads\CalMemberUploadController@import');
Route::post('/import/cal_member/list','Uploads\CalMemberUploadController@getCalMemberList');
Route::get('/export/cal_member','Uploads\CalMemberUploadController@export');
Route::post('/export/cal_member/update_cal','Uploads\CalMemberUploadController@updateCal');

Route::get('/export/member','Uploads\MemberUploadController@export');
Route::post('/import/member','Uploads\MemberUploadController@import');
Route::post('/import/member_list','Uploads\MemberUploadController@getMemberList');

Route::get('export/company_exportation','Uploads\CompanyUploadController@exportation');
Route::post('/import/company','Uploads\CompanyUploadController@import');
Route::post('/import/company/list','Uploads\CompanyUploadController@getCompanyList');
Route::get('/export/company','Uploads\CompanyUploadController@export');
Route::get('export/member_exportation','Uploads\MemberUploadController@exportation');

Route::get('export/availment','Uploads\Availment\AvailmentUploadController@export_template');
Route::post('import/availment','Uploads\Availment\AvailmentUploadController@import');
Route::post('import/availment/list','Uploads\Availment\AvailmentUploadController@getAvailmentList');
Route::post('import/availment/save', 'Uploads\Availment\AvailmentUploadController@save_import');

Route::get('/test', 'TestController@test');
Route::get('/export/audit_trail','Uploads\AuditTrailUploadController@export');
Route::get('/export/member/availment_history','Uploads\MemberAvailmentHistoryController@export');

// Route::post('/import/member-list/test/{id}','Uploads\MemberUploadController@testing');

Route::post('/import/specialization','Uploads\SpecializationUploadController@import');
Route::post('/import/doctor','Uploads\DoctorUploadController@import');