<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <!-- Vendors styles-->
        <link rel="stylesheet" href="{{asset('common/coreui/vendors/simplebar/css/simplebar.css')}}">
        <link rel="stylesheet" href="{{asset('common/coreui/css/vendors/simplebar.css')}}">
        <!-- Main styles for this application-->
        <link href="{{asset('common/coreui/icons/css/all.css')}}" rel="stylesheet">
        <link href="{{asset('common/coreui/css/style.css')}}" rel="stylesheet">
        <!-- We use those styles to show code examples, you should remove them in your application.-->
        <link href="{{asset('common/coreui/css/examples.css')}}" rel="stylesheet">
        @stack('head-scripts')
    </head>
    <body class="font-sans antialiased">
        <div class="wrapper d-flex flex-column min-vh-100 bg-light">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                <div class="wrapper d-flex flex-column min-vh-100 bg-light">
                <div class="body flex-grow-1 px-3">
                    <div class="container-lg">
                        @yield('content')
                    </div>
                </div>
                </div>
            </main>
        </div>
        <script src="{{asset('common/js/jquery-3.7.0.min.js')}}"></script>
        <script src="{{asset('common/coreui/vendors/@coreui/coreui/js/coreui.bundle.min.js')}}"></script>
        <script src="{{asset('common/coreui/vendors/simplebar/js/simplebar.min.js')}}"></script>
        <!-- Plugins and scripts required by this view-->
        <script src="{{asset('common/coreui/vendors/@coreui/utils/js/coreui-utils.js')}}"></script>
        @stack('footer-scripts')
    </body>
</html>
