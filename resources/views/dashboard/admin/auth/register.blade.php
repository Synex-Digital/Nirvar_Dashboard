

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
<link rel="icon" type="image/png" sizes="16x16" href="{{asset('dashboard_assets/images/logoN.png')}}">
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

                                    <h4 class="text-center mb-4 text-white">Sign Up</h4>
                                    <form method="POST" action="{{ route('adminRegisterStore') }}">
                                        @csrf

                                        <div class=" mb-3">
                                            <label class="mb-1 text-white"><strong>Name</strong></label>
                                                <input id="name" type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                                                @error('name')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                        </div>


                                        <div class=" mb-3">
                                            <label class="mb-1 text-white"><strong>Email</strong></label>
                                                <input id="email" type="email" class="form-control form-control-sm @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                                                @error('email')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                        </div>

                                        <div class=" mb-3">
                                            <label class="mb-1 text-white"><strong>Password</strong></label>
                                                <input id="password" type="password" class="form-control form-control-sm @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                                @error('password')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                        </div>

                                        <div class=" mb-3">
                                            <label class="mb-1 text-white form-label"><strong>Confirm Password</strong></label>

                                                <input id="password-confirm" type="password" class="form-control form-control-sm" name="password_confirmation" required autocomplete="new-password">

                                        </div>
                                        <div class="text-center mt-5" >
                                            <button type="submit" class="btn bg-primary text-white ">Resgister</button>
                                        </div>

                                    </form>
                                    {{-- <div class="new-account mt-3">
                                        <p class="text-white">Already have an account? <a class="text-white" href="{{ route('login') }}">Sign in</a></p>
                                    </div> --}}
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

