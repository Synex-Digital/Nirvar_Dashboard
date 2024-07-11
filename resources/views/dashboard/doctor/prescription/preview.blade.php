@extends('dashboard.layouts.app')

@section('title')
    Preview Prescription
@endsection
@section('style')
    <link rel="stylesheet" href="{{ asset('dashboard_assets/vendor/select2/css/select2.min.css') }}">
    {{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" /> --}}
    <style>
        .form-control-sm {
            height: 32px !important;
        }

        .card-header {
            border-bottom: none;
        }

        @media(max-width: 778px) {
            .flt {
                margin-top: 5px;
                float: none !important;
            }
        }

        .select2-selection__rendered {
            min-height: 0 !important;
        }

        .select2-selection__rendered,
        .select2-results__option,
        .select2-search__field {
            font-size: 12px !important;
        }

        .remove-button {
            width: auto;
            /* Adjust the width as needed */
            padding: 3px 10px;
            /* Adjust padding for smaller size */
            margin-right: 5px;
            /* Adjust margin for positioning */
        }
    </style>
@endsection
@php
    function calculateAge($birthdate, $currentDate)
    {
        $birthDate = new DateTime($birthdate);
        $currentDate = new DateTime($currentDate);
        $age = $currentDate->diff($birthDate)->y;
        return $age;
    }
    function weightHeight(String $input)
    {
        $data = $input;

// Initialize variables
        $weight = null;
        $height = null;
        if($data){
          if (preg_match('/^(\d+),?\s*(\d+)\s*FT\.?\s*(\d*)\s*IN?$/i', $data, $matches)) {
                $weight = !empty($matches[1]) ? $matches[1] : null;
                $height = trim($matches[2] . ' FT ' . $matches[3] . ' IN');
            } elseif (preg_match('/^(\d+),?\s*(\d+)\s*FT$/i', $data, $matches)) {
                $weight = !empty($matches[1]) ? $matches[1] : null;
                $height = trim($matches[2] . ' FT');
            } elseif (preg_match('/^(\d+)\s*FT\.?\s*(\d*)\s*IN?$/i', $data, $matches)) {
                $weight = null;
                $height = trim($matches[1] . ' FT ' . $matches[2] . ' IN');
            } elseif (preg_match('/^(\d+)$/i', $data, $matches)) {
                $weight = $matches[1];
                $height = null;
            } elseif (preg_match('/^(\d+)\s*FT$/i', $data, $matches)) {
                $weight = null;
                $height = trim($matches[1] . ' FT');
            } elseif (preg_match('/^(\d+)\s*FT\.?\s*(\d+)\s*IN?$/i', $data, $matches)) {
                $weight = null;
            }
        }

        return ['weight'=>$weight, 'height'=>$height];

    }

    function tests($input)
    {
        $tests = null;
        $parts = $input ? explode('" "', trim($input, '"')) : [];
        if (isset($parts)) {
            $tests = $parts;
        }
        return $tests;
    }
    function prescriptionAdvice($input)
    {
        $advice = null;
        $parts = $input ? explode('" "', trim($input, '"')) : [];
        if (isset($parts)) {
            $advice = $parts;
        }
        return $advice;
    }

@endphp
@section('content')
    {{-- modal --}}
    <div class="modal fade" id="basicModal" style="display: none;" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('mail.prescription') }}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Mail</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal">
                        </button>
                    </div>
                    <div class="modal-body">

                        <div class="mb-2">
                            <input type="text" class="form-control" name="email" value="{{ $patients->user ? $patients->user->email : 'UNKNOWN' }}" placeholder="email">
                            <input type="hidden"  name="prescription_id" value="{{ $prescriptions->id }}" >

                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="page-titles d-flex justify-content-between">
        <div>
            <h4>Prescription</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Preview Prescription</a></li>
            </ol>
        </div>
        <div>
            <!-- Button trigger modal -->
            <button type="button" class="btn  btn-primary btn-sm mb-2" id="print">Print</button>

            <button type="button" class="btn  btn-primary btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#basicModal">Mail</button>

        </div>
    </div>

    <div class="card" id="printable">
        <div class="card-header d-flex justify-content-center align-items-center">
            <img src="{{ asset('dashboard_assets/images/logoN.png') }}" width="65" class="mx-2" alt="">
            <img src="{{ asset('dashboard_assets/images/logotextN.png') }}" width="160" class="" alt=""
                style="margin-top:15px;">
        </div>
        <div class="card-body">

            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <h5 class="text-uppercase"> {{ $doctors->user->name }}</h5>
                    <p class="fw-light text-capitalize mb-0 " style="color: black !important">
                        {{ $doctors->docHasSpec? $doctors->docHasSpec->speciality->name : 'UNKNOWN' }}</p>
                    <p class="fw-light text-capitalize mb-0 " style="color: black !important">{{ $doctors->degrees }}</p>
                    <p class="fw-light text-capitalize mb-0 " style="color: black !important">{{ $doctors->registration }}
                    </p>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12  ">
                    <div class="float-end flt">

                        <h5>Date: <span class="fw-light text-capitalize mb-0 " style="color: black !important">
                                {{ $prescriptions->created_at->format('d-M-y') }} </span></h5>
                        <h5>Ref: <span class="fw-light text-capitalize mb-0 " style="color: black !important">
                                {{ $prescriptions->reference }} </span></h5>
                    </div>
                </div>
            </div>
            <div class="row border-top border-bottom   mt-2 mb-3  ">
                <div class="col-lg-12 col-md-12 col-sm-12 mt-2  d-flex flex-row">
                    <h5>Patient Name:  <span class="fw-light text-capitalize mb-0 me-4 " style="color: black !important">
                            {{ $patients->user? $patients->user->name : 'UNKNOWN' }}</span> </h5>
                    <h5 class="mb-0"> Age: <span class="fw-light  me-4 " style="color: black !important">
                            {{ calculateAge($patients->date_of_birth, $patients->created_at) }}</span> </h5>
                    <h5 class="mb-0"> Gender: <span class="fw-light text-capitalize me-4 "
                            style="color: black !important"> {{ $patients->gender }}</span> </h5>
                    @if ($patients->blood_group != null)
                        <h5 class="mb-0"> Blood Group: <span class="fw-light fw-capitalize me-4 "
                                style="color: black !important"> {{ $patients->blood_group }}</span> </h5>
                    @endif
                    @if ($patients->weight_height != null)
                        @if (weightHeight($patients->weight_height)['weight'] != null)
                            <h5 class="mb-0"> Weight: <span class="fw-light  me-4 " style="color: black !important">
                                {{ weightHeight($patients->weight_height)['weight'] }}
                            </span> </h5>
                        @endif
                        @if (weightHeight($patients->weight_height)['height'] != null)
                            <h5 class="mb-0"> Height: <span class="fw-light  me-4 " style="color: black !important">
                                    {{ weightHeight($patients->weight_height)['height'] }}</span> </h5>
                        @endif
                    @endif
                    <h5 class="mb-0"> Contact: <span class="fw-light me-4 " style="color: black !important">
                            {{ $patients->user? $patients->user->number : 'UNKNOWN' }}</span> </h5>
                </div>


            </div>
            <div class=" mt-2 ">
                <div class=" row mb-2">
                    <h5 class="col-lg-12 col-sm-12 col-md-12 ">Chief Complaint: <span class="fw-light text-capitalize me-4 "
                            style="color: black !important"> {{ $prescriptions->chief_complaint }}</span></h5>
                </div>
                <div class="row">
                    <div class="col-lg-6 ">
                        <h5 class="">Diagnosis Tests:</h5>
                        <ul style="margin-left:35px;">
                            @forelse (tests($prescriptions->tests) as $test)
                                <li style="list-style-type: inherit">{{ $test }}</li>
                            @empty
                                <li style="list-style-type: inherit">No Tests</li>
                            @endforelse
                        </ul>

                    </div>
                    <div class="col-lg-6">
                        <h5>Medicine:</h5>

                        <div class="row mt-2 mb-1  g-3 " style="">

                            @forelse ($prescriptions->medicine as $medicine)
                                <ul class="d-flex   " style="margin-left: 35px;">
                                    <li style="list-style-type: inherit" class="me-3 text-capitalize fst-italic">
                                        {{ $medicine->type }}</li>
                                    <li class="me-3 fw-semibold">{{ $medicine->drug? $medicine->drug->name : 'UNKNOWN' }}</li>
                                    <li class="me-3  fw-semibold">{{ $medicine->mg_ml }}</li>

                                </ul>
                                <ul class="d-flex justify-content-between mt-0 border-bottom">
                                    <li class=" me-3" style="margin-left: 35px;">{{ $medicine->dose }}</li>
                                    <li class="me-3">{{ $medicine->advice }}</li>
                                    <li class="me-3">{{ $medicine->duration }}</li>
                                </ul>
                            @empty
                            @endforelse


                        </div>


                        <div class=" mt-5">
                            <div class="row mt-2   g-3 ">
                                <h5>Advice:</h5>
                                <ul style="margin-left:35px;">
                                    @forelse (prescriptionAdvice($prescriptions->prescription_advice) as $advice)
                                        <li style="list-style-type: inherit">{{ $advice }}</li>
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

@endsection
@section('script')
    <script src="{{ asset('dashboard_assets/vendor/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('dashboard_assets/js/plugins-init/select2-init.js') }}"></script>

    <script>
        $(document).ready(function() {
            let printable = document.getElementById('printable').innerHTML;
            let print = document.getElementById('print');
            var originalContent = document.body.innerHTML;

            print.addEventListener('click', function() {


                document.body.innerHTML = printable;
                window.print();
                document.body.innerHTML = originalContent;

                // To re-run any JavaScript that was lost during the content replacement
                window.location.reload();
            });
        });
    </script>







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
