<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Prescriptiion</title>
    <link rel="stylesheet" href="{{ asset('dashboard_assets/css/style.css') }}">
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous"> --}}
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
    @media only screen and (min-width: 1200px) and (max-width: 1400px) {
        .content-body {
            margin-left: 0rem;
        }
    }
    [data-header-position="fixed"] .content-body {
        padding-top: 0rem;
    }
    </style>
</head>

<body>
@php
        $doctors = $prescription->doctor;
        $patients = $prescription->patient;
        function calculateAge($birthdate, $currentDate)
    {
        $birthDate = new DateTime($birthdate);
        $currentDate = new DateTime($currentDate);
        $age = $currentDate->diff($birthDate)->y;
        return $age;
    }
    function weight($input) {
        $weight = null;
        $parts = explode(',', $input);
        if (isset($parts[0])) {
            $weight = trim($parts[0]);
        }
        return $weight;
    }
    function height($input) {
        $height = null;
        $feet = null;
        $inches = null;
        $parts = explode(',', $input);
        if (isset($parts[1])) {
            $height = trim($parts[1]);
        }
        if ($height !== null) {
            preg_match('/(\d+)\s*FT\s*(\d*)\s*IN*/i', $height, $matches);
            if (isset($matches[1])) {
                $feet = $matches[1];
            }
            if (isset($matches[2])) {
                $inches = $matches[2];
            }
        }
        return $height;
    }
    function tests($input) {
        $tests = null;
        $parts = $input ? explode('" "', trim($input, '"')) : [];
        if (isset($parts)) {
            $tests = $parts;
        }
        return $tests;
    }
    function prescriptionAdvice($input) {
        $advice = null;
        $parts = $input ? explode('" "', trim($input, '"')) : [];
        if (isset($parts)) {
            $advice = $parts;
        }
        return $advice;
    }
@endphp
<div id="main-wrapper">
    <div class="content-body">
        <div class="container-fluid">

          <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-center align-items-center">
                        <img src="{{asset('dashboard_assets/images/logoN.png')}}" width="65" class="mx-2"  alt="">
                        <img src="{{asset('dashboard_assets/images/logotextN.png')}}" width="160" class="" alt="" style="margin-top:15px;">
                    </div>
                    <div class="card-body">

                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <h5 class="text-uppercase"> {{ $doctors->user->name }}</h5>
                                <p class="fw-light text-capitalize mb-0 " style="color: black !important">{{ $doctors->docHasSpec? $doctors->docHasSpec->speciality->name : 'UNKNOWN' }}</p>
                                <p class="fw-light text-capitalize mb-0 " style="color: black !important">{{ $doctors->degrees }}</p>
                                <p class="fw-light text-capitalize mb-0 " style="color: black !important">{{ $doctors->registration }}</p>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12  ">
                                <div class="float-end flt">

                                    <h5>Date: <span class="fw-light text-capitalize mb-0 " style="color: black !important"> {{ $prescription->created_at->format('d-M-y') }} </span></h5>
                                    <h5>Ref: <span class="fw-light text-capitalize mb-0 " style="color: black !important"> {{ $prescription->reference }}  </span></h5>
                                </div>
                            </div>
                        </div>
                        <div class="row border-top border-bottom   mt-2 mb-3  ">
                           <div class="col-lg-12 col-md-12 col-sm-12 mt-2  d-flex flex-row">
                               <h5 >Patient Name: <span class="fw-light text-capitalize mb-0 me-4 " style="color: black !important"> {{ $patients->user? $patients->user->name  : 'UNKNOWN'}}</span>  </h5>
                               <h5 class="mb-0"> Age: <span class="fw-light  me-4 " style="color: black !important" > {{ calculateAge($patients->date_of_birth, $patients->created_at) }}</span> </h5>
                               <h5 class="mb-0"> Gender: <span class="fw-light text-capitalize me-4 " style="color: black !important" > {{ $patients->gender }}</span> </h5>
                               @if ($patients->blood_group != null)
                               <h5 class="mb-0"> Blood Group: <span class="fw-light fw-capitalize me-4 " style="color: black !important" > {{ $patients->blood_group }}</span> </h5>
                               @endif
                               @if ($patients->weight_height != null)
                                @if (weight($patients->weight_height) != null)
                                    <h5 class="mb-0"> Weight: <span class="fw-light  me-4 " style="color: black !important" > {{ weight($patients->weight_height) }}</span> </h5>
                                @endif
                                @if (height($patients->weight_height) != null)
                                    <h5 class="mb-0"> Height: <span class="fw-light  me-4 " style="color: black !important" > {{  height($patients->weight_height) }}</span> </h5>
                                @endif
                               @endif
                               <h5 class="mb-0"> Contact: <span class="fw-light me-4 " style="color: black !important" > {{ $patients->user ? $patients->user->number : 'UNKNOWN' }}</span> </h5>
                            </div>


                        </div>
                        <div class=" mt-2 ">
                            <div class=" row mb-2">
                                <h5 class="col-lg-12 col-sm-12 col-md-12 ">Chief Complaint: <span class="fw-light text-capitalize me-4 " style="color: black !important" > {{ $prescription->chief_complaint }}</span></h5>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 ">
                                    <h5 class="">Diagnosis Tests:</h5>
                                    <ul style="margin-left:35px;" >
                                        @forelse (tests($prescription->tests) as $test )
                                        <li style="list-style-type: inherit">{{$test}}</li>
                                        @empty
                                            <li style="list-style-type: inherit">No Tests</li>
                                        @endforelse
                                    </ul>

                                </div>
                                <div class="col-lg-6">
                                    <h5>Medicine:</h5>

                                        <div class="row mt-2 mb-1  g-3 " style="">

                                            @forelse ($prescription->medicine as $medicine )
                                            <ul class="d-flex   " style="margin-left: 35px;">
                                                <li style="list-style-type: inherit" class="me-3 text-capitalize fst-italic">  {{$medicine->type}}</li>
                                                <li  class="me-3 fw-semibold">{{$medicine->drug ? $medicine->drug->name : 'UNKNOWN'}}</li>
                                                <li  class="me-3  fw-semibold">{{$medicine->mg_ml}}</li>

                                            </ul>
                                            <ul class="d-flex justify-content-between mt-0 border-bottom" >
                                                <li class=" me-3" style="margin-left: 35px;">{{$medicine->dose}}</li>
                                                <li class="me-3">{{$medicine->advice}}</li>
                                                <li class="me-3">{{$medicine->duration}}</li>
                                            </ul>
                                            @empty

                                            @endforelse


                                        </div>


                                    <div class=" mt-5">
                                        <div class="row mt-2   g-3 ">
                                            <h5>Advice:</h5>
                                            <ul style="margin-left:35px;" >
                                                @forelse (prescriptionAdvice($prescription->prescription_advice) as $advice )
                                                    <li style="list-style-type: inherit">{{$advice}}</li>
                                                @empty
                                                    <li style="list-style-type: inherit">No Advice</li>
                                                @endforelse
                                            </ul>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
          </div>
        </div>
    </div>







</div>

</body>
<script src="{{asset('dashboard_assets/vendor/global/global.min.js')}}"></script>
<script src="{{asset('dashboard_assets/js/custom.min.js')}}"></script>

</html>
