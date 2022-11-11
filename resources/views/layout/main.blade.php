@extends('../layout/base')

@section('body')

    <body class="app">

        @yield('content')

        <!-- BEGIN: JS Assets-->
        <script src="{{ asset('dist/js/app.min.js') }}"></script>
        <!-- <script src="{{ mix('dist/js/app.js') }}"></script> -->
        {{-- <script src="{{ asset('dist/js/currency-changer.js') }}"></script> --}}
      
        <!-- END: JS Assets-->

        <script src="{{asset('dist/js/jquery-3.6.0.js')}}"  crossorigin="anonymous"></script>
        @yield('script')
        
    </body>
    
@endsection
