<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Modules\PrimaryModule\Models\UserGroupPermission;
use Nwidart\Modules\Facades\Module;

class PageSetup
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // get the current logged in user

        if (!session()->has('layout') && !session()->has('theme')) {

            $user = Auth::user();
            $request->session()->put('layout', $user->layout);
            $request->session()->put('theme', $user->theme);
        }

        $pagename = Route::currentRouteName();

        $request->session()->put('pageName', $pagename);


        $activeMenu = $this->activeMenu($request->session()->get('layout'), $request->session()->get('pageName'));

        $page_data = [
            'top_menu' => $this->topMenu(),
            'side_menu' => $this->sideMenu(),
            'simple_menu' => $this->simpleMenu(),
            'first_page_name' => $activeMenu['first_page_name'],
            'second_page_name' => $activeMenu['second_page_name'],
            'third_page_name' => $activeMenu['third_page_name'],
        ];
        $request->session()->put('pagesetup', $page_data);
        $request['pagesetup'] = $page_data;

        // dump($page_data);

        return $next($request);
    }

    /*
        Menu List
        * @return array
    */
    protected function menuList()
    {
        /* create permission array of user */
        $user = Auth::user();
        $permission = Cache::remember('permission', 30, function () use ($user) {
            return UserGroupPermission::with('userpermission')->where([
                'user_group_id' => $user->user_group_id
            ])->get();
        });

        $menuList = [
            'dashboard' => [
                'icon' => 'trending-up',
                'icon_image' => 'dashboard',
                'layout' => 'top-menu',
                'page_name' => 'dashboard',
                'title' => 'Dashboard',
                'module' => 'primarymodule',
            ]
        ];

        /* 1st step: check main module */
        if (Module::has('PrimaryModule')) {

            /* Configaration */
            /* 2nd Step: check main category related with user_permissions_category*/

            /* Hotel Config */
            /* 2nd Step: check main category related with user_permissions_category*/
            if ($permission->where('userpermission.category_id', 3)->count() > 0) { // Hotel configaration

                /* 3rd step: check menu item related with user_permissions table */
                $sub_menu = [];
                if ($permission->where('permission_code', 3)->count() > 0) {
                    $sub_menu['side-menu'] = [
                        'icon' => 'sun',
                        'icon_image' => 'Seasons',
                        'layout' => 'side-menu',
                        'page_name' => 'add_update_season_view',
                        'title' => 'Seasons',
                        'module' => 'primarymodule',
                    ];
                }
                if ($permission->where('permission_code', 4)->count() > 0) {
                    $sub_menu['Agents'] = [
                        'icon' => 'user-plus',
                        'icon_image' => 'Agents',
                        'layout' => 'side-menu',
                        'page_name' => 'agents_view',
                        'title' => 'Agents',
                        'module' => 'primarymodule',
                    ];
                }
                if ($permission->where('permission_code', 7)->count() > 0) {
                    $sub_menu['MealPlan'] = [
                        'icon' => 'coffee',
                        'icon_image' => 'MealPlan',
                        'layout' => 'side-menu',
                        'page_name' => 'add_update_meal_view',
                        'title' => 'MealPlan',
                        'module' => 'primarymodule',
                    ];
                }
                if ($permission->where('permission_code', 8)->count() > 0) {
                    $sub_menu['Agent_room_rate'] = [
                        'icon' => 'user-x',
                        'icon_image' => 'AgentRoomRates',
                        'layout' => 'side-menu',
                        'page_name' => 'agent_room_rate_view',
                        'title' => 'Agent Room Rate',
                        'module' => 'primarymodule',
                    ];
                }
                $menuList['Hotel Config'] = [
                    'icon' => 'home',
                    'icon_image' => 'Hotel-Config',
                    'page_name' => 'season_view',
                    'title' => 'Hotel Config',
                    'module' => 'primarymodule',
                    'sub_menu' => $sub_menu
                ];
            }

            /* Room */
            if ($permission->where('userpermission.category_id', 4)->count() > 0) { //Room

                /* 3rd step: check menu item related with user_permissions table */
                $sub_menu = [];
                if ($permission->where('permission_code', 12)->count() > 0) {
                    $sub_menu['Rooms Category'] = [
                        'icon' => 'book',
                        'icon_image' => 'RoomCategory',
                        'layout' => 'side-menu',
                        'page_name' => 'room_category_view_add_update',
                        'title' => 'Category',
                        'module' => 'primarymodule',
                    ];
                }
                if ($permission->where('permission_code', 13)->count() > 0) {
                    $sub_menu['side-menu'] = [
                        'icon' => 'users',
                        'icon_image' => 'RoomType',
                        'layout' => 'side-menu',
                        'page_name' => 'room_type_add_edit',
                        'title' => 'Room Type',
                        'module' => 'primarymodule',
                    ];
                }
                if ($permission->where('permission_code', 14)->count() > 0) {
                    $sub_menu['Rooms'] = [
                        'icon' => 'box',
                        'icon_image' => 'Rooms',
                        'layout' => 'side-menu',
                        'page_name' => 'room_view',
                        'title' => 'Rooms',
                        'module' => 'primarymodule',

                    ];
                }
                if ($permission->where('permission_code', 16)->count() > 0) {
                    $sub_menu['Room Facilites'] = [
                        'icon' => 'sliders',
                        'icon_image' => 'RoomFacilities',
                        'layout' => 'side-menu',
                        'page_name' => 'room_facilities_view',
                        'title' => 'Facilities',
                        'module' => 'primarymodule',
                    ];
                }
                $menuList['Room'] =  [
                    'icon' => 'log-in',
                    'icon_image' => 'room-management',
                    'page_name' => 'room_type_view',
                    'title' => 'Room',
                    'module' => 'primarymodule',
                    'sub_menu' => $sub_menu
                ];
            }
        }




        return $menuList;
    }

    /**
     * Determine active menu & submenu.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function activeMenu($layout, $pageName)
    {
        $firstPageName = '';
        $secondPageName = '';
        $thirdPageName = '';

        if ($layout == 'top-menu') {
            foreach ($this->topMenu() as $menu) {
                if ($menu['page_name'] == $pageName && empty($firstPageName)) {
                    $firstPageName = $menu['page_name'];
                }

                if (isset($menu['sub_menu'])) {
                    foreach ($menu['sub_menu'] as $subMenu) {
                        if ($subMenu['page_name'] == $pageName && empty($secondPageName) && $subMenu['page_name'] != 'dashboard') {
                            $firstPageName = $menu['page_name'];
                            $secondPageName = $subMenu['page_name'];
                        }

                        if (isset($subMenu['sub_menu'])) {
                            foreach ($subMenu['sub_menu'] as $lastSubmenu) {
                                if ($lastSubmenu['page_name'] == $pageName) {
                                    $firstPageName = $menu['page_name'];
                                    $secondPageName = $subMenu['page_name'];
                                    $thirdPageName = $lastSubmenu['page_name'];
                                }
                            }
                        }
                    }
                }
            }
        } else if ($layout == 'simple-menu') {
            foreach ($this->simpleMenu() as $menu) {
                if ($menu !== 'devider' && $menu['page_name'] == $pageName && empty($firstPageName)) {
                    $firstPageName = $menu['page_name'];
                }

                if (isset($menu['sub_menu'])) {
                    foreach ($menu['sub_menu'] as $subMenu) {
                        if ($subMenu['page_name'] == $pageName && empty($secondPageName) && $subMenu['page_name'] != 'dashboard') {
                            $firstPageName = $menu['page_name'];
                            $secondPageName = $subMenu['page_name'];
                        }

                        if (isset($subMenu['sub_menu'])) {
                            foreach ($subMenu['sub_menu'] as $lastSubmenu) {
                                if ($lastSubmenu['page_name'] == $pageName) {
                                    $firstPageName = $menu['page_name'];
                                    $secondPageName = $subMenu['page_name'];
                                    $thirdPageName = $lastSubmenu['page_name'];
                                }
                            }
                        }
                    }
                }
            }
        } else {
            foreach ($this->sideMenu() as $menu) {
                if ($menu !== 'devider' && $menu['page_name'] == $pageName && empty($firstPageName)) {
                    $firstPageName = $menu['page_name'];
                }

                if (isset($menu['sub_menu'])) {

                    foreach ($menu['sub_menu'] as $subMenu) {
                        if ($subMenu['page_name'] == $pageName && empty($secondPageName) && $subMenu['page_name'] != 'dashboard') {
                            $firstPageName = $menu['page_name'];
                            $secondPageName = $subMenu['page_name'];
                        }


                        if (isset($subMenu['sub_menu'])) {
                            foreach ($subMenu['sub_menu'] as $lastSubmenu) {
                                if ($lastSubmenu['page_name'] == $pageName) {
                                    $firstPageName = $menu['page_name'];
                                    $secondPageName = $subMenu['page_name'];
                                    $thirdPageName = $lastSubmenu['page_name'];
                                }
                            }
                        }
                    }
                }
            }
        }

        return [
            'first_page_name' => $firstPageName,
            'second_page_name' => $secondPageName,
            'third_page_name' => $thirdPageName
        ];
    }

    /**
     * List of side menu items.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sideMenu()
    {
        return  $this->menuList();
    }

    /**
     * List of simple menu items.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function simpleMenu()
    {
        return $this->menuList();
    }

    /**
     * List of top menu items.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function topMenu()
    {
        return  $this->menuList();
    }
}
