@extends('../layout/menu/' . session('layout'))


<!-- this will add styles to the form-layout page using the page  -->

@yield('style-area')

@section('subcontent')

<div class="col-span-12 lg:col-span-8 xxl:col-span-9">
    <!-- BEGIN: Display Information -->
    <div class="intro-y box lg:mt-2">

    <div class="flex items-center p-3 pt-5 border-b border-gray-200 dark:border-dark-5" id="btn-holder">
            <img class="menu-icon-img title-icon black-icon" id="title_icon" >
            <h2 class="font-medium text-base mr-auto" id="form_title">@yield('title') </h2>
        </div>

        <div style="">


            @yield('home-content')

        </div>

    </div>

</div>


<!-- // if you need to write js scripts to the layout then write them in below script section -->

@section('script')

@include('includes.home-scripts')

<script>
    $(document).ready(function() {
        $('#dttbl').DataTable();
    });

    // title image insert

    let formname = form_title.innerHTML;
    var base_url = window.location.origin;
    let image_path = formname.replace(/\s/g, '');
    let img_url = base_url + "/dist/images/menu_icons/" + image_path + ".png";
    document.getElementById('title_icon').setAttribute('src', img_url);

    // title image insert
    // console.log(image_path);
</script>

@endsection

<!-- this will add scripts to the form-layout using the page  -->
@yield('script-area')

@endsection
