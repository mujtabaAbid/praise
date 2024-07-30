<!DOCTYPE html>
<html lang="en">

<head>

    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="">
    <meta name="description" content="Kabano App">

    <title>@yield('title')</title>

    <!-- Favicon icon -->

    @include('includes.style')
    @yield('custom-css')
    <style>
        .w-50 {
            width: 50%;
        }
    </style>

    <link rel="shortcut icon" sizes="256x256" type="image/png" href="{{ asset('assets/logos/logo.png') }}">
</head>

<body>

    <!--*******************
    Preloader start
********************-->
    <div id="preloader">
        <div class="lds-ripple">
            <div></div>
            <div></div>
        </div>
    </div>
    <!--*******************
    Preloader end
********************-->

    <!--**********************************
    Main wrapper start
***********************************-->
    <div id="main-wrapper">

        <!--**********************************
        Nav header start
    ***********************************-->
        <div class="nav-header">
            <a href="{{ url('dashboard') }}" class="brand-logo">
                <img src="{{ asset('storage/logo/logo.png') }}" style="width: 200px;margin-top: 25px" />
            </a>
            <div class="nav-control">
                <div class="hamburger">
                    <span class="line"></span><span class="line"></span><span class="line"></span>
                </div>
            </div>
        </div>
        <!--**********************************
        Nav header end
    ***********************************-->

        <!--**********************************
        Header start
    ***********************************-->
        @include('includes.header')
        <!--**********************************
        Header end ti-comment-alt
    ***********************************-->

        <!--**********************************
        Sidebar start
    ***********************************-->
        @include('includes.sidebar')
        <!--**********************************
        Sidebar end
    ***********************************-->

        <!--**********************************
        Content body start
    ***********************************-->
        <div class="content-body">
            <!-- row -->
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>
        <!--**********************************
        Content body end
    ***********************************-->

        <!--**********************************
        Footer start
    ***********************************-->
        @include('includes.footer')
        <!--**********************************
        Footer end
    ***********************************-->

        <!--**********************************
       Support ticket button start
    ***********************************-->

        <!--**********************************
       Support ticket button end
    ***********************************-->

    </div>
    <!--**********************************
    Main wrapper end
***********************************-->

    <!--**********************************
 Scripts
***********************************-->
    <!-- Required vendors -->
    @include('includes.script')
    <script>
        function JobickCarousel() {

            /*  testimonial one function by = owl.carousel.js */
            jQuery('.front-view-slider').owlCarousel({
                loop: false,
                margin: 30,
                nav: true,
                autoplaySpeed: 3000,
                navSpeed: 3000,
                autoWidth: true,
                paginationSpeed: 3000,
                slideSpeed: 3000,
                smartSpeed: 3000,
                autoplay: false,
                animateOut: 'fadeOut',
                dots: true,
                navText: ['', ''],
                responsive: {
                    0: {
                        items: 1,

                        margin: 10
                    },

                    480: {
                        items: 1
                    },

                    767: {
                        items: 3
                    },
                    1750: {
                        items: 3
                    }
                }
            })
        }

        jQuery(window).on('load', function() {
            setTimeout(function() {
                JobickCarousel();
            }, 1000);
        });
    </script>
    @yield('custom-script')
</body>

</html>
