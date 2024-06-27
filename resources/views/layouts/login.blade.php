<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>{{config('constant.siteTitle')}}</title>
        <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/auth.css') }}" />
        <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/responsive.css') }}" />
        <script src="{{ asset('admin/js/jquery-3.6.0.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>
        @stack('css')
    </head>
    <body>
        <div class="auth-section">
            <div class="row w-100 mx-0">
                <div class="col-lg-@yield('col',4) mx-auto">
                    <div class="auth-form">
                        <div class="brand-logo">
                            <img src="{{ asset('admin/images/logo.svg') }}" alt="logo" />
                        </div>
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
        <script src="{{asset('plugins/js/jquery.validate.min.js')}}"></script>
        @stack('js')
    </body>
</html>

