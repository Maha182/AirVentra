<?php

// Controllers
use App\Http\Controllers\dashController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Security\RolePermission;
use App\Http\Controllers\Security\RoleController;
use App\Http\Controllers\Security\PermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskChartController;
use Illuminate\Support\Facades\Artisan;
// Packages
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StorageAssignmentController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\AdminTaskController;
use App\Http\Controllers\PlacementController;
use App\Http\Controllers\LocationCheckController;
use App\Http\Controllers\RackScanController;
use Illuminate\Http\Request;
use App\Models\Location;
use Illuminate\Support\Facades\Http;

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
    
    
    // Inside the 'auth' or 'dashboard' middleware group
    Route::get('/options', function () {
        Http::post('http://127.0.0.1:5002/stop_service', ['service' => 'barcode']);
        Http::post('http://127.0.0.1:5002/stop_service', ['service' => 'assignment']);
    return view('optionspage');  
    })->name('OptionsPage');

    



    Route::prefix('charts')->group(function () {
        Route::get('/task-completion-trend', [TaskChartController::class, 'taskCompletionTrend']);
        Route::get('/task-distribution', [TaskChartController::class, 'taskDistributionByEmployee']);
        Route::get('/task-status', [TaskChartController::class, 'taskStatusBreakdown']);
        Route::get('/live-updates', [TaskChartController::class, 'liveTaskUpdates']);
        Route::get('/task-types-distribution', [TaskChartController::class, 'taskTypesDistribution']);
        Route::get('/task-completion-time', [TaskChartController::class, 'taskCompletionTime']);
        Route::get('/assigned-vs-completed', [TaskChartController::class, 'assignedVsCompleted']);
        Route::get('/workload-3d', [TaskChartController::class, 'workload3D']);
        Route::get('/delayed-vs-on-time', [TaskChartController::class, 'delayedVsOnTime']);
    });
    
    // Route::get('/AddEmployee', function () {
    //     return view('AddEmployee');  
    //     })->name('AddEmployee');

    Route::get('/storage-assignment', function () {
        Http::post('http://127.0.0.1:5002/start_service', ['service' => 'barcode']);
        Http::post('http://127.0.0.1:5002/start_service',['service' => 'assignment']);
        return view('storage-assignment');
    })->name('storage-assignment');
    
  

    Route::get('/mainPage', function (Request $request) {
        // Retrieve the rack data from the session
        $rackData = session('current_rack', null);
    
        if ($rackData) {
            // Start the barcode service
            Http::post('http://127.0.0.1:5002/start_service', ['service' => 'barcode']);
            
            // Stop the assignment service if running
            Http::post('http://127.0.0.1:5002/stop_service', ['service' => 'assignment']);
            
            // Pass the rack data to the main page view
            return view('mainPage', [
                'rackId' => $rackData['rack_id'],
                'location' => $rackData['zone'],
                'locationCapacity' => $rackData['capacity'],
                'status' => 'Active', // Or set it based on your needs
            ]);
        } else {
            return redirect()->route('ScanShelf')->with('error', 'Rack location not found');
        }
    })->name('mainPage');


    Route::get('/lookupLocation', [StorageAssignmentController::class, 'lookupLocation'])->name('lookupLocation');
    Route::post('/assign/manual', [StorageAssignmentController::class, 'assignManual'])->name('assign.manual');

    Route::get('/sendLocationData', [StorageAssignmentController::class, 'sendLocationData'])->name('sendLocationData');
    Route::post('/assign-product', [StorageAssignmentController::class, 'assignProductToLocation'])->name('assignProduct');
    Route::get('/getErrorReports', [PlacementController::class, 'getErrorReports']);    

    Route::get('/getBarcode', [PlacementController::class, 'getBarcode']);
    Route::get('/check-placement', [PlacementController::class, 'checkPlacement'])->name('check-placement');
    //inventory level check
    Route::get('/scan-shelf', function () {
        Http::post('http://127.0.0.1:5002/start_service', ['service' => 'barcode']);
        // Stop assignment service if running
        Http::post('http://127.0.0.1:5002/stop_service', ['service' => 'assignment']);
        return view('ScanShelf');
    })->name('ScanShelf');
    
    Route::get('/scan-rack', [RackScanController::class, 'scanRack'])->name('scan-rack');

    Route::post('/update_inventory', [InventoryController::class, 'updateInventory'])->name('updateInventory');
    Route::post('/reset_scans', [InventoryController::class, 'resetScans'])->name('Reset');

    Route::middleware(['auth', 'isEmployee'])->group(function () {
        Route::get('/tasks', [TaskController::class, 'index'])->name('task');
        Route::get('/tasks/completed', [TaskController::class, 'completedTasks']);
        Route::post('/tasks/{id}/complete', [TaskController::class, 'markAsComplete'])->name('tasks.complete');
        Route::get('/tasks/stats', [TaskController::class, 'getTaskStats'])->name('tasks.stats');
        Route::get('/tasks/details/{taskId}', [TaskController::class, 'details'])->name('tasks.details');
        Route::get('/completed-tasks-trend/{filter}', [TaskController::class, 'getCompletedTasksTrend'])
        ->name('completed-tasks-trend')
        ->where('filter', 'day|week|month');  // Ensures only valid filters are passed
        
        Route::get('/task-breakdown', [TaskController::class, 'getTaskBreakdown']);
    
    });
    

   

    // Routes that require admin access
    Route::middleware('admin')->group(function () {
        // Admin only: charts and resources
        Route::get('/charts', [ProductController::class, 'charts'])->name('product_charts');
        Route::resource('users', UserController::class);
        Route::resource('products', ProductController::class);
    
        // Admin dashboard and task management
        Route::get('/moniter', function () {
                return view('analyseTask');
        })->name('analyseTask');
    
        Route::get('/admin', [AdminTaskController::class, 'index'])->name('admin.dashboard');
        Route::post('/admin/{taskId}/reassign', [AdminTaskController::class, 'reassignTask']);
        Route::post('/admin/{id}/update-deadline', [AdminTaskController::class, 'updateDeadline'])->name('tasks.update-deadline');

        // Route::get('/products/stock-levels', [ProductController::class, 'stockLevels'])
        // ->name('products.stock-levels');
    });


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


