
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta -->
	<meta charset="utf-8">


	<!-- Mobile Specific -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- Title -->

	<title>Nirvar @yield('title')</title>
    @include('dashboard.layouts.headerLink')
    @yield('style')
</head>
<body>

    <!--*******************
        Preloader start
    ********************-->
    <div id="preloader">
        <div class="sk-three-bounce">
            <div class="sk-child sk-bounce1"></div>
            <div class="sk-child sk-bounce2"></div>
            <div class="sk-child sk-bounce3"></div>
        </div>
    </div>
    <!--*******************
        Preloader end
    ********************-->

    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">


        @include('dashboard.layouts.navBar')

        @include('dashboard.layouts.sideBar')
        <div class="content-body">
            <!-- row -->
			<div class="container-fluid">
                @yield('content')
            </div>
        </div>




    </div>


    @include('dashboard.layouts.footer')

    @include('dashboard.layouts.scriptLink')
    @yield('script')
</body>
</html>
