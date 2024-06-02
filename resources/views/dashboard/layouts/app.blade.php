
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta -->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="keywords" content="admin dashboard, admin template, administration, analytics, bootstrap, disease, doctor, elegant, health, hospital admin, medical dashboard, modern, responsive admin dashboard">
	<meta name="author" content="DexignZone">
	<meta name="robots" content="">
	<meta name="description" content="Welly is a clean-code, responsive Django Admin template that can be easily customized to fit the needs of various hospital, medical dashboard, health, doctor, and other businesses.">
	<meta property="og:title" content="Welly - Django Hospital Admin Dashboard Bootstrap Template">
	<meta property="og:description" content="Welly is a clean-code, responsive Django Admin template that can be easily customized to fit the needs of various hospital, medical dashboard, health, doctor, and other businesses.">
	<meta property="og:image" content="../social-image.png">
	<meta name="format-detection" content="telephone=no">

	<!-- Mobile Specific -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- Title -->

	<title>Nirvar</title>
    @include('dashboard.layouts.headerLink')
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
</body>
</html>
