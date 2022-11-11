@extends('../layout/menu/' . session('layout'))

@section('subcontent')
    <div class="grid grid-cols-12 gap-6">
        <div class="col-span-12 xxl:col-span-9 grid grid-cols-12 gap-6">
            <!-- BEGIN: General Report -->
            <div class="col-span-12 mt-8">
                <div class="intro-y flex items-center h-10">
                    <h2 class="text-lg font-medium truncate mr-5">
                    <img  class="menu-icon-img title-icon black-icon" src="{{ asset('dist/images/menu_icons/dashboard.png') }}">
                    Dashboard
                    </h2>
                </div>
            </div>
            <!-- END: General Report -->
  
 
        </div>

    </div>

    <script>
        $(document).ready(function(){
    $('.report-box').matchHeight();
})
    </script>
@endsection
