<?php

// Controllers
use App\Http\Controllers\dashController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Security\RolePermission;
use App\Http\Controllers\Security\RoleController;
use App\Http\Controllers\Security\PermissionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Artisan;
// Packages
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StorageAssignmentController;
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

require __DIR__.'/auth.php';

Route::get('/storage', function () {
    Artisan::call('storage:link');
});





//Landing-Pages Routes
Route::group(['prefix' => 'landing-pages'], function() {
Route::get('index',[dashController::class, 'landing_index'])->name('landing-pages.index');
Route::get('blog',[dashController::class, 'landing_blog'])->name('landing-pages.blog');
Route::get('blog-detail',[dashController::class, 'landing_blog_detail'])->name('landing-pages.blog-detail');
Route::get('about',[dashController::class, 'landing_about'])->name('landing-pages.about');
Route::get('contact',[dashController::class, 'landing_contact'])->name('landing-pages.contact');
Route::get('ecommerce',[dashController::class, 'landing_ecommerce'])->name('landing-pages.ecommerce');
Route::get('faq',[dashController::class, 'landing_faq'])->name('landing-pages.faq');
Route::get('feature',[dashController::class, 'landing_feature'])->name('landing-pages.feature');
Route::get('pricing',[dashController::class, 'landing_pricing'])->name('landing-pages.pricing');
Route::get('saas',[dashController::class, 'landing_saas'])->name('landing-pages.saas');
Route::get('shop',[dashController::class, 'landing_shop'])->name('landing-pages.shop');
Route::get('shop-detail',[dashController::class, 'landing_shop_detail'])->name('landing-pages.shop-detail');
Route::get('software',[dashController::class, 'landing_software'])->name('landing-pages.software');
Route::get('startup',[dashController::class, 'landing_startup'])->name('landing-pages.startup');
});

//UI Pages Routs
Route::get('/', [HomeController::class, 'home'])->name('home');
//Route::get('/', [dashController::class, 'uisheet'])->name('uisheet');

Route::group(['middleware' => 'auth'], function () {
    // Permission Module
    Route::get('/role-permission',[RolePermission::class, 'index'])->name('role.permission.list');
    Route::resource('permission',PermissionController::class);
    Route::resource('role', RoleController::class);

    // Dashboard Routes
    Route::get('/dashboard', [dashController::class, 'index'])->name('dashboard');

    // Users Module
    Route::resource('users', UserController::class);

    // Inside the 'auth' or 'dashboard' middleware group
    Route::get('/options', function () {
    return view('optionspage');  
    })->name('OptionsPage');

    // Route::get('/AddEmployee', function () {
    //     return view('AddEmployee');  
    //     })->name('AddEmployee');

    Route::get('/storage-assignment', function () {
         return view('storage-assignment');  
         })->name('storage-assignment');
  

    Route::get('/mainPage', function () {
        return view('mainPage');  
        })->name('mainPage');


    Route::get('/lookup/location', [StorageAssignmentController::class, 'lookupLocation'])->name('lookup.location');
    Route::post('/assign/manual', [StorageAssignmentController::class, 'assignManual'])->name('assign.manual');
    // Route::post('/assign-storage', [StorageAssignmentController::class, 'storage'])->name('assign.storage');
    Route::post('/assign-storage', [StorageAssignmentController::class, 'assignStorage'])->name('assign.storage');

         
});

//App Details Page => 'Dashboard'], function() {
Route::group(['prefix' => 'menu-style'], function() {
    //MenuStyle Page Routs
    Route::get('horizontal', [dashController::class, 'horizontal'])->name('menu-style.horizontal');
    Route::get('dual-horizontal', [dashController::class, 'dualhorizontal'])->name('menu-style.dualhorizontal');
    Route::get('dual-compact', [dashController::class, 'dualcompact'])->name('menu-style.dualcompact');
    Route::get('boxed', [dashController::class, 'boxed'])->name('menu-style.boxed');
    Route::get('boxed-fancy', [dashController::class, 'boxedfancy'])->name('menu-style.boxedfancy');
});

//App Details Page => 'special-pages'], function() {
Route::group(['prefix' => 'special-pages'], function() {
    //Example Page Routs
    Route::get('billing', [dashController::class, 'billing'])->name('special-pages.billing');
    Route::get('calender', [dashController::class, 'calender'])->name('special-pages.calender');
    Route::get('kanban', [dashController::class, 'kanban'])->name('special-pages.kanban');
    Route::get('pricing', [dashController::class, 'pricing'])->name('special-pages.pricing');
    Route::get('rtl-support', [dashController::class, 'rtlsupport'])->name('special-pages.rtlsupport');
    Route::get('timeline', [dashController::class, 'timeline'])->name('special-pages.timeline');
});

//Widget Routs
Route::group(['prefix' => 'widget'], function() {
    Route::get('widget-basic', [dashController::class, 'widgetbasic'])->name('widget.widgetbasic');
    Route::get('widget-chart', [dashController::class, 'widgetchart'])->name('widget.widgetchart');
    Route::get('widget-card', [dashController::class, 'widgetcard'])->name('widget.widgetcard');
});

//Maps Routs
Route::group(['prefix' => 'maps'], function() {
    Route::get('google', [dashController::class, 'google'])->name('maps.google');
    Route::get('vector', [dashController::class, 'vector'])->name('maps.vector');
});

//Auth pages Routs
Route::group(['prefix' => 'auth'], function() {
    Route::get('signin', [dashController::class, 'signin'])->name('auth.signin');
    Route::get('signup', [dashController::class, 'signup'])->name('auth.signup');
    Route::get('confirmmail', [dashController::class, 'confirmmail'])->name('auth.confirmmail');
    Route::get('lockscreen', [dashController::class, 'lockscreen'])->name('auth.lockscreen');
    Route::get('recoverpw', [dashController::class, 'recoverpw'])->name('auth.recoverpw');
    Route::get('userprivacysetting', [dashController::class, 'userprivacysetting'])->name('auth.userprivacysetting');
});

//Error Page Route
Route::group(['prefix' => 'errors'], function() {
    Route::get('error404', [dashController::class, 'error404'])->name('errors.error404');
    Route::get('error500', [dashController::class, 'error500'])->name('errors.error500');
    Route::get('maintenance', [dashController::class, 'maintenance'])->name('errors.maintenance');
});


//Forms Pages Routs
Route::group(['prefix' => 'forms'], function() {
    Route::get('element', [dashController::class, 'element'])->name('forms.element');
    Route::get('wizard', [dashController::class, 'wizard'])->name('forms.wizard');
    Route::get('validation', [dashController::class, 'validation'])->name('forms.validation');
});


//Table Page Routs
Route::group(['prefix' => 'table'], function() {
    Route::get('bootstraptable', [dashController::class, 'bootstraptable'])->name('table.bootstraptable');
    Route::get('datatable', [dashController::class, 'datatable'])->name('table.datatable');
});

//Icons Page Routs
Route::group(['prefix' => 'icons'], function() {
    Route::get('solid', [dashController::class, 'solid'])->name('icons.solid');
    Route::get('outline', [dashController::class, 'outline'])->name('icons.outline');
    Route::get('dualtone', [dashController::class, 'dualtone'])->name('icons.dualtone');
    Route::get('colored', [dashController::class, 'colored'])->name('icons.colored');
});
//Extra Page Routs
Route::get('privacy-policy', [dashController::class, 'privacypolicy'])->name('pages.privacy-policy');
Route::get('terms-of-use', [dashController::class, 'termsofuse'])->name('pages.term-of-use');



//home page set up

