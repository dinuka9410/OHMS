<!-- BEGIN: Mobile Menu -->
<div class="mobile-menu md:hidden">
    <div class="mobile-menu-bar">
        <!-- <a href="" class="flex mr-auto">
            <img alt="" class="main-logo" src="{{ asset('dist/images/logo_removebg.png') }}">
        </a> -->
        <a href="javascript:;" id="mobile-menu-toggler">
            <i data-feather="bar-chart-2" class="w-8 h-8 text-white transform -rotate-90"></i>
        </a>
    </div>
    <ul class="border-t border-theme-24 py-5 hidden">
    @php
        $pagesetup = session()->get('pagesetup');
    @endphp

        @foreach ($pagesetup['side_menu'] as $menu)
            @if ($menu == 'devider')
                <li class="menu__devider my-6"></li>
            @else
                <li>
                    <a href="{{ url($menu['module'].'/'.$menu['page_name']) }}" class="{{ $pagesetup['first_page_name'] == $menu['page_name'] ? 'menu menu--active' : 'menu' }}">
                        <div class="menu__icon">
                            <i data-feather="{{ $menu['icon'] }}"></i>
                        </div>
                        <div class="menu__title">
                            {{ $menu['title'] }}
                            @if (isset($menu['sub_menu']) )
                                <i data-feather="chevron-down" class="menu__sub-icon"></i>
                            @endif
                        </div>
                    </a>
                    @if (isset($menu['sub_menu']))
                        <ul class="{{ $pagesetup['first_page_name'] == $menu['page_name'] ? 'menu__sub-open' : '' }}">
                            @foreach ($menu['sub_menu'] as $subMenu)
                                <li>
                                    <a href="{{ url( $subMenu['module'].'/'.$subMenu['page_name']) }}" class="{{ $pagesetup['second_page_name'] == $subMenu['page_name'] ? 'menu menu--active' : 'menu' }}">
                                        <div class="menu__icon">
                                            <i data-feather="activity"></i>
                                        </div>
                                        <div class="menu__title">
                                            {{ $subMenu['title'] }}
                                            @if (isset($subMenu['sub_menu']))
                                                <i data-feather="chevron-down" class="menu__sub-icon"></i>
                                            @endif
                                        </div>
                                    </a>
                                    @if (isset($subMenu['sub_menu']))
                                        <ul class="{{ $pagesetup['second_page_name'] == $subMenu['page_name'] ? 'menu__sub-open' : '' }}">
                                            @foreach ($subMenu['sub_menu'] as $lastSubMenu)
                                                <li>
                                                    <a href="{{ url($lastSubMenu['page_name']) }}" class="{{ $pagesetup['third_page_name'] == $lastSubMenu['page_name'] ? 'menu menu--active' : 'menu' }}">
                                                        <div class="menu__icon">
                                                            <i data-feather="zap"></i>
                                                        </div>
                                                        <div class="menu__title">{{ $lastSubMenu['title'] }}</div>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endif
        @endforeach
    </ul>
</div>
<!-- END: Mobile Menu -->
