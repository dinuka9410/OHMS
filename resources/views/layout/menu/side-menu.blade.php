@extends('../layout/main')

@section('head')
@yield('subhead')
@endsection

@section('content')
@include('../layout/menu/mobile-menu')

<style>
    .main-logo {
        border-radius: 5px;
        max-width: 170px;
        margin-left: auto;
        margin-right: auto;
    }

 @media only screen and (min-width: 320px) and (max-width: 575.98px) {
    
    .top-bar{
        position: fixed;
        top: 52px;
        width: 100%;
        margin-left: 0;
        z-index: 999;
        background-color: #222954;
        left: 0;
        right:0;
        padding-left:  15px;
        padding-right: 15px ;
        height: 45px;
    }
    .content{
        margin-top: 90px;
        padding-left: 0;
        padding-right: 0;
    }
    .intro-y{
        margin-top: 30px;
        padding: 5px;
    }
    
}
@media only screen and (min-width: 575.98px) and (max-width: 767.98px) {
    .top-bar{
        position: fixed;
        top: 52px;
        width: 100% ;
        z-index: 999;
        margin-left: 0;
        background-color: #222954;
        left: 0;
        right:0;
        padding-left: 15px;
        padding-right: 15px;
        height: 45px;
    }
    .content{
        margin-top: 90px;
        padding-left: 0;
        padding-right: 0;
    }
}
@media only screen and (min-width: 767.98px) and (max-width: 991.98px) {
    .side-nav{
        width: 13% !important;
        position: fixed;
        background: #222954;
        height: 100vh;
    }
    .top-bar{
        position: fixed;
        top: 0;
        padding-top: 15px;
        padding-bottom: 15px;
        width: 85%;
        margin-left: 14.3%;
        z-index: 999;
        background-color: #222954;
        left: 0;
        right:0;
        padding-left: 5px;
        padding-right: 5px;
        height: 65px;
        border-bottom-left-radius: 32px;
        border-bottom-right-radius: 32px;
    }
    .side-nav .intro-x.flex.items-center.pt-4{
        padding-top: 10px;
    }
    .side-nav .intro-x.flex.items-center.pt-4 img{
        max-width: 85px !important;
    }
    .breadcrumb {
        padding: 2px 9px;
    }
    .content{
        margin-top: 60px;
        padding-left: 0;
        padding-right: 0;
        padding: 15px;
        margin-left: 12.8% !important;
    }
    .intro-y.flex.items-center.h-10{
        padding: 15px;
    }
    .grid.grid-cols-12.gap-6 .grid.grid-cols-12.gap-6.mt-5{
      padding: 15px;
    }
}
@media only screen and (min-width: 991.98px) and (max-width: 1199.98px) {
    .top-bar{
        position: fixed;
        top: 0;
        padding-top: 15px;
        padding-bottom: 15px;
        width: 85%;
        margin-left: 14%;
        z-index: 999;
        background-color: #222954;
        left: 0;
        right:0;
        padding-left: 5px;
        padding-right: 5px;
        height: 65px;
        border-bottom-left-radius: 32px;
        border-bottom-right-radius: 32px;
    }
    .side-nav{
        width: 13% !important;
        position: fixed;
        background: #222954;
        height: 100vh;
    }
    .grid.grid-cols-12.gap-6 .grid.grid-cols-12.gap-6.mt-5{
        padding: 15px;
      }
    .breadcrumb {
        padding: 2px 9px;
    }
    .main-logo {
        max-width: 115px !important;
    }
    .content{
        padding: 15px;
        margin-top: 60px;
        padding-left: 15px;
        padding-right: 15px;
        margin-left: 13.1% !important;
    }
    .intro-y.flex.items-center.h-10{
        padding: 15px;
    }
    .side-nav .intro-x.flex.items-center.pt-4{
        padding-top: 6px;
    }
}
@media only screen and (min-width: 1199.98px) and (max-width: 1279.98px){
    .side-nav{
        width: 11% !important;
        position: fixed;
        background: #222954;
        height: 100vh;
    }
    .top-bar{
        position: fixed;
        top: 0;
        padding-top: 15px;
        padding-bottom: 15px;
        width: 87%;
        margin-left: 12%;
        z-index: 999;
        background-color: #222954;
        left: 0;
        right:0;
        padding-left: 5px;
        padding-right: 5px;
        height: 65px;
        border-bottom-left-radius: 32px;
        border-bottom-right-radius: 32px;
    }
    .content{
        margin-top: 60px;
        padding-left: 0;
        padding-right: 0;
        margin-left: 11.3% !important;
        padding: 15px;
    }
    .intro-y.flex.items-center.h-10{
        padding: 15px;
    }
    .grid.grid-cols-12.gap-6 .grid.grid-cols-12.gap-6.mt-5{
      padding: 15px;
    }
    .side-nav .side-nav__devider{
        margin-top: 60px;
    }
}
@media only screen and (min-width: 1279.98px) and (max-width: 1299.98px){
    .side-nav{
        width: 14%;
        position: fixed;
        background: #222954;
        height: 100vh;
    }
    .top-bar{
        position: fixed;
        top: -2px;
        padding-top: 15px;
        padding-bottom: 15px;
        width: 83%;
        margin-left: 15.7%;
        z-index: 999;
        background-color: #222954;
        left: 0;
        right:0;
        padding-left: 5px;
        padding-right: 5px;
        height: 65px;
        border-bottom-left-radius: 32px;
        border-bottom-right-radius: 32px;
    }
    .content{
        margin-top: 60px;
        padding-left: 0;
        padding-right: 0;
        margin-left: 14.35% !important;
        padding: 15px;
    }
    .intro-y.flex.items-center.h-10{
        padding: 15px;
    }
    .grid.grid-cols-12.gap-6 .grid.grid-cols-12.gap-6.mt-5{
      padding: 15px;
    }
    .side-nav .side-nav__devider{
        margin-top: 60px;
    }
}
@media only screen and (min-width: 1299.98px) and (max-width: 1399.98px) {
    .side-nav{
        width: 14%;
        position: fixed;
        background: #222954;
        height: 100vh;
    }
    .top-bar{
        position: fixed;
        top: 0;
        padding-top: 15px;
        padding-bottom: 15px;
        width: 83%;
        margin-left: 15.6%;
        z-index: 999;
        background-color: #222954;
        left: 0;
        right:0;
        padding-left: 5px;
        padding-right: 5px;
        height: 65px;
        border-bottom-left-radius: 32px;
        border-bottom-right-radius: 32px;
    }
    .content{
        margin-top: 60px;
        padding-left: 0;
        padding-right: 0;
        margin-left: 14.35% !important;
        padding: 15px;
    }
    .intro-y.flex.items-center.h-10{
        padding: 15px;
    }
    .grid.grid-cols-12.gap-6 .grid.grid-cols-12.gap-6.mt-5{
      padding: 15px;
    }
    .side-nav .side-nav__devider{
        margin-top: 60px;
    }
}
@media only screen and (min-width: 1399.98px) and (max-width: 1499.98px){
    .side-nav{
        width: 14%;
        position: fixed;
        background: #222954;
        height: 100vh;
    }
    .top-bar{
        position: fixed;
        top: 0;
        padding-top: 15px;
        padding-bottom: 15px;
        width: 81%;
        margin-left: 18%;
        z-index: 999;
        background-color: #222954;
        left: 0;
        right:0;
        padding-left: 5px;
        padding-right: 5px;
        height: 65px;
        border-bottom-left-radius: 32px;
        border-bottom-right-radius: 32px;
    }
    .content{
        margin-top: 60px;
        padding-left: 0;
        padding-right: 0;
    }
    .intro-y.flex.items-center.h-10{
        padding: 15px;
    }
    .grid.grid-cols-12.gap-6 .grid.grid-cols-12.gap-6.mt-5{
      padding: 15px;
    }
    .side-nav .side-nav__devider{
        margin-top: 60px;
    }
}
@media only screen and (min-width: 1399.98px) and (max-width: 1799.98px){
    .side-nav{
        width: 14%;
        position: fixed;
        background: #222954;
        height: 100vh;
    }
    .top-bar{
        position: fixed;
        top: -5px;
        padding-top: 15px;
        padding-bottom: 15px;
        width: 84%;
        margin-left: 15%;
        z-index: 999;
        background-color: #222954;
        left: 0;
        right:0;
        padding-left: 5px;
        padding-right: 5px;
        height: 60px;
        border-bottom-left-radius: 32px;
        border-bottom-right-radius: 32px;
    }
    .content{
        margin-top: 60px;
        padding-left: 0;
        padding-right: 0;
        margin-left: 14.35% !important;
        padding: 15px;
    }
    .side-nav .side-nav__devider{
        margin-top: 60px;
    }
}
@media only screen and (min-width: 1799.98px)  {
    .side-nav{
        width: 14%;
        position: fixed;
        background: #222954;
        height: 100vh;
       
    }
    .top-bar{
        position: fixed;
        top: -2px;
        padding-top: 15px;
        padding-bottom: 15px;
        width: 84.2%;
        margin-left: 15%;
        z-index: 999;
        background-color: #222954;
        left: 0;
        right:0;
        padding-left: 5px;
        padding-right: 5px;
        height: 64px;
        border-bottom-left-radius: 32px;
        border-bottom-right-radius: 32px;
    }
    .content{
        margin-top: 58px;
        padding-left: 0;
        padding-right: 0;
        margin-left: 14.35% !important;
        padding: 15px;
    }
    .side-nav .side-nav__devider{
        margin-top: 60px;
    }
}
</style>
<div class="flex">
    <!-- BEGIN: Side Menu -->
    <nav class="side-nav">
        <!-- <a href="{{route('dashboard')}}" class="intro-x flex items-center pt-1">
            <img alt="" class="main-logo" src="{{ asset('dist/images/logo_removebg.png') }}" style="border-radius: 5px;">

        </a> -->
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
                <a @if(isset($menu['sub_menu'])) @else href="{{url($menu['module'].'/'.$menu['page_name'])}}" @endif class="{{ $pagesetup['first_page_name'] == $menu['page_name'] ? 'side-menu side-menu--active' : 'side-menu' }}">
                    <div class="side-menu__icon">
                        <!-- <i data-feather="{{ $menu['icon'] }}"></i> -->
                        <img class="menu-icon-img" src="{{ asset('dist/images/menu_icons/' . $menu['icon_image'].'.png') }}" width: 25px;>
                    </div>
                    <div class="side-menu__title">
                        {{ $menu['title'] }}
                        @if (isset($menu['sub_menu']))
                        <i data-feather="chevron-down" class="side-menu__sub-icon"></i>
                        @endif
                    </div>
                </a>
                @if (isset($menu['sub_menu']) )
                <ul class="{{ $pagesetup['first_page_name'] == $menu['page_name'] ? 'side-menu__sub-open' : '' }}">
                    @foreach ($menu['sub_menu'] as $subMenu)
                    @if( isset($subMenu['module']) && !is_null($subMenu['module']) )
                    <li>
                        <!-- url generate by using array index (Main module name)
                        it's looks like [module name]/[url path] -->
                        <a href="{{ url( $subMenu['module'].'/'.$subMenu['page_name']) }}" class="{{ $pagesetup['second_page_name'] == $subMenu['page_name'] ? 'side-menu side-menu--active' : 'side-menu' }}">
                            <div class="side-menu__icon">
                                <!-- <i data-feather="{{ $subMenu['icon'] }}"></i> -->
                                <img class="menu-icon-img" src="{{ asset('dist/images/menu_icons/' . $subMenu['icon_image'].'.png') }}">

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
                                <a href="{{ url( $lastSubMenu['module'].'/'.$lastSubMenu['page_name'] ) }}" class="{{ $pagesetup['third_page_name'] == $lastSubMenu['page_name'] ? 'side-menu side-menu--active' : 'side-menu' }}">
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
    
    
    <!-- END: Side Menu -->
    <!-- BEGIN: Content -->
    @include('../layout/components/top-bar')
    <div class="content">
        
        
        @yield('subcontent')
    </div>
    <!-- END: Content -->
</div>
@endsection
