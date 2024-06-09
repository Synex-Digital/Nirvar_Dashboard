@extends('dashboard.layouts.app')

@section('title') Create Prescription @endsection
@section('style')
<link rel="stylesheet" href="{{asset('dashboard_assets/vendor/select2/css/select2.min.css')}}">
{{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" /> --}}
<style>
    .form-control-sm{
        height: 32px !important;
    }
    .card-header{
        border-bottom: none;
    }
    @media(max-width: 778px) {
        .flt{
            margin-top: 5px;
            float: none !important;
        }
    }
    .select2-selection__rendered{
        min-height: 0 !important;
    }
    .select2-selection__rendered, .select2-results__option, .select2-search__field {
    font-size: 12px !important;
}
.remove-button {
    width: auto; /* Adjust the width as needed */
    padding: 3px 10px; /* Adjust padding for smaller size */
    margin-right: 5px; /* Adjust margin for positioning */
}

</style>
@endsection

@section('content')
<div class="page-titles">
    <h4>Prescription</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('home')}}">Dashboard</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">Preview Prescription</a></li>
    </ol>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-center align-items-center">
        <img src="{{asset('dashboard_assets/images/logoN.png')}}" width="65" class="mx-2"  alt="">
        <img src="{{asset('dashboard_assets/images/logotextN.png')}}" width="160" class="" alt="" style="margin-top:15px;">
    </div>
    <div class="card-body">

        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <h5> {{ $doctors->user->name }}</h5>
                <p>{{ $doctors->docHasSpec->speciality->name }}</p>
                {{-- <h5><span class="text-muted  fw-light "> {{ $doctors->docHasSpec->speciality->name }}  </span></h5> --}}
                <h5> <span class="text-muted fw-light "> {{ $doctors->degrees }}  </span></h5>
                <h5> <span class="text-muted fw-light fst-italic"> </span></h5>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 ">
                <div class="float-end flt">

                    <h5>Date: <span class=" text-muted fw-light fst-italic"> </span></h5>
                    <h5>Ref: <span class="text-muted fw-light fst-italic"> </span></h5>
                </div>
            </div>
        </div>
        <div class="row border-top border-bottom   mt-3 mb-3  ">
           <div class="col-lg-3">
                <p class="mb-0">Patient Number  </p>



           </div>
           <div class="col-lg-3">
                <p class="mb-0">Patient Name  </p>

           </div>

           <div class="col-lg-2">
                <p class="mb-0">Gender </p>

           </div>
           <div class="col-lg-2">
                <p class="mb-0"> Age </p>

            </div>
           <div class="col-lg-2">
                <p class="mb-0">Weight</p>


            </div>
            <div class="col-lg-2 ">
                <p class="mb-0">Blood Group</p>

            </div>
           <div class="col-lg-2 mb-3">
                <p class="mb-0">Height</p>
                <div class="row">
                    <div class="col-lg-6  mb-2">


                    </div>
                    <div class="col-lg-6 ">


                    </div>
                </div>
            </div>

        </div>
        <div class=" mt-3">
            <div class="form-group row">
                <h5 class="col-lg-2 col-sm-12 col-md-12 mt-2">Cheif Complaint: </h5>
                <div class="col-lg-10 col-md-12 col-sm-12">

                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 ">
                    <h5 class="">Investigations:</h5>
                    <div class="row mt-2 mb-1  g-3 invest">
                        <div class="col-12 my-1 ">

                        </div>
                    </div>

                </div>
                <div class="col-lg-6">
                    <h5>Medicine:</h5>

                        <div class="row mt-2 mb-1  g-3 medi" style="">
                            <div class="col-4 my-1 ">
                                <input type="text" name="type[]" class="form-control form-control-sm bg-white input-default  inp " placeholder="Type">
                            </div>
                            <div class="col-4  my-1   ">

                            </div>
                            <div class="col-4 my-1 ">
                                <input type="text" name="strength[]" class="form-control form-control-sm bg-white input-default  inp " placeholder="Strength">
                            </div>
                            <div class="col-4 my-1 ">
                                <input type="text" name="dose[]" class="form-control form-control-sm bg-white input-default  inp " placeholder="Dose(1+0+1)">
                            </div>
                            <div class="col-4 my-1 ">
                                <input type="text" name="duration[]" class="form-control form-control-sm bg-white input-default  inp " placeholder="Duration">
                            </div>
                            <div class="col-4 my-1 ">
                                <input type="text" name="medicineAdvice[]" class="form-control form-control-sm bg-white input-default  inp " placeholder="Before Or After Meal     ">
                            </div>
                        </div>


                    <div class=" mt-5">
                    <div class="row mt-2   g-3 advice">
                            <div class="col-12 my-1 ">
                                <input type="text" name="advice[]" class="form-control form-control-sm bg-white input-default  inp " placeholder="Advice">
                            </div>

                        </div>
                    </div>


                </div>
            </div>
        </div>


    </div>
</div>

@endsection
@section('script')

<script src="{{asset('dashboard_assets/vendor/select2/js/select2.full.min.js')}}"></script>
<script src="{{asset('dashboard_assets/js/plugins-init/select2-init.js')}}"></script>








<script>
    $(document).ready(function() {
        // Function to check if the screen width is greater than 768px (desktop size)
        function isDesktopView() {
            return window.innerWidth > 768;
        }

        // Run the code only if it's desktop view
        if (isDesktopView()) {
            // Simulate a click on the hamburger menu
            $('#hamburger').click();
        }
    });
</script>
@endsection

