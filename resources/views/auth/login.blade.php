
<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <!-- Meta -->
	<meta charset="utf-8">


	<!-- Mobile Specific -->
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- Title -->
	<title>Nirvar Dashoboard</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
  <link href="{{ asset('dashboard_assets/css/style.css') }}" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&amp;family=Roboto:wght@100;300;400;500;700;900&amp;display=swap" rel="stylesheet">
    <style>
        body {
            background-color:#8fe5d0 !important;
        }
        .authincation-content{
            background: #0aaa97;
            box-shadow: 0 0 35px 0 rgba(0, 0, 0, 0.15);
            border-radius: 5px;
        }
    </style>
</head>

<body class="h-100">
    <div class="authincation h-100">
        <div class="container h-100">
            <div class="row justify-content-center h-100 align-items-center">
                <div class="col-md-6">
                    <div class="authincation-content">
                        <div class="row no-gutters">
                            <div class="col-xl-12">
                                <div class="auth-form">
									<div class="text-center mb-3">
										<a href="index.html"><img src="images/logo-full.png" alt=""></a>
									</div>
                                    <h4 class="text-center mb-4 text-white">Sign in your account</h4>
                                    <form method="POST" action="{{ route('login') }}">
                                        @csrf
                                        <div class="form-group">
                                            <label class="mb-1 text-white"><strong>Email</strong></label>
                                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label class="mb-1 text-white"><strong>Password</strong></label>
                                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                                            @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        {{-- <div class="row d-flex justify-content-between mt-4 mb-2">
                                            <div class="form-group">
                                               <div class="form-check custom-checkbox ms-1 text-white">
													<input type="checkbox" class="form-check-input" id="basic_checkbox_1">
													<label class="form-check-label" for="basic_checkbox_1">Remember my preference</label>
												</div>
                                            </div>
                                            <div class="form-group">
                                                <a class="text-white" href="page-forgot-password.html">Forgot Password?</a>
                                            </div>
                                        </div> --}}
                                        <div class="text-center mt-5" >
                                            <button type="submit" class="btn btn-primary  ">Sign Me In</button>
                                        </div>
                                    </form>
                                    <div class="new-account mt-3">
                                        <p class="text-white">Don't have an account? <a class="text-white" href="{{ route('register') }}">Sign Up</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Required vendors -->
       <script src="{{asset('dashboard_assets/vendor/global/global.min.js')}}"></script>
	<script src="{{asset('dashboard_assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js')}}"></script>
     <script src="{{asset('dashboard_assets/js/custom.min.js')}}"></script>
    <script src="{{asset('dashboard_assets/js/deznav-init.js')}}"></script>

</body>

</html>

