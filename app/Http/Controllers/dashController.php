<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LocationCheck;
use App\Models\User;
use App\Models\Product;
use App\Models\Location;
use Carbon\Carbon;
use App\Models\LocationCapacityCheck;
use Illuminate\Support\Facades\Http;

class dashController extends Controller
{
    public function index(Request $request)
    {
        Http::post('http://127.0.0.1:5002/stop_service', ['service' => 'barcode']);
        Http::post('http://127.0.0.1:5002/stop_service', ['service' => 'assignment']);  
        
        $locationCapacityChecks = LocationCapacityCheck::all();
        $locationChecks = LocationCheck::all();    
        
        $correctCount = LocationCheck::whereRaw('LOWER(status) = ?', ['Corrected'])->count();
        $misplacedCount = LocationCheck::whereRaw('LOWER(status) = ?', ['Pending'])->count();
        
        $overstockCount = LocationCapacityCheck::where('status', 'overfilled')->count();
        $understockCount = LocationCapacityCheck::where('status', 'underfilled')->count();
        
        $misplacedProducts = LocationCheck::selectRaw('MONTHNAME(scan_date) as month, COUNT(*) as total')
            ->groupBy('month')
            ->orderByRaw('MIN(scan_date)')
            ->get();
        
        $months = $misplacedProducts->pluck('month')->toArray();
        $totals = $misplacedProducts->pluck('total')->toArray();
        
        $totalScans = LocationCheck::count();
        $totalRacks = LocationCapacityCheck::count();
        $filledRacks = LocationCapacityCheck::where('status', '!=', 'empty')->count();
        $rackCapacity = $totalRacks > 0 ? round(($filledRacks / $totalRacks) * 100, 2) : 0;
        
        $formattedTotalScans = number_format($totalScans);
        $formattedCorrectPlacements = number_format($correctCount);
        $formattedMisplacedItems = number_format($misplacedCount);
        $totalProduct = Product::count();
     
        $inventoryStats = LocationCapacityCheck::selectRaw("
            SUM(CASE WHEN status = 'overfilled' THEN 1 ELSE 0 END) as overfilled,
            SUM(CASE WHEN status = 'underfilled' THEN 1 ELSE 0 END) as underfilled,
            SUM(CASE WHEN status = 'normal' THEN 1 ELSE 0 END) as normal
        ")->first();
        
        $totalCapacity = Location::sum('capacity');
        $currentCapacity = Location::sum('current_capacity');
        $capacityUtilization = ($totalCapacity > 0) ? round(($currentCapacity / $totalCapacity) * 100, 2) : 0;


        $topProblematicLocations = LocationCheck::selectRaw("wrong_location, COUNT(*) as errors")
            ->groupBy('wrong_location')
            ->orderByDesc('errors')
            ->limit(5)
            ->get();

        $zoneErrorData = LocationCheck::join('locations', 'placement_error_report.wrong_location', '=', 'locations.id')
        ->selectRaw('locations.zone_name, COUNT(*) as errors')
        ->groupBy('locations.zone_name')
        ->orderByDesc('errors')
        ->get();
        $assets = ['chart', 'animation'];
        return view('dashboards.dashboard', compact(
            'assets', 'locationChecks', 'locationCapacityChecks',
            'overstockCount', 'understockCount', 'correctCount', 'misplacedCount', 'months', 'totals',
            'totalScans','zoneErrorData', 'rackCapacity', 'formattedTotalScans', 'formattedCorrectPlacements', 'formattedMisplacedItems',
            'totalProduct', 'inventoryStats','totalCapacity','currentCapacity','capacityUtilization','topProblematicLocations'
        ));
    }
    
    /*
     * Menu Style Routs
     */
    public function horizontal(Request $request)
    {
        $assets = ['chart', 'animation'];
        return view('menu-style.horizontal',compact('assets'));
    }
    public function dualhorizontal(Request $request)
    {
        $assets = ['chart', 'animation'];
        return view('menu-style.dual-horizontal',compact('assets'));
    }
    public function dualcompact(Request $request)
    {
        $assets = ['chart', 'animation'];
        return view('menu-style.dual-compact',compact('assets'));
    }
    public function boxed(Request $request)
    {
        $assets = ['chart', 'animation'];
        return view('menu-style.boxed',compact('assets'));
    }
    public function boxedfancy(Request $request)
    {
        $assets = ['chart', 'animation'];
        return view('menu-style.boxed-fancy',compact('assets'));
    }

    /*
     * Pages Routs
     */
    public function billing(Request $request)
    {
        return view('special-pages.billing');
    }

    public function calender(Request $request)
    {
        $assets = ['calender'];
        return view('special-pages.calender',compact('assets'));
    }

    public function kanban(Request $request)
    {
        return view('special-pages.kanban');
    }

    public function pricing(Request $request)
    {
        return view('special-pages.pricing');
    }

    public function rtlsupport(Request $request)
    {
        $assets = ['chart', 'animation'];
        return view('special-pages.rtl-support',compact('assets'));
    }

    public function timeline(Request $request)
    {
        return view('special-pages.timeline');
    }


    /*
     * Widget Routs
     */
    public function widgetbasic(Request $request)
    {
        return view('widget.widget-basic');
    }
    public function widgetchart(Request $request)
    {
        $assets = ['chart'];
        return view('widget.widget-chart', compact('assets'));
    }
    public function widgetcard(Request $request)
    {
        return view('widget.widget-card');
    }

    /*
     * Maps Routs
     */
    public function google(Request $request)
    {
        return view('maps.google');
    }
    public function vector(Request $request)
    {
        return view('maps.vector');
    }

    /*
     * Auth Routs
     */
    public function signin(Request $request)
    {
        return view('auth.login');
    }
    public function signup(Request $request)
    {
        return view('auth.register');
    }
    public function confirmmail(Request $request)
    {
        return view('auth.confirm-mail');
    }
    public function lockscreen(Request $request)
    {
        return view('auth.lockscreen');
    }
    public function recoverpw(Request $request)
    {
        return view('auth.recoverpw');
    }
    public function userprivacysetting(Request $request)
    {
        return view('auth.user-privacy-setting');
    }

    /*
     * Error Page Routs
     */

    public function error404(Request $request)
    {
        return view('errors.error404');
    }

    public function error500(Request $request)
    {
        return view('errors.error500');
    }
    public function maintenance(Request $request)
    {
        return view('errors.maintenance');
    }

    /*
     * uisheet Page Routs
     */
    public function uisheet(Request $request)
    {
        return view('uisheet');
    }

    /*
     * Form Page Routs
     */
    public function element(Request $request)
    {
        return view('forms.element');
    }

    public function wizard(Request $request)
    {
        return view('forms.wizard');
    }

    public function validation(Request $request)
    {
        return view('forms.validation');
    }

     /*
     * Table Page Routs
     */
    public function bootstraptable(Request $request)
    {
        return view('table.bootstraptable');
    }

    public function datatable(Request $request)
    {
        return view('table.datatable');
    }

    /*
     * Icons Page Routs
     */

    public function solid(Request $request)
    {
        return view('icons.solid');
    }

    public function outline(Request $request)
    {
        return view('icons.outline');
    }

    public function dualtone(Request $request)
    {
        return view('icons.dualtone');
    }

    public function colored(Request $request)
    {
        return view('icons.colored');
    }

    /*
     * Extra Page Routs
     */
    public function privacypolicy(Request $request)
    {
        return view('privacy-policy');
    }
    public function termsofuse(Request $request)
    {
        return view('terms-of-use');
    }


    /*
    * Landing Page Routs
    */
    public function landing_index(Request $request)
    {
        return view('landing-pages.pages.index');
    }
    public function landing_blog(Request $request)
    {
        return view('landing-pages.pages.blog');
    }
    public function landing_about(Request $request)
    {
        return view('landing-pages.pages.about');
    }
    public function landing_blog_detail(Request $request)
    {
        return view('landing-pages.pages.blog-detail');
    }
    public function landing_contact(Request $request)
    {
        return view('landing-pages.pages.contact-us');
    }
    public function landing_ecommerce(Request $request)
    {
        return view('landing-pages.pages.ecommerce-landing-page');
    }
    public function landing_faq(Request $request)
    {
        return view('landing-pages.pages.faq');
    }
    public function landing_feature(Request $request)
    {
        return view('landing-pages.pages.feature');
    }
    public function landing_pricing(Request $request)
    {
        return view('landing-pages.pages.pricing');
    }
    public function landing_saas(Request $request)
    {
        return view('landing-pages.pages.saas-marketing-landing-page');
    }
    public function landing_shop(Request $request)
    {
        return view('landing-pages.pages.shop');
    }
    public function landing_shop_detail(Request $request)
    {
        return view('landing-pages.pages.shop_detail');
    }
    public function landing_software(Request $request)
    {
        return view('landing-pages.pages.software-landing-page');
    }
    public function landing_startup(Request $request)
    {
        return view('landing-pages.pages.startup-landing-page');
    }
}
