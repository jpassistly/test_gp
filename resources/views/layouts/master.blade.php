<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <title> @yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ URL::asset('assets/images/favicon.ico') }}">
    @include('layouts.head-css')

</head>

@section('body')

    <body data-sidebar="dark">
    @show
    <!-- Begin page -->
    <div id="layout-wrapper">
        @include('layouts.topbar')
        @include('layouts.sidebar')
        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <style>
                    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
                    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
                        color: #ffffff !important;
                        border: 1px solid #979797;
                        background-color: white;
                        background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #fff), color-stop(100%, #dcdcdc));
                        background: -webkit-linear-gradient(top, #fff 0%, #dcdcdc 100%);
                        background: -moz-linear-gradient(top, #fff 0%, #dcdcdc 100%);
                        background: -ms-linear-gradient(top, #fff 0%, #dcdcdc 100%);
                        background: -o-linear-gradient(top, #fff 0%, #dcdcdc 100%);
                        background: linear-gradient(to bottom, #255328 0%, #255328 100%);
                    }
                    .dt-responsive{
                        margin-top:0px !important;
                    }
                </style>
                <div class="container-fluid">
                    @yield('content')
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->
            @include('layouts.footer')
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->

    <!-- Right Sidebar -->
    {{--  @include('layouts.right-sidebar') --}}
    <!-- /Right-bar -->

    <!-- JAVASCRIPT -->
    @include('layouts.vendor-scripts')
    <script>
        $(document).ready(function() {
            // Set a timeout to dismiss the alert after 5 seconds (5000 milliseconds)
            setTimeout(function() {
                $('.alert-success').alert('close');
            }, 3000);
        });
    </script>
</body>

</html>
