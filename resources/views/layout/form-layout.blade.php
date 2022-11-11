@extends('../layout/menu/' . session('layout'))

<!-- this will add styles to the form-layout page using the page  -->

@yield('style-area')

@section('subcontent')

<div class="col-span-12 lg:col-span-8 xxl:col-span-9">
    <!-- BEGIN: Display Information -->
    <div class="intro-y box lg:mt-5">

        <div class="flex items-center p-5 border-b border-gray-200 dark:border-dark-5" id="btn-holder">
            <img class="menu-icon-img title-icon black-icon" id="title_icon">
            <h2 class="text-lg font-medium mr-auto" id="form_title">@yield('form-name') </h2>
            <span id="new_button"></span>
            <button class="button w-20 bg-theme-9 text-white mt-3" hidden id="update">Update</button>&nbsp;
            <button class="button w-20 bg-theme-9 text-white mt-3" hidden id="save">Save</button>&nbsp;
            <button class="button w-20 bg-theme-1 text-white mt-3" hidden id="clear">Clear</button>&nbsp;
            <a class="button w-20 bg-theme-6 text-white mt-3" id="cancel" href="{{ url()->previous() }}">Cancel</a>

        </div>

        <div class="p-5" id="form-validation">
            @yield('form-area')
        </div>

        <!--
            Status Area
            status Ex: Active/Terminated)
            status button Ex: switch or radio buttan
            additional-info Ex: create user / create date time
         -->
        <div class="crud_info_div additional-info-box" id="status_box"  style=" margin-bottom: 1%; margin-top: 4%; float:right; display:none;">

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

    let formname = form_title.innerHTML;
    var base_url = window.location.origin;
    let image_path = formname.replace(/\s/g, '');
    let img_url = base_url + "/dist/images/menu_icons/" + image_path + ".png";
    // document.getElementById("demo").innerHTML = base_url;
    document.getElementById('title_icon').setAttribute('src', img_url);
</script>



@endsection


<!-- this will add scripts to the form-layout  using the page  -->

@yield('script-area')

@endsection
