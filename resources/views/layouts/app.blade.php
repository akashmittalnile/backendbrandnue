<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>{{config('constant.siteTitle')}}</title>
        <link rel="stylesheet" type="text/css" href="{{asset('admin/css/common.css')}}" />
        <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/home.css') }}" />
        <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/responsive.css') }}" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @stack('css')
        <script>
            const base_url = "{{ url('/') }}";
        </script>
    </head>
    <body>
        <div class="main-site">
            <noscript>You need to enable JavaScript to run this Site.</noscript>
            <div class="please-wait"><p><span class="fa fa-spinner fa-spin" role="status" aria-hidden="true"></span> Please wait....</p></div>
            @include('layouts.header')
            <div class="page-body-wrapper">
                @include('layouts.sidebar')
                @yield('content')
            </div>
        </div>
        <script src="{{ asset('admin/js/jquery-3.6.0.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('admin/js/function.js') }}" type="text/javascript"></script>              
        
        @stack('js')
    </body>
</html>
