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
        <li class="breadcrumb-item active"><a href="javascript:void(0)">Create Prescription</a></li>
    </ol>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-center align-items-center">
        <img src="{{asset('dashboard_assets/images/logoN.png')}}" width="65" class="mx-2"  alt="">
        <img src="{{asset('dashboard_assets/images/logotextN.png')}}" width="160" class="" alt="" style="margin-top:15px;">
    </div>
    <div class="card-body">
        <form action="{{route('prescription.store')}}" method="POST">
            @csrf
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <h5>Doctor: <span class="text-muted fw-light fst-italic"> {{ $doctor->user->name }}</span></h5>
                <h5>Degrees: <span class="text-muted fw-light fst-italic">  {{ $doctor->degrees }}</span></h5>
                <h5>Specialization:<span class="text-muted fw-light fst-italic">  {{ $doctor->docHasSpec->speciality->name }}</span></h5>
                <h5>Doctor Registration No: <span class="text-muted fw-light fst-italic"> {{ $doctor->registration }}</span></h5>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 ">
                <div class="float-end flt">

                    <h5>Date: <span class=" text-muted fw-light fst-italic"> {{ Carbon\Carbon::now()->format('d-m-Y')    }}</span></h5>
                    <h5>Ref: <span class="text-muted fw-light fst-italic"> </span></h5>
                </div>
            </div>
        </div>
        <div class="row border-top border-bottom   mt-3 mb-3  ">
           <div class="col-lg-3">
                <p class="mb-0">Patient Number <span class="text-danger">*</span> </p>
         
            <select name="phone_number" id="phone_number" style="width: 100%">
                <option value="">Select a phone number</option>
            </select>

           </div>
           <div class="col-lg-3">
                <p class="mb-0">Patient Name <span class="text-danger">*</span> </p>
                <input type="text" class="form-control form-control-sm" name="name" id="name"  placeholder="Name" value="{{ old('name') }}">
           </div>

           <div class="col-lg-2">
                <p class="mb-0">Gender <span class="text-danger">*</span></p>
                <select name="gender" id="" class="form-control form-control-sm" >
                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Others</option>
                </select>
           </div>
           <div class="col-lg-2">
                <p class="mb-0"> Age <span class="text-danger">*</span></p>
                <input type="number" min="0" class="form-control form-control-sm" name="age" id="age" value="{{ old('age') }}" >
            </div>
           <div class="col-lg-2">
                <p class="mb-0">Weight</p>

                <input type="number" min="0" class="form-control form-control-sm" name="weight" id="weight" placeholder="kg" value="{{ old('weight') }}">
            </div>
            <div class="col-lg-2 ">
                <p class="mb-0">Blood Group</p>
                <select name="blood_group" id="" class="  form-control form-control-sm">
                    <option value="" {{ old('blood_group') == '' ? 'selected' : '' }}>None</option>
                    <option value="A+" {{ old('blood_group') == 'A+' ? 'selected' : '' }}>A+</option>
                    <option value="A-" {{ old('blood_group') == 'A-' ? 'selected' : '' }}>A-</option>
                    <option value="B+" {{ old('blood_group') == 'B+' ? 'selected' : '' }}>B+</option>
                    <option value="B-" {{ old('blood_group') == 'B-' ? 'selected' : '' }}>B-</option>
                    <option value="AB+" {{ old('blood_group') == 'AB+' ? 'selected' : '' }}>AB+</option>
                    <option value="AB-" {{ old('blood_group') == 'AB-' ? 'selected' : '' }}>AB-</option>
                    <option value="O+" {{ old('blood_group') == 'O+' ? 'selected' : '' }}>O+</option>
                    <option value="O-" {{ old('blood_group') == 'O-' ? 'selected' : '' }}>O-</option>

                </select>
            </div>
           <div class="col-lg-2 mb-3">
                <p class="mb-0">Height</p>
                <div class="row">
                    <div class="col-lg-6  mb-2">
                        <select name="heightFt" id=" heightFt" class="form-control form-control-sm" >
                            <option value="" {{ old('heightFt') == '' ? 'selected' : '' }}>None</option>
                            <option value="2 FT" {{ old('heightFt') == '2 FT' ? 'selected' : '' }}>2 FT</option>
                            <option value="3 FT" {{ old('heightFt') == '3 FT' ? 'selected' : '' }}>3 FT</option>
                            <option value="4 FT" {{ old('heightFt') == '4 FT' ? 'selected' : '' }}>4 FT</option>
                            <option value="5 FT" {{ old('heightFt') == '5 FT' ? 'selected' : '' }}>5 FT</option>
                            <option value="6 FT" {{ old('heightFt') == '6 FT' ? 'selected' : '' }}>6 FT</option>
                            <option value="7 FT" {{ old('heightFt') == '7 FT' ? 'selected' : '' }}>7 FT</option>
                            <option value="8 FT" {{ old('heightFt') == '8 FT' ? 'selected' : '' }}>8 FT</option>
                        </select>

                    </div>
                    <div class="col-lg-6 ">
                        <select name="heightIn" id =" heightIn" class="form-control form-control-sm" >
                            <option value="" {{ old('heightIn') == '' ? 'selected' : '' }}>None</option>
                            <option value="0 IN" {{ old('heightIn') == '0 IN' ? 'selected' : '' }}>0 IN</option>
                            <option value="1 IN" {{ old('heightIn') == '1 IN' ? 'selected' : '' }}>1 IN</option>
                            <option value="2 IN" {{ old('heightIn') == '2 IN' ? 'selected' : '' }}>2 IN</option>
                            <option value="3 IN" {{ old('heightIn') == '3 IN' ? 'selected' : '' }}>3 IN</option>
                            <option value="4 IN" {{ old('heightIn') == '4 IN' ? 'selected' : '' }}>4 IN</option>
                            <option value="5 IN" {{ old('heightIn') == '5 IN' ? 'selected' : '' }}>5 IN</option>
                            <option value="6 IN" {{ old('heightIn') == '6 IN' ? 'selected' : '' }}>6 IN</option>
                            <option value="7 IN" {{ old('heightIn') == '7 IN' ? 'selected' : '' }}>7 IN</option>
                            <option value="8 IN" {{ old('heightIn') == '8 IN' ? 'selected' : '' }}>8 IN</option>
                            <option value="9 IN" {{ old('heightIn') == '9 IN' ? 'selected' : '' }}>9 IN</option>
                            <option value="10 IN" {{ old('heightIn') == '10 IN' ? 'selected' : '' }}>10 IN</option>
                            <option value="11 IN" {{ old('heightIn') == '11 IN' ? 'selected' : '' }}>11 IN</option>


                        </select>

                    </div>
                </div>
            </div>

        </div>
        <div class=" mt-3">
            <div class="form-group row">
                <h5 class="col-lg-2 col-sm-12 col-md-12 mt-2">Cheif Complaint: <span class="text-danger">*</span></h5>
                <div class="col-lg-10 col-md-12 col-sm-12">
                   <input type="text" class="form-control form-control-sm" name="complaint" id="complaint" placeholder="Cold, Fever, Flu">
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 ">
                    <h5 class="">Investigations:</h5>
                    <div class="row mt-2 mb-1  g-3 invest">
                        <div class="col-12 my-1 ">
                            <input type="text" name="test[]" class="form-control form-control-sm bg-white input-default  inp " placeholder="Test Name">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary btn-xs float-end" id="plus">Add More</button>
                </div>
                <div class="col-lg-6">
                    <h5>Medicine: <span class="text-danger">*</span></h5>

                        <div class="row mt-2 mb-1  g-3 medi" style="">
                            <div class="col-4 my-1 ">
                                <input type="text" name="type[]" class="form-control form-control-sm bg-white input-default  inp " placeholder="Type">
                            </div>
                            <div class="col-4  my-1   ">
                                <select name="drug[]" class=" form-control form-control-sm">
                                    <option value="">Select Drug</option>
                                    @foreach ($drug as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach

                                </select>
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

                        <div class="d-flex justify-content-end align-items-center mb-2">
                            <button type="button" class="btn btn-danger btn-xs me-2" id="removeMedi" style="display: none;">Remove </button>
                            <button type="button" class="btn btn-primary btn-xs" id="plusMedi">Add Medicine</button>
                        </div>
                    <div class=" mt-5">
                    <div class="row mt-2   g-3 advice">
                            <div class="col-12 my-1 ">
                                <input type="text" name="advice[]" class="form-control form-control-sm bg-white input-default  inp " placeholder="Advice">
                            </div>

                        </div>
                    </div>

                    <button type="button" class="btn btn-primary btn-xs float-end" id="plusAdvice">Add More</button>
                </div>
            </div>
        </div>
        <div class="mt-5 text-center">
            <button type="submit" class="btn btn-primary btn-sm " id="create" >Create</button>
        </div>
    </form>
    </div>
</div>

@endsection
@section('script')

<script src="{{asset('dashboard_assets/vendor/select2/js/select2.full.min.js')}}"></script>
<script src="{{asset('dashboard_assets/js/plugins-init/select2-init.js')}}"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#phone_number').select2({
            placeholder: 'Find or Insert',
            ajax: {
                url: '{{ route("selectUser") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page || 1
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;

                    return {
                        results: data.items.map(function (item) {
                            return {
                                id: item.text, // Use phone number as the id
                                text: item.text
                            };
                        }),
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            minimumInputLength: 10,
            templateResult: formatUser,
            templateSelection: formatUserSelection,
            tags: true
        });

        function formatUser(user) {
            if (user.loading) {
                return user.text;
            }
            return user.text;
        }

        function formatUserSelection(user) {
            return user.text || user.id;
        }

        $('#phone_number').on('change', function() {
            var selectedNumber = $(this).val();

            // Validation: Number should start with '01' and be exactly 11 digits long
            if (!/^01\d{9}$/.test(selectedNumber)) {
                alert('Invalid phone number. It should start with "01" and be exactly 11 digits long.');
                // $(this).val('').trigger('change');
                $('#create').prop('disabled', true);
            }else{
                $('#create').prop('disabled', false);
                  $.ajax({
                    type:"GET",
                    url:"/get/patient/"+selectedNumber,
                    success:function(res){
                        if(res){
                            let user = res.user;
                            let patient = res.patient;
                            $('#name').val(user.name);
                            $('#gender').val(patient.gender);
                            $('select[name="gender"]').val(patient.gender.toLowerCase());
                           // Parse the birth date and created_at date
                            let birthDate = new Date(patient.date_of_birth.replace(' ', 'T'));
                            let createdAtDate = new Date(patient.created_at.replace(' ', 'T'));
                            if (isNaN(birthDate) || isNaN(createdAtDate)) {
                                alert('Invalid date format');
                                return;
                            }
                            // Calculate age from birth date and created_at date
                            let age = createdAtDate.getFullYear() - birthDate.getFullYear();
                            let monthDiff = createdAtDate.getMonth() - birthDate.getMonth();
                            if (monthDiff < 0 || (monthDiff === 0 && createdAtDate.getDate() < birthDate.getDate())) {age--; }
                            $('#age').val(age);
                            let weightHeight = patient.weight_height.split(',');
                            let weight = weightHeight[0].trim();
                            let height = weightHeight[1].trim().split('.');
                            $('#weight').val(weight);
                            $('select[name="heightFt"]').val(height[0]);
                            $('select[name="heightIn"]').val(height[1]);

                            $('select[name="blood_group"]').val(patient.blood_group);
                        }else{
                            alert('not found');
                        }
                    }
                })
            }
        });
        // Add more
        $('#plusAdvice').click(function () {
        let inputNew = $('.advice:last').clone(true);
        inputNew.find('input').val(''); // Clear the value of the cloned input fields
            $(inputNew).insertAfter('.advice:last');
        });
        $('#plus').click(function () {
            let inputNew = $('.invest:last').clone(true);
            inputNew.find('input').val(''); // Clear the value of the cloned input fields
            $(inputNew).insertAfter('.invest:last');
        });
        //add
        $('#plusMedi').click(function () {
            let inputNew = $('.medi:last').clone(true);
            inputNew.find('input').val(''); // Clear the value of the cloned input fields

            // Check if the "Remove" button already exists
            if ($('#removeMedi').length === 0) {
                // Create and append the "Remove" button beside the "Add Medicine" button
                let removeButton = $('<button>').text('Remove').addClass('remove-button btn btn-danger btn-xs ms-2 me-2').click(function() {
                    $(inputNew).remove(); // Remove the cloned div
                    if ($('.medi').length <= 1) {
                        $('#removeMedi').hide(); // Hide the "Remove Medicine" button if there's only one "medi" div left
                    }
                });
                $(this).after(removeButton);
            }

            // Show the "Remove Medicine" button if it's hidden
            $('#removeMedi').show();

            // Insert the cloned div after the last "medi" div
            inputNew.insertAfter('.medi:last');
        });

        // Add click event handler for the "Remove Medicine" button
        $('#removeMedi').click(function () {
            $('.medi:last').remove(); // Remove the last cloned div
            if ($('.medi').length <= 1) {
                $('#removeMedi').hide(); // Hide the "Remove Medicine" button if there's only one "medi" div left
            }
        });
        //

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

