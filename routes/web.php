<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);
Route::get('/login2', function () {
    return view('auth.login2');
});

// Auth::routes();
Auth::routes(['register' => false]);
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index']);

Route::group(['prefix' => 'admin/', 'middleware' => ['backnotallowed', 'role:Administrator']], function () {
    Route::get('dashboard', [App\Http\Controllers\AdminController::class, 'index'])->name('admindashboard');

    Route::get('/manageAccount', [App\Http\Controllers\AdminController::class, 'account'])->name('account');
    Route::post('/manageAccount', [App\Http\Controllers\AdminController::class, 'addaccount'])->name('addaccount');
    Route::delete('/manageAccount/delete/{id}', [App\Http\Controllers\AdminController::class, 'deleteAccount']);
    Route::patch('/manageAccount/update/{id}', [App\Http\Controllers\AdminController::class, 'updateAccount']);
    Route::get('/manageAccount/view/{id}', [App\Http\Controllers\AdminController::class, 'getUserById'])->name('view');

    Route::get('/facility', [App\Http\Controllers\AdminController::class, 'facility'])->name('facility');
    Route::post('/departmentsave', [App\Http\Controllers\AdminController::class, 'adddepartment']);
    Route::post('/buildingsave', [App\Http\Controllers\AdminController::class, 'addbuilding']);
    Route::patch('/building/update/{id}', [App\Http\Controllers\AdminController::class, 'updatebuilding']);
    Route::patch('/department/update/{id}', [App\Http\Controllers\AdminController::class, 'updatedepartment']);
    Route::delete('/building/delete/{id}', [App\Http\Controllers\AdminController::class, 'deletebuilding']);
    Route::delete('/department/delete/{id}', [App\Http\Controllers\AdminController::class, 'deletedepartment']);

    Route::get('/supplier_items', [App\Http\Controllers\AdminController::class, 'supplier_items'])->name('supplier_items');
    Route::post('/suppliersave', [App\Http\Controllers\AdminController::class, 'addsupplier']);
    Route::post('/itemsave', [App\Http\Controllers\AdminController::class, 'additem']);
    Route::patch('/supplier/update/{id}', [App\Http\Controllers\AdminController::class, 'updatesupplier']);
    Route::patch('/item/update/{id}', [App\Http\Controllers\AdminController::class, 'updateitem']);
    Route::delete('/supplier/delete/{id}', [App\Http\Controllers\AdminController::class, 'deletesupplier']);
    Route::delete('/item/delete/{id}', [App\Http\Controllers\AdminController::class, 'deleteitem']);

    Route::post('/supplieritemsave', [App\Http\Controllers\AdminController::class, 'addsupplieritem']);
    Route::patch('/supplieritem/update/{id}', [App\Http\Controllers\AdminController::class, 'updatesupplieritem']);
    Route::delete('/supplieritem/delete/{id}', [App\Http\Controllers\AdminController::class, 'deletesupplieritem']);


    Route::get('/profile', [App\Http\Controllers\AdminController::class, 'profile'])->name('profile');
    Route::patch('/profile/update/{id}', [App\Http\Controllers\AdminController::class, 'updateprofile']);
    Route::patch('/profile/update/password/{id}', [App\Http\Controllers\AdminController::class, 'updatepasword']);
    Route::post('/profile/admin-changeProfilePic', [App\Http\Controllers\AdminController::class, 'changeProfilePic'])->name('adminProfilePic');

    Route::get('/purchase_request', [App\Http\Controllers\AdminController::class, 'purchase_request'])->name('purchase_request');
    Route::post('/requisitionsave', [App\Http\Controllers\AdminController::class, 'addrequisition']);
    Route::get('/purchase_request/view/{pr_no}', [App\Http\Controllers\AdminController::class, 'view_purchase_request'])->name('vpr');
    Route::get('/purchase_request/view_track/{pr_no}', [App\Http\Controllers\AdminController::class, 'view_track_pr'])->name('ad_pr_track');
    Route::get('/purchase_order', [App\Http\Controllers\AdminController::class, 'purchase_order'])->name('ad_purchase_order');
    Route::get('/po_for_approval/view/{pr_no}', [App\Http\Controllers\AdminController::class, 'view_approved_po'])->name('view_approved_po_ad');
    Route::get('/purchase_order/view_track/{pr_no}', [App\Http\Controllers\AdminController::class, 'view_track_po'])->name('ad_po_track');

    Route::get('/ad_to_received', [App\Http\Controllers\AdminController::class, 'to_received'])->name('ad_to_received');
    Route::get('/to_received/view/{pr_no}/{po_no}', [App\Http\Controllers\AdminController::class, 'view_po_to_received'])->name('view_po_to_received_ad');
    Route::get('/purchase_request/view/denied/{pr_no}', [App\Http\Controllers\AdminController::class, 'view_denied_pr'])->name('add_denied');

    Route::get('/approved/view/{dln}/{po_no}/{user_id}', [App\Http\Controllers\AdminController::class, 'view_po_to_received'])->name('view_forwarded_add');
    Route::patch('/approved/view/{dln}/{po_no}/{staff_id}/received_items', [App\Http\Controllers\AdminController::class, 'received_item']);
    Route::get('/approved/view/track/{dln}/{po_no}/{user_id}', [App\Http\Controllers\AdminController::class, 'track_po_to_received'])->name('track_forwarded_add');

    Route::post('/approved/view/{dln}/{po_no}/{staff_id}/report_item', [App\Http\Controllers\AdminController::class, 'report_item']);
    Route::get('/approved/view/{rn}', [App\Http\Controllers\AdminController::class, 'view_reported_item'])->name('view_reported_item_add');
});
// requestor
Route::group(['prefix' => 'requestor/', 'middleware' => ['backnotallowed', 'role:Requestor']], function () {
    Route::get('dashboard', [App\Http\Controllers\RequestorController::class, 'index'])->name('requestordashboard');

    Route::get('/profile', [App\Http\Controllers\RequestorController::class, 'profile'])->name('req_profile');
    Route::patch('/profile/update/{id}', [App\Http\Controllers\RequestorController::class, 'updateprofile']);
    Route::patch('/profile/update/password/{id}', [App\Http\Controllers\RequestorController::class, 'updatepasword']);
    Route::post('/profile/requestor-changeProfilePic', [App\Http\Controllers\RequestorController::class, 'changeProfilePic'])->name('requestorProfilePic');

    Route::get('/purchase_request', [App\Http\Controllers\RequestorController::class, 'purchase_request'])->name('req_purchase_request');
    Route::post('/requisitionsave', [App\Http\Controllers\RequestorController::class, 'addrequisition']);
    Route::get('/purchase_request/view/{pr_no}', [App\Http\Controllers\RequestorController::class, 'view_purchase_request']);
    Route::get('/purchase_request/view/{pr_no}', [App\Http\Controllers\RequestorController::class, 'view_purchase_request'])->name('req_vpr');
    Route::get('/purchase_request/view_track/{pr_no}', [App\Http\Controllers\RequestorController::class, 'view_track_pr'])->name('req_pr_track');
    Route::get('/purchase_order', [App\Http\Controllers\RequestorController::class, 'purchase_order'])->name('req_purchase_order');
    Route::get('/po_for_approval/view/{pr_no}', [App\Http\Controllers\RequestorController::class, 'view_approved_po'])->name('view_approved_po_req');
    Route::get('/purchase_order/view_track/{pr_no}', [App\Http\Controllers\RequestorController::class, 'view_track_po'])->name('req_po_track');
    Route::get('/to_received', [App\Http\Controllers\RequestorController::class, 'to_received'])->name('to_received');
    Route::get('/to_received/view/{pr_no}/{po_no}', [App\Http\Controllers\RequestorController::class, 'view_po_to_received'])->name('view_po_to_received');
    Route::get('/purchase_request/view/denied/{pr_no}', [App\Http\Controllers\RequestorController::class, 'view_denied_pr'])->name('req_denied');
    Route::get('/purchase_request/view/hold/{pr_no}', [App\Http\Controllers\RequestorController::class, 'view_denied_pr'])->name('req_hold');

    Route::get('/approved/view/{dln}/{po_no}/{staff_id}', [App\Http\Controllers\RequestorController::class, 'view_po_to_received'])->name('view_forwarded_req');
    Route::patch('/approved/view/{dln}/{po_no}/{staff_id}/received_items', [App\Http\Controllers\RequestorController::class, 'received_item']);
    Route::get('/approved/view/{dln}/{po_no}/{staff_id}', [App\Http\Controllers\RequestorController::class, 'view_po_to_received'])->name('view_forwarded_req');
    Route::get('/approved/view/track/{dln}/{po_no}/{staff_id}', [App\Http\Controllers\RequestorController::class, 'track_po_to_received'])->name('track_forwarded_req');

    Route::post('/approved/view/{dln}/{po_no}/{staff_id}/report_item', [App\Http\Controllers\RequestorController::class, 'report_item']);
    Route::get('/approved/view/{rn}', [App\Http\Controllers\RequestorController::class, 'view_reported_item'])->name('view_reported_item_req');
});
// approver
Route::group(['prefix' => 'approver/', 'middleware' => ['backnotallowed', 'role:Approver']], function () {
    Route::get('dashboard', [App\Http\Controllers\ApproverController::class, 'index'])->name('approverdashboard');

    Route::get('/profile', [App\Http\Controllers\ApproverController::class, 'profile'])->name('app_profile');
    Route::patch('/profile/update/{id}', [App\Http\Controllers\ApproverController::class, 'updateprofile']);
    Route::patch('/profile/update/password/{id}', [App\Http\Controllers\ApproverController::class, 'updatepasword']);
    Route::post('/profile/approver-changeProfilePic', [App\Http\Controllers\ApproverController::class, 'changeProfilePic'])->name('approverProfilePic');

    Route::get('/purchase_request', [App\Http\Controllers\ApproverController::class, 'purchase_request'])->name('app_purchase_request');
    Route::post('/requisitionsave', [App\Http\Controllers\ApproverController::class, 'addrequisition']);
    Route::get('/purchase_request/view/{pr_no}', [App\Http\Controllers\ApproverController::class, 'view_purchase_request'])->name('app_vpr');

    Route::get('/new_pr', [App\Http\Controllers\ApproverController::class, 'new_pr'])->name('new_pr');
    Route::get('/new_pr/view/{pr_no}', [App\Http\Controllers\ApproverController::class, 'view_new_pr'])->name('view_new_pr');
    Route::patch('/new_pr/view/update_new_pr/{pr_no}', [App\Http\Controllers\ApproverController::class, 'update_new_pr']);
    Route::patch('/new_pr/view/deny_new_pr/{pr_no}', [App\Http\Controllers\ApproverController::class, 'deny_new_pr']);

    Route::get('/pr_for_verification', [App\Http\Controllers\ApproverController::class, 'pr_for_verification'])->name('pr_for_verification');
    Route::get('/pr_for_verification/view/{pr_no}', [App\Http\Controllers\ApproverController::class, 'view_pr_for_verification'])->name('pr_verify');
    Route::patch('/pr_for_verification/view/verify_pr/{pr_no}', [App\Http\Controllers\ApproverController::class, 'verify_pr']);
    Route::patch('/pr_for_verification/view/verify_submit/{pr_no}', [App\Http\Controllers\ApproverController::class, 'verify_submit']);
    Route::get('/pr_for_verification/view/verified/{pr_no}', [App\Http\Controllers\ApproverController::class, 'update_pr_for_verification'])->name('update_verified');
    Route::patch('/pr_for_verification/view/verified/update/{pr_no}', [App\Http\Controllers\ApproverController::class, 'update_verified_pr']);
    Route::patch('/pr_for_verification/view/verified/delete/{id}', [App\Http\Controllers\ApproverController::class, 'delete_verified_item']);
    Route::patch('/pr_for_verification/view/verified/{pr_no}/update_quantity/{id}', [App\Http\Controllers\ApproverController::class, 'update_quantity']);

    Route::patch('/new_pr/view/{pr_no}/checkverifyitem/update/{id}', [App\Http\Controllers\ApproverController::class, 'checkverifyitem']);
    Route::patch('/new_pr/view/{pr_no}/denyitem/update/{id}', [App\Http\Controllers\ApproverController::class, 'denyitem']);
    Route::get('/pr_for_approval', [App\Http\Controllers\ApproverController::class, 'pr_for_approval'])->name('pr_for_approval');
    Route::get('/pr_for_approval/view/{pr_no}', [App\Http\Controllers\ApproverController::class, 'view_pr_for_approval'])->name('pr_approval');
    Route::post('/pr_for_approval/view/approve/{pr_no}', [App\Http\Controllers\ApproverController::class, 'approve_pr']);
    Route::get('/pr_approved/view/{pr_no}', [App\Http\Controllers\ApproverController::class, 'view_approved_pr'])->name('view_approved_pr_app');

    Route::get('/po_for_approval', [App\Http\Controllers\ApproverController::class, 'po_for_approval'])->name('po_for_approval');
    Route::get('/po_prepared/view/{pr_no}', [App\Http\Controllers\ApproverController::class, 'view_prepared_po'])->name('view_prepared_po_app');
    Route::patch('/po_prepared/view/verify/{po_no}', [App\Http\Controllers\ApproverController::class, 'verify_po']);
    Route::get('/view_verified_po/view/{pr_no}', [App\Http\Controllers\ApproverController::class, 'view_verified_po'])->name('view_verified_po');
    Route::patch('/view_verified_po/view/approve/{po_no}', [App\Http\Controllers\ApproverController::class, 'approve_po']);
    Route::get('/po_for_approval/view/{pr_no}', [App\Http\Controllers\ApproverController::class, 'view_approved_po'])->name('view_approved_po_app');

    Route::get('/view_approved_po/vieww/{pr_no}', [App\Http\Controllers\ApproverController::class, 'view_approved_po'])->name('view_approving_po');
    Route::patch('/view_approved_po/view/approveCT/{po_no}', [App\Http\Controllers\ApproverController::class, 'approveCT_po']);
    Route::patch('/view_approved_po/view/approveCEO/{po_no}', [App\Http\Controllers\ApproverController::class, 'approveCEO_po']);
    Route::get('/view_approved_po/viewww/{pr_no}', [App\Http\Controllers\ApproverController::class, 'view_approved_po'])->name('view_approving_po_app');
    Route::get('/view_approved_po/view/{pr_no}', [App\Http\Controllers\ApproverController::class, 'view_approved_po'])->name('view_approving_po_vip');
    Route::get('/purchase_request/view_track/{pr_no}', [App\Http\Controllers\ApproverController::class, 'view_track_pr'])->name('app_pr_track');
    Route::get('/purchase_order', [App\Http\Controllers\ApproverController::class, 'purchase_order'])->name('app_purchase_order');
    // Route::get('/po_for_approval/view/{pr_no}', [App\Http\Controllers\ApproverController::class, 'view_approved_po'])->name('view_approved_po_app');
    Route::get('/purchase_order/view_track/{pr_no}', [App\Http\Controllers\ApproverController::class, 'view_track_po'])->name('app_po_track');

    Route::get('/app_to_received', [App\Http\Controllers\ApproverController::class, 'to_received'])->name('app_to_received');
    Route::get('/to_received/view/{pr_no}/{po_no}', [App\Http\Controllers\ApproverController::class, 'view_po_to_received'])->name('view_po_to_received_app');
    Route::get('/purchase_request/view/denied/{pr_no}', [App\Http\Controllers\ApproverController::class, 'view_denied_pr'])->name('app_denied');

    Route::get('/to_transmit', [App\Http\Controllers\ApproverController::class, 'to_transmit'])->name('to_transmit');
    Route::get('/forwarded/view/{dln}/{po_no}/', [App\Http\Controllers\ApproverController::class, 'view_forwarded'])->name('view_forwarded');
    Route::get('/forwardedd/view/{dln}/{po_no}/', [App\Http\Controllers\ApproverController::class, 'view_forwardedd'])->name('view_forwardedd');
    Route::get('/forwarded/view_done/{dln}/{po_no}/', [App\Http\Controllers\ApproverController::class, 'view_forwardeddd'])->name('view_forwardeddd');
    Route::patch('/approved/app/view/{dln}/{po_no}/{staff_id}/approved_forward', [App\Http\Controllers\ApproverController::class, 'approved_forward']);
    Route::get('/approved/view/{dln}/{po_no}/{user_id}', [App\Http\Controllers\ApproverController::class, 'view_po_to_received'])->name('view_forwarded_app');
    Route::patch('forwardedd/view/{dln}/{staff_id}/received_items', [App\Http\Controllers\ApproverController::class, 'received_item']);
    Route::get('/approved/app/view/{dln}/{po_no}/{user_id}', [App\Http\Controllers\ApproverController::class, 'view_po_to_received'])->name('view_forwarded_app');
    Route::get('/approved/view/{dln}/{po_no}', [App\Http\Controllers\ApproverController::class, 'track_po_to_received'])->name('track_forwarded_app');
    // Route::get('/approved/view/track/{dln}/{po_no}', [App\Http\Controllers\ApproverController::class, 'track_po_to_received'])->name('track_forwarded_app');

    Route::post('/forwarded/view/{dln}/{staff_id}/report_item', [App\Http\Controllers\ApproverController::class, 'report_item']);
    Route::get('/approved/view/{rn}', [App\Http\Controllers\ApproverController::class, 'view_reported_item'])->name('view_reported_item_app');
});
// validator
Route::group(['prefix' => 'validator/', 'middleware' => ['backnotallowed', 'role:Validator']], function () {
    Route::get('dashboard', [App\Http\Controllers\ValidatorController::class, 'index'])->name('validatordashboard');

    Route::get('/profile', [App\Http\Controllers\ValidatorController::class, 'profile'])->name('val_profile');
    Route::patch('/profile/update/{id}', [App\Http\Controllers\ValidatorController::class, 'updateprofile']);
    Route::patch('/profile/update/password/{id}', [App\Http\Controllers\ValidatorController::class, 'updatepasword']);
    Route::post('/profile/validator-changeProfilePic', [App\Http\Controllers\ValidatorController::class, 'changeProfilePic'])->name('validatorProfilePic');

    Route::get('/purchase_request', [App\Http\Controllers\ValidatorController::class, 'purchase_request'])->name('val_purchase_request');
    Route::post('/requisitionsave', [App\Http\Controllers\ValidatorController::class, 'addrequisition']);
    Route::get('/purchase_request/view/{pr_no}', [App\Http\Controllers\ValidatorController::class, 'view_purchase_request'])->name('val_vpr');

    Route::get('/pr_check_fund', [App\Http\Controllers\ValidatorController::class, 'pr_check_fund'])->name('pr_check_fund');
    Route::get('/pr_check_fund/view/{pr_no}', [App\Http\Controllers\ValidatorController::class, 'view_pr_check_fund'])->name('view_pr_check_fund');
    Route::post('/pr_check_fund/view/check/{pr_no}', [App\Http\Controllers\ValidatorController::class, 'check_pr']);
    Route::get('/pr_check_fund/view/checked/{pr_no}', [App\Http\Controllers\ValidatorController::class, 'view_pr_check_fund'])->name('view_pr_checked_fund');

    Route::get('/po_for_approval', [App\Http\Controllers\ValidatorController::class, 'po_for_approval'])->name('po_for_approval_val');
    Route::get('/po_prepared/view/{pr_no}', [App\Http\Controllers\ValidatorController::class, 'view_prepared_po'])->name('view_prepared_po_val');
    Route::patch('/po_prepared/view/verify/{po_no}', [App\Http\Controllers\ValidatorController::class, 'verify_po']);
    Route::get('/view_verified_po/view/{pr_no}', [App\Http\Controllers\ValidatorController::class, 'view_verified_po'])->name('view_verified_po_val');
    // Route::patch('/view_verified_po/view/approve/{po_no}', [App\Http\Controllers\ValidatorController::class, 'approve_po']);
    Route::get('/view_approved_po/view/{pr_no}', [App\Http\Controllers\ValidatorController::class, 'view_approved_po'])->name('view_approving_po_val');
    Route::get('/purchase_request/view_track/{pr_no}', [App\Http\Controllers\ValidatorController::class, 'view_track_pr'])->name('val_pr_track');
    Route::get('/purchase_order', [App\Http\Controllers\ValidatorController::class, 'purchase_order'])->name('val_purchase_order');
    Route::get('/po_for_approval/view/{pr_no}', [App\Http\Controllers\ValidatorController::class, 'view_approved_po'])->name('view_approved_po_val');
    Route::get('/purchase_order/view_track/{pr_no}', [App\Http\Controllers\ValidatorController::class, 'view_track_po'])->name('val_po_track');

    Route::get('/val_to_received', [App\Http\Controllers\ValidatorController::class, 'to_received'])->name('val_to_received');
    Route::get('/to_received/view/{pr_no}/{po_no}/{staff_id}', [App\Http\Controllers\ValidatorController::class, 'view_po_to_received'])->name('view_po_to_received_val');
    Route::get('/purchase_request/view/denied/{pr_no}', [App\Http\Controllers\ValidatorController::class, 'view_denied_pr'])->name('val_denied');

    Route::get('/approved/view/{dln}/{po_no}/{staff_id}', [App\Http\Controllers\ValidatorController::class, 'view_po_to_received'])->name('view_forwarded_val');
    Route::patch('/approved/view/{dln}/{po_no}/{staff_id}/received_items', [App\Http\Controllers\ValidatorController::class, 'received_item']);
    Route::get('/approved/view/track/{dln}/{po_no}/{staff_id}', [App\Http\Controllers\ValidatorController::class, 'track_po_to_received'])->name('track_forwarded_val');

    Route::post('/approved/view/{dln}/{po_no}/{staff_id}/report_item', [App\Http\Controllers\ValidatorController::class, 'report_item']);
    Route::get('/approved/view/{rn}', [App\Http\Controllers\ValidatorController::class, 'view_reported_item'])->name('view_reported_item_val');
    Route::patch('/pr_check_fund/view/{pr_no}/hold_new_pr', [App\Http\Controllers\ValidatorController::class, 'hold_new_pr']);
});
// processor
Route::group(['prefix' => 'processor/', 'middleware' => ['backnotallowed', 'role:Processor']], function () {
    Route::get('dashboard', [App\Http\Controllers\ProcessorController::class, 'index'])->name('processordashboard');

    Route::get('/profile', [App\Http\Controllers\ProcessorController::class, 'profile'])->name('pro_profile');
    Route::patch('/profile/update/{id}', [App\Http\Controllers\ProcessorController::class, 'updateprofile']);
    Route::patch('/profile/update/password/{id}', [App\Http\Controllers\ProcessorController::class, 'updatepasword']);
    Route::post('/profile/processor-changeProfilePic', [App\Http\Controllers\ProcessorController::class, 'changeProfilePic'])->name('processorProfilePic');

    Route::get('/purchase_request', [App\Http\Controllers\ProcessorController::class, 'purchase_request'])->name('pro_purchase_request');
    Route::post('/requisitionsave', [App\Http\Controllers\ProcessorController::class, 'addrequisition']);
    // Route::get('/purchase_request/view/{pr_no}', [App\Http\Controllers\ProcessorController::class, 'view_purchase_request']);
    Route::get('/purchase_request/view/{pr_no}', [App\Http\Controllers\ProcessorController::class, 'view_purchase_request'])->name('pro_vpr');

    Route::get('/pr_for_canvass', [App\Http\Controllers\ProcessorController::class, 'pr_for_canvass'])->name('pr_for_canvass');
    // Route::get('/pr_for_canvass/view/{pr_no}', [App\Http\Controllers\ProcessorController::class, 'view_pr_for_canvass']);
    Route::get('/pr_for_canvass/view/{pr_no}', [App\Http\Controllers\ProcessorController::class, 'view_pr_for_canvass'])->name('pr_canvass');
    // Route::get('/pr_for_canvass/view/generate_canvass/{pr_no}', [App\Http\Controllers\ProcessorController::class, 'generate_canvass']);
    Route::post('/pr_for_canvass/view/send_canvass/{pr_no}', [App\Http\Controllers\ProcessorController::class, 'sendcanvass']);
    Route::get('/pr_for_canvass/view/canvassed/{pr_no}', [App\Http\Controllers\ProcessorController::class, 'view_canvassed'])->name('view_canvassed');
    Route::patch('/pr_for_canvass/view/canvassed/update/{pr_no}', [App\Http\Controllers\ProcessorController::class, 'update_canvassed']);
    Route::delete('/pr_for_canvass/view/canvassed/delete/{id}', [App\Http\Controllers\ProcessorController::class, 'delete_canvassed_item']);
    Route::delete('/pr_for_canvass/view/canvassed/delete_item/{id}', [App\Http\Controllers\ProcessorController::class, 'delete_canvass_item']);

    Route::get('/supplier_items', [App\Http\Controllers\ProcessorController::class, 'supplier_items'])->name('pro_supplier_items');
    Route::post('/suppliersave', [App\Http\Controllers\ProcessorController::class, 'addsupplier']);
    Route::post('/itemsave', [App\Http\Controllers\ProcessorController::class, 'additem']);
    Route::patch('/supplier/update/{id}', [App\Http\Controllers\ProcessorController::class, 'updatesupplier']);
    Route::patch('/item/update/{id}', [App\Http\Controllers\ProcessorController::class, 'updateitem']);
    Route::delete('/supplier/delete/{id}', [App\Http\Controllers\ProcessorController::class, 'deletesupplier']);
    Route::delete('/item/delete/{id}', [App\Http\Controllers\ProcessorController::class, 'deleteitem']);

    Route::post('/supplieritemsave', [App\Http\Controllers\ProcessorController::class, 'addsupplieritem']);
    Route::patch('/supplieritem/update/{id}', [App\Http\Controllers\ProcessorController::class, 'updatesupplieritem']);
    Route::delete('/supplieritem/delete/{id}', [App\Http\Controllers\ProcessorController::class, 'deletesupplieritem']);

    Route::get('/approved_pr', [App\Http\Controllers\ProcessorController::class, 'approved_pr'])->name('approved_pr');
    Route::get('/pr_approved/view/{pr_no}', [App\Http\Controllers\ProcessorController::class, 'view_approved_pr'])->name('view_approved_pr');
    Route::post('/pr_approved/vieww/save_po/{pr_no}', [App\Http\Controllers\ProcessorController::class, 'save_po']);
    Route::get('/pr_approved/view/po_form/{pr_no}', [App\Http\Controllers\ProcessorController::class, 'po_form'])->name('po_form');
    Route::patch('/pr_approved/view/po_form/prepare/{po_no}', [App\Http\Controllers\ProcessorController::class, 'prepare_po']);
    Route::get('/approved_po', [App\Http\Controllers\ProcessorController::class, 'approved_po'])->name('approved_po');
    Route::get('/po_prepared/view/{pr_no}', [App\Http\Controllers\ProcessorController::class, 'view_prepared_po'])->name('view_prepared_po_pro');
    Route::get('/view_verified_po/view/{pr_no}', [App\Http\Controllers\ProcessorController::class, 'view_verified_po'])->name('view_verified_po_pro');
    Route::get('/view_approved_po/view/{pr_no}', [App\Http\Controllers\ProcessorController::class, 'view_approved_po'])->name('view_approving_po_pro');
    Route::get('/po_for_approval/view/{pr_no}', [App\Http\Controllers\ProcessorController::class, 'view_approved_po'])->name('view_approved_po_pro');
    Route::get('/new_pr/view/{pr_no}', [App\Http\Controllers\ProcessorController::class, 'view_new_pr'])->name('view_new_pr_pro');
    Route::get('/pr_for_verification/view/{pr_no}', [App\Http\Controllers\ProcessorController::class, 'view_pr_for_verification'])->name('pr_verify_pro');
    Route::get('/pr_for_verification/view/verified/{pr_no}', [App\Http\Controllers\ProcessorController::class, 'update_pr_for_verification'])->name('update_verified_pro');
    Route::get('/pr_check_fund/view/{pr_no}', [App\Http\Controllers\ProcessorController::class, 'view_pr_check_fund'])->name('view_pr_check_fund_pro');
    Route::get('/pr_check_fund/view/checked/{pr_no}', [App\Http\Controllers\ProcessorController::class, 'view_pr_checked_fund'])->name('view_pr_checked_fund_pro');
    Route::get('/pr_for_approval/view/{pr_no}', [App\Http\Controllers\ProcessorController::class, 'view_pr_for_approval'])->name('pr_approval_pro');
    Route::get('/pr_approved/vieww/{pr_no}', [App\Http\Controllers\ProcessorController::class, 'view_approved_prr'])->name('view_approved_pr_pro');
    Route::get('/pr_to_po', [App\Http\Controllers\ProcessorController::class, 'pr_to_po'])->name('pr_to_po');

    Route::get('/purchase_request/view_track/{pr_no}', [App\Http\Controllers\ProcessorController::class, 'view_track_pr'])->name('pro_pr_track');
    Route::get('/purchase_order', [App\Http\Controllers\ProcessorController::class, 'purchase_order'])->name('pro_purchase_order');
    Route::get('/po_for_approval/view/{pr_no}', [App\Http\Controllers\ProcessorController::class, 'view_approved_po'])->name('view_approved_po_pro');
    Route::get('/purchase_order/view_track/{pr_no}', [App\Http\Controllers\ProcessorController::class, 'view_track_po'])->name('pro_po_track');
    Route::get('/order_po', [App\Http\Controllers\ProcessorController::class, 'order_po'])->name('order_po');
    Route::get('/po_to_order/view/{pr_no}', [App\Http\Controllers\ProcessorController::class, 'view_po_to_order'])->name('view_po_to_order');
    Route::patch('/po_to_order/view/order/{pr_no}', [App\Http\Controllers\ProcessorController::class, 'send_order']);
    Route::get('/ordered_po/view/{pr_no}/{order_no}/{sid}', [App\Http\Controllers\ProcessorController::class, 'view_ordered_po'])->name('view_ordered_po');
    Route::put('/ordered_po/view/{pr_no}/{order_no}/{sid}/save_delivery_no/{po_no}', [App\Http\Controllers\ProcessorController::class, 'save_delivery_no']);
    Route::post('/deliveries/view/{pr_no}/save_delivery_no/{po_no}', [App\Http\Controllers\ProcessorController::class, 'save_delivery_no']);
    Route::get('/deliveries', [App\Http\Controllers\ProcessorController::class, 'deliveries'])->name('deliveries');
    Route::get('/deliveries/view/{pr_no}/{po_no}/{dln}/{order_no}/{sid}', [App\Http\Controllers\ProcessorController::class, 'view_po_deliveries'])->name('view_po_deliveries');
    Route::get('/deliveries/approved/view/{pr_no}/{po_no}/{dln}/{order_no}/{sid}', [App\Http\Controllers\ProcessorController::class, 'view_deliveries_for_approval'])->name('view_deliveries_for_approval');
    Route::patch('/deliveries/view/{pr_no}/{po_no}/{dln}/set_delivery/{supplier_id}', [App\Http\Controllers\ProcessorController::class, 'set_delivery']);

    Route::get('/pro_to_received', [App\Http\Controllers\ProcessorController::class, 'to_received'])->name('pro_to_received');
    Route::get('/to_received/view/{pr_no}/{po_no}', [App\Http\Controllers\ProcessorController::class, 'view_po_to_received'])->name('view_po_to_received_pro');
    Route::post('/deliveries/view/{pr_no}/{po_no}/{dln}/{order_no}/{sid}/forward_delivery', [App\Http\Controllers\ProcessorController::class, 'forward_delivery']);
    Route::post('/ordered_po/view/{pr_no}/{order_no}/{sid}/forward_delivery', [App\Http\Controllers\ProcessorController::class, 'forward_delivery']);
    Route::patch('/deliveries/view/{pr_no}/{po_no}/received_delivery/{dln}', [App\Http\Controllers\ProcessorController::class, 'received_delivery']);
    Route::post('/deliveries/view/{pr_no}/{po_no}/update_delivery/{dln}', [App\Http\Controllers\ProcessorController::class, 'update_delivery']);
    Route::get('/purchase_request/view/denied/{pr_no}', [App\Http\Controllers\ProcessorController::class, 'view_denied_pr'])->name('pro_denied');
    // Route::get('/approved/view/{fwn}/{dln}/{po_no}/{staff_id}', [App\Http\Controllers\ProcessorController::class, 'view_item_received'])->name('view_forwarded_pro');
    Route::get('/approved/view/{dln}/{po_no}/{user_id}', [App\Http\Controllers\ProcessorController::class, 'view_po_to_received'])->name('view_forwarded_pro');
    Route::patch('/approved/view/{dln}/{po_no}/{staff_id}/received_items', [App\Http\Controllers\ProcessorController::class, 'received_item']);
    Route::get('/approved/view/track/{dln}/{po_no}/{user_id}', [App\Http\Controllers\ProcessorController::class, 'track_po_to_received'])->name('track_forwarded_pro');

    Route::post('/approved/view/{dln}/{po_no}/{staff_id}/report_item', [App\Http\Controllers\ProcessorController::class, 'report_item']);
    Route::get('/approved/view/{rn}', [App\Http\Controllers\ProcessorController::class, 'view_reported_item'])->name('view_reported_item_pro');
    Route::get('/reported_items', [App\Http\Controllers\ProcessorController::class, 'reported_items'])->name('reported_items');
    Route::get('/reported_items/view/{rn}', [App\Http\Controllers\ProcessorController::class, 'view_reported_item2'])->name('view_reported_items');
});
