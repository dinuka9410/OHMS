@extends('../layout/main')

@section('head')
@yield('subhead')
@endsection

@section('content')
@include('../layout/menu/mobile-menu')
<style>
    .side-nav.side-nav--simple{
        position: fixed;
        -ms-overflow-style: none;
        scrollbar-width: none;
        overflow-y: scroll;
        max-height: auto;
        height: 100vh;
    
    }
    .side-nav.side-nav--simple::-webkit-scrollbar{
    display: none;
    }
    .top-bar{
        position: fixed;
        width: -webkit-fill-available;
        margin-left: 83px;
        margin-right: 30px;
        top: 0;
        padding: 15px;
        height: 50px;
        background: #222954;
        border-bottom-left-radius: 30px;
        border-bottom-right-radius: 30px;
    }
    .content{
        margin-left: 84.3px;
        margin-top: 17.5px;
    }

@media only screen and (min-width: 320px) and (max-width: 575.98px) {
    .top-bar{
        top: 50px;
        margin-left: 0;
        margin-right: 0;
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
    }
    .content{
        margin-top: 50px;
    }
    
}
@media only screen and (min-width: 575.98px) and (max-width: 767.98px) {
    .top-bar{
        top: 50px;
        margin-left: 0;
        margin-right: 0;
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
    }
    .content{
        margin-top: 50px;
    }
}
@media only screen and (min-width: 767.98px) and (max-width: 991.98px) {
    .top-bar{
        margin-right: 15px;
    }
    .content{
        margin-top: 40px;
    }
}
@media only screen and (min-width: 991.98px) and (max-width: 1199.98px) {
    .top-bar{
        margin-right: 15px;
    }
}
@media only screen and (min-width: 1199.98px) and (max-width: 1279.98px){
    .top-bar{
        margin-right: 15px;
    }
}
@media only screen and (min-width: 1279.98px) and (max-width: 1299.98px){
    
}
@media only screen and (min-width: 1299.98px) and (max-width: 1399.98px) { 
    
}
@media only screen and (min-width: 1399.98px) and (max-width: 1499.98px){
    
}
@media only screen and (min-width: 1399.98px) and (max-width: 1799.98px){
    
}
@media only screen and (min-width: 1799.98px)  {
    
}
    
</style>
<div class="flex">
    <!-- BEGIN: Simple Menu -->
    <nav class="side-nav side-nav--simple">
        <a href="" class="intro-x flex items-center pl-5 pt-4">
            <!-- <img alt="" class="w-6 main-logo" src="{{ asset('dist/images/logo.svg') }}"> -->
        </a>
        <div class="side-nav__devider my-6"></div>
        <ul>
            @php
            $pagesetup = session()->get('pagesetup');
            @endphp
            @foreach ($pagesetup['side_menu'] as $menu)
            @if ($menu == 'devider')
            <li class="side-nav__devider my-6"></li>
            @else
            <li>
                @if(isset($menu['module']) && !is_null($menu['module']) )
                    @if(!isset($menu['sub_menu']) )
                    <a href="{{ url($menu['module'].'/'.$menu['page_name']) }}" class="{{ $pagesetup['first_page_name'] == $menu['page_name'] ? 'side-menu side-menu--active' : 'side-menu' }}">
                    @else
                    <a class="{{ $pagesetup['first_page_name'] == $menu['page_name'] ? 'side-menu side-menu--active' : 'side-menu' }}">
                    @endif
                        <div class="side-menu__icon">
                            <img class="menu-icon-img simple-main-menu-icon" src="{{ asset('dist/images/menu_icons/' . $menu['icon_image'].'.png') }}" width: 25px;>
                        </div>
                        <div class="side-menu__title">
                            {{ $menu['title'] }}
                            @if (isset($menu['sub_menu']))
                            <img class="menu-icon-img simple-main-menu-icon" src="{{ asset('dist/images/menu_icons/' . $menu['icon_image'].'.png') }}" width: 25px;>
                            @endif
                        </div>
                    </a>
                    @if (isset($menu['sub_menu']))
                    <ul class="{{ $pagesetup['first_page_name'] == $menu['page_name'] ? 'side-menu__sub-open' : '' }}">
                        @foreach ($menu['sub_menu'] as $subMenu)
                        @if( isset($subMenu['module']) && !is_null($subMenu['module']))
                        <li>
                            <a href="{{ url( $subMenu['module'].'/'.$subMenu['page_name']) }}" class="{{ $pagesetup['second_page_name'] == $subMenu['page_name'] ? 'side-menu side-menu--active' : 'side-menu' }}">
                                <div class="side-menu__icon">
                                    <img class="menu-icon-img simple-sub-menu-icon" src="{{ asset('dist/images/menu_icons/' . $subMenu['icon_image'].'.png') }}" width: 25px;>
                                </div>
                                <div class="side-menu__title">
                                    {{ $subMenu['title'] }}
                                    @if (isset($subMenu['sub_menu']))
                                    <i data-feather="chevron-down" class="side-menu__sub-icon"></i>
                                    @endif
                                </div>
                            </a>
                            @if (isset($subMenu['sub_menu']))
                            <ul class="{{ $pagesetup['second_page_name'] == $subMenu['page_name'] ? 'side-menu__sub-open' : '' }}">
                                @foreach ($subMenu['sub_menu'] as $lastSubMenu)
                                <li>
                                    <a href="{{ url( $lastSubMenu['module'].'/'.$lastSubMenu['page_name']) }}" class="{{ $pagesetup['third_page_name'] == $lastSubMenu['page_name'] ? 'side-menu side-menu--active' : 'side-menu' }}">
                                        <div class="side-menu__icon">
                                            <i data-feather="zap"></i>
                                        </div>
                                        <div class="side-menu__title">{{ $lastSubMenu['title'] }}</div>
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                            @endif
                        </li>
                        @endif
                        @endforeach
                    </ul>
                    @endif
                @endif
            </li>
            @endif
            @endforeach
        </ul>
    </nav>
    <!-- END: Simple Menu -->
    <!-- BEGIN: Content -->
    @include('../layout/components/top-bar')
    <div class="content">

        @yield('subcontent')
    </div>
    <!-- END: Content -->
</div>
@endsection

