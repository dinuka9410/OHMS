@extends('../layout/menu/' . session('layout'))

<!-- this will add styles to the form-layout page using the page  -->

@yield('style-area')
@section('style')

@endsection
@section('subcontent')

<!-- Top Bar ... Contain with page name and necessary buttuns -->
<div class="header-line grid grid-cols-12 gap-6 mt-4">

    <div class="col-span-12 lg:col-span-6 grid gap-3">
        <div class="intro-y block sm:flex items-center h-10 flex">
            <img class="menu-icon-img title-icon black-icon" id="title_icon">
            <h2 class="font-medium text-base mr-auto" id="form_title"> @yield('form-name') </h2>
        </div>
    </div>

    <div class="col-span-12 lg:col-span-6 grid gap-3 large-screen">
        <div class="intro-y block sm:flex items-center h-10">
            <div class="ml-auto mt-3 sm:mt-0 relative text-gray-700 dark:text-gray-300">
                <div class="flex items-center border-b border-gray-200 dark:border-dark-5" id="btn-holder">
                    <span id="new_button"></span>
                    <button class="button w-20 bg-theme-9 text-white " hidden id="save">Save</button>&nbsp;
                    <button class="button w-20 bg-theme-1 text-white " hidden id="clear">Clear</button>&nbsp;
                    <a class="button w-20 bg-theme-6 text-white " id="cancel" href="{{ url()->previous() }}">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-content grid grid-cols-12 gap-2 mt-1">
    <!-- form area -->
    <div class="col-span-12 lg:col-span-6 grid gap-1">
        <div id="editdiv" class="intro-y box p-1 mt-1 sm:mt-1"> 
            <div class="flex flex-col xl:flex-row xl:items-center">
                <div class="p-1" id="form-validation">
                    @yield('form-area')
                </div>
            </div>
        </div>
    </div>
    <!-- table area -->
    <div class="col-span-12 lg:col-span-6 grid gap-1 full-height-overflow">
        <div class="intro-y box p-1 mt-12 sm:mt-1">
            @yield('table-area')
        </div>
    </div>

    <div class="col-span-12 lg:col-span-6 grid gap-3 small-screen">
        <div class="intro-y block sm:flex items-center h-10">
            <div class="ml-auto mt-3 sm:mt-0 relative text-gray-700 dark:text-gray-300">
                <div class="flex items-center border-b border-gray-200 dark:border-dark-5" id="btn-holder">
                    <span id="new_button"></span>
                    <button class="button w-20 bg-theme-9 text-white " hidden id="update">Update</button>&nbsp;
                    <button class="button w-20 bg-theme-9 text-white " hidden id="save">Save</button>&nbsp;
                    <button class="button w-20 bg-theme-1 text-white " hidden id="clear">Clear</button>&nbsp;
                    <a class="button w-20 bg-theme-6 text-white " id="cancel" href="{{ url()->previous() }}">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="crud_info_div additional-info-box" id="status_box" style=" margin-bottom: 1%; margin-top: 4%; float:right; display:none;">

    <div class="flex flex-col sm:flex-row items-center pb-1 border-b border-gray-200 dark:border-dark-5">
        <i data-feather="bell" class="notification__icon dark:text-gray-300 pr-1 sucess"></i>
        <h2 class="font-medium text-base mr-auto" id='status'> @yield('status')</h2>

        <div class="w-full sm:w-auto flex items-center sm:ml-auto mt-3 sm:mt-0">
            @yield('status_button')
        </div>
    </div>

    <div class="additional-info-box">
        <div class="table-box">
            <table>
                @yield('additional-info')
            </table>
        </div>
    </div>

</div>
<!-- // if you need to write js scripts to the layout then write them in below script section -->

@section('script')

@include('includes.form-scripts')


<!-- this will validate the given form with the id of frmdt  -->

<script>
    $('#clear').click(function(e) {
        window.location.reload();
    });

    // title image insert

    let formname = form_title.innerHTML;
    var base_url = window.location.origin;
    let image_path = formname.replace(/\s/g, '');
    let img_url = base_url + "/dist/images/menu_icons/" + image_path + ".png";
    document.getElementById('title_icon').setAttribute('src', img_url);

    // title image insert
</script>



@endsection


<!-- this will add scripts to the form-layout  using the page  -->

@yield('script-area')

@endsection
