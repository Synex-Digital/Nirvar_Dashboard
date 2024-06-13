
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
    <style>
        .disabled {
            pointer-events: none; /* Disable all pointer events */
            opacity: 0.5; /* Make it look disabled */
        }

        .disabled a, .disabled-link {
            color: #aaa !important; /* Grey out the text */
            pointer-events: none; /* Disable clicks */
            text-decoration: none;
        }

        .disabled a i {
            color: #aaa !important; /* Grey out the icon */
        }
    </style>

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

        @php
        $user = Auth::user();
        $doctor = $user->doctor;
        $count = 0;
        $doctor->degrees == null ? $count ++ : '';
        // // $doctor->description == null ? $count++ : '';
        $doctor->docHasSpec == null ? $count++ : '';
        $docHasSpec = $doctor->docHasSpec()->exists();
        if ($doctor->degrees == null || !$docHasSpec	 ) {
            $count++;
        }
        if ($doctor->degrees != null && $docHasSpec) {
            $disablePrescriptions = false;
        }else{
            $disablePrescriptions = true;
        }
        @endphp

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
