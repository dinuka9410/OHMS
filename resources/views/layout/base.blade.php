<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ session('theme') }}">
<!-- BEGIN: Head -->
<head>
    <meta charset="utf-8">
    <link href="{{ asset('dist/images/') }}" rel="shortcut icon">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description">
    <meta name="author" content="LEFT4CODE">

    <title>Online HMS</title>

    @yield('head')

    <!-- this will enable the global msg notifications in the system -->
    @include('includes.common_js')

    <!-- BEGIN: CSS Assets-->
    <link rel="stylesheet" href="{{ mix('dist/css/app.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{asset('dist/css/custom.css')}}">
    <style>

    <link rel="stylesheet" href='{{asset('dist/css/bootstrap.min.css')}}' integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    </style>
    <!-- END: CSS Assets-->


</head>
<!-- END: Head -->

@yield('body')

</html>
