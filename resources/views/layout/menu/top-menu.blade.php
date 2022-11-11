




@extends('../layout/main')

@section('head')
@yield('subhead')
@endsection

@section('content')
@include('../layout/menu/mobile-menu')

<style>
    .top-bar-sticky {
    position: fixed;
    top:10px;
    width: 100%;
    z-index: 999;
    bottom: 300px;
    background-color: #222954;
    height: 75px;
    
  }
  .top-nav-sticky {
    padding-top: 5px;
    position: fixed;
    top: 65px;
    width: 100%;
    z-index: 999;
    bottom: 10px;
    height: 55px;
    background-color: #222954;
    padding-bottom: 5px;
}
.content{
    margin-top: 112px;
}
/*==== fix header responsive style ====*/
@media (max-width: 320px) {
    
    .top-bar-sticky{
        top:3px;
    }
    .mobile-menu{
        padding-top: 5px;
        position: fixed;
        top: 45px;
        width: 100%;
        z-index: 999;
        bottom: 10px;
        height: 67px;
        background-color: #222954;
        padding-bottom: 5px;
    }
    .mobile-menu .border-t{
        background-color: #222954;
    }
}
@media (max-width: 575.98px) {
    .top-bar-sticky{
        top:3px;
    }
    .mobile-menu{
        padding-top: 5px;
        position: fixed;
        top: 45px;
        width: 100%;
        z-index: 999;
        bottom: 10px;
        height: 67px;
        background-color: #222954;
        padding-bottom: 5px;
    }
    .mobile-menu .border-t{
        background-color: #222954;
    }
}
@media (max-width: 767.98px) {
    .top-bar-sticky{
        top:3px;
    }
    .mobile-menu{
        padding-top: 5px;
        position: fixed;
        top: 45px;
        width: 100%;
        z-index: 999;
        bottom: 10px;
        height: 67px;
        background-color: #222954;
        padding-bottom: 5px;
    }
    .mobile-menu .border-t{
        background-color: #222954;
    }
}
@media (max-width: 991.98px) {}
@media (max-width: 1199.98px) {}
@media (max-width: 1799.98px) {}
@media (max-width: 2560.98px) {}
/*==== fix header responsive style End====*/
</style>

<!-- BEGIN: Top Bar -->
<div class="top-bar-sticky border-b border-theme-24 -mt-10 md:-mt-5 -mx-3 sm:-mx-8 px-3 sm:px-8 pt-3 md:pt-0 mb-5" id="topnav">
    <div class="top-bar-boxed flex items-center">
        <!-- BEGIN: Logo -->
        <a href="{{route('dashboard')}}" class="intro-x flex items-center pt-2">
            <!-- <img alt="" class="main-logo" src="{{ asset('dist/images/logo_removebg.png') }}" style="border-radius: 5px;"> -->
            <!-- <span class="hidden xl:block text-white text-lg ml-3">
                    Mid<span class="font-medium">one</span>
                </span> -->
        </a>
        <!-- END: Logo -->

        <div class="dashbord-top-bar">
         @include('../layout/components/top-bar')
        </div>
       

    </div>
</div>
<!-- END: Top Bar -->
<!-- BEGIN: Top Menu -->
<nav class="top-nav top-nav-sticky" >
    <ul>
    @php
            $pagesetup = session()->get('pagesetup');
    @endphp

        @foreach ($pagesetup['top_menu'] as $menu)
        <li>
        @if(isset($menu['module']) && !is_null($menu['module']) )
            <a @if(isset($menu['sub_menu'])) @else href="{{ url($menu['module'].'/'.$menu['page_name']) }}" @endif class="{{ $pagesetup['first_page_name'] == $menu['page_name'] ? 'top-menu top-menu--active' : 'top-menu main-menu-item' }}">
                <div class="top-menu__icons">
                    <!-- <i data-feather="{{ $menu['icon'] }}"></i> -->
                    <img class="menu-icon-img" src="{{ asset('dist/images/menu_icons/' . $menu['icon_image'].'.png') }}" width: 25px;>
                </div>
                <div class="top-menu__title">
                    {{ $menu['title'] }}
                    @if (isset($menu['sub_menu']))
                    <i data-feather="chevron-down" class="top-menu__sub-icon"></i>
                    @endif
                </div>
            </a>
            @if (isset($menu['sub_menu']))
            <ul class="{{ $pagesetup['first_page_name'] == $menu['page_name'] ? 'side-menu__sub-open' : '' }}">
                @foreach ($menu['sub_menu'] as $subMenu)
                @if( isset($subMenu['module']) && !is_null($subMenu['module']) )
                <li>
                    <a href="{{ url( $subMenu['module'].'/'.$subMenu['page_name']) }}" class="top-menu">
                        <div class="top-menu__icon">
                            <!-- <i data-feather="activity"></i> -->
                            <img class="menu-icon-img" src="{{ asset('dist/images/menu_icons/' . $subMenu['icon_image'].'.png') }}">
                        </div>
                        <div class="top-menu__title">
                            {{ $subMenu['title'] }}
                            @if (isset($subMenu['sub_menu']))
                            <i data-feather="chevron-down" class="top-menu__sub-icon"></i>
                            @endif
                        </div>
                    </a>
                    @if (isset($subMenu['sub_menu']))
                    <ul class="{{ $pagesetup['second_page_name'] == $subMenu['page_name'] ? 'top-menu__sub-open' : '' }}">
                        @foreach ($subMenu['sub_menu'] as $lastSubMenu)
                        <li>
                            <a href="{{ url( $lastSubMenu['module'].'/'.$lastSubMenu['page_name'] ) }}" class="{{ $pagesetup['third_page_name'] == $lastSubMenu['page_name'] ? 'top-menu top-menu--active' : 'top-menu' }}">
                                <div class="top-menu__icon">
                                    <i data-feather="zap"></i>
                                </div>
                                <div class="top-menu__title">{{ $lastSubMenu['title'] }}</div>
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
        @endforeach

    </ul>
</nav>
<!-- END: Top Menu -->
<!-- BEGIN: Content -->
<div class="content">
   
   
    @yield('subcontent')
</div>
<!-- END: Content -->
@endsection





{{-- this will add the currencies from the DB to the currency drop down in the top   --}}
<script src="{{asset('dist/js/jquery-3.6.0.js')}}"></script>
<script src="{{asset('dist/js/currency_rates.js')}}"></script>



