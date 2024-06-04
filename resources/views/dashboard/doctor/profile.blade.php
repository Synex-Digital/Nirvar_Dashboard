@extends('dashboard.layouts.app')
@section('title')Doctor Profile @endsection
@section('style')
<link href="{{ asset('dashboard_assets/vendor/lightgallery/css/lightgallery.min.css') }}" rel="stylesheet">
<link href="{{ asset('dashboard_assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endsection
@section('content')
<div class="page-titles">
    <h4>Profile</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('home')}}">Dashboard</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">Profile</a></li>
    </ol>
</div>


<div class="row">
    <div class="col-lg-12">
        <div class="profile card card-body px-3 pt-3 pb-0">
            <div class="profile-head">
                <div class="photo-content">
                    <div class="cover-photo"></div>
                </div>
                <div class="profile-info">
                    <div class="profile-photo">
                        <img src="https://ui-avatars.com/api/?name={{Auth::user()->name}}&background=random" class="img-fluid rounded-circle" alt="" style="height: 100px;
                        width: 120px;">
                        {{-- <img src="{{ asset('dashboard_assets/images/profile/profile.png') }}" class="img-fluid rounded-circle" alt=""> --}}

                    </div>
                    <div class="profile-details">
                        <div class="profile-name px-3 pt-2">
                            <h4 class="text-primary mb-0">{{ $doctor->user->name }}</h4>
                            <p> {{ $doctor->docHasSpec()->exists() ? $doctor->docHasSpec->speciality->name  : 'UNKNOWN' }} </p>
                        </div>
                        <div class="profile-email px-2 pt-2">
                            <h4 class="text-muted mb-0">{{ $doctor->user->email }}</h4>
                            <p>Email</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">

    <div class="col-xl-12">
        <div class="card h-auto">
            <div class="card-body">
                <div class="profile-tab">
                    <div class="custom-tab-1">
                        <ul class="nav nav-tabs">

                            <li class="nav-item"><a href="#about-me" data-bs-toggle="tab" id="aboutMe" class="nav-link active show">About Me</a>
                            </li>
                            <li class="nav-item"><a href="#profile-settings" data-bs-toggle="tab" id="setting" class="nav-link">Setting</a>
                            </li>
                        </ul>
                        <div class="tab-content">

                            <div id="about-me" class="tab-pane fade active show">
                                <div class="profile-about-me">
                                    <div class="pt-4 border-bottom-1 pb-3">
                                        <h4 class="text-primary">About Me</h4>
                                        <p class="mb-2"> {{ $doctor->description ? $doctor->description : 'NO DATA FOUND!' }} </p>

                                    </div>
                                </div>
                                <div class="profile-skills mb-4">
                                    <h4 class="text-primary mb-2">Degrees</h4>

                                    <p class="mb-2" >{{ $doctor->degrees ?  $doctor->degrees :'NO DATA FOUND!' }}</p>

                                </div>
                                <div class="profile-skills mb-5">
                                    <h4 class="text-primary mb-2">Speciality</h4>

                                    <a href="javascript:void()" class="btn btn-primary light btn-xs mb-1">{{ $doctor->docHasSpec ? $doctor->docHasSpec->speciality->name : 'UNKNOWN' }}</a>

                                </div>

                                <div class="profile-personal-info">
                                    <h4 class="text-primary mb-4">Personal Information</h4>
                                    <div class="row mb-2">
                                        <div class="col-3">
                                            <h5 class="f-w-500">Name <span class="pull-right">:</span>
                                            </h5>
                                        </div>
                                        <div class="col-9"><span>{{ $doctor->user->name }}</span>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-3">
                                            <h5 class="f-w-500">Email <span class="pull-right">:</span>
                                            </h5>
                                        </div>
                                        <div class="col-9"><span>{{ $doctor->user->email }}</span>
                                        </div>
                                    </div>
                                    {{-- <div class="row mb-2">
                                        <div class="col-3">
                                            <h5 class="f-w-500">Availability <span class="pull-right">:</span></h5>
                                        </div>
                                        <div class="col-9"><span>Full Time (Free Lancer)</span>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-3">
                                            <h5 class="f-w-500">Age <span class="pull-right">:</span>
                                            </h5>
                                        </div>
                                        <div class="col-9"><span>27</span>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-3">
                                            <h5 class="f-w-500">Location <span class="pull-right">:</span></h5>
                                        </div>
                                        <div class="col-9"><span>Rosemont Avenue Melbourne,
                                                Florida</span>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-3">
                                            <h5 class="f-w-500">Year Experience <span class="pull-right">:</span></h5>
                                        </div>
                                        <div class="col-9"><span>07 Year Experiences</span>
                                        </div>
                                    </div> --}}
                                </div>
                            </div>
                            <div id="profile-settings" class="tab-pane fade">
                                <div class="pt-3">
                                    <div class="settings-form">
                                        <h4 class="text-primary">Account Setting</h4>
                                        <form>
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label>Name</label>
                                                    <input type="text" name="name" placeholder="Name" class="form-control" value="{{ $doctor->user->name }}" required>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label>Email</label>
                                                    <input type="email" name="email" placeholder="Email" class="form-control" value="{{ $doctor->user->email }}" required>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-8 {{ $doctor->degrees ? '' : 'input-danger' }}">
                                                    <label>Degrees</label>
                                                    <input type="text" class="form-control" placeholder="Degrees" value="{{ $doctor->degrees }}" {{ $doctor->degrees? 'required' : '' }}>
                                                </div>
                                                <div class="form-group col-md-4 ">
                                                    <label>Speciality</label>
                                                    <select class="form-control default-select {{ $doctor->docHasSpec ? '' : 'border border-danger' }} "  >
                                                        <option selected="">Choose...</option>
                                                        @forelse ($specialities as $speciality)
                                                            <option
                                                            {{ $doctor->docHasSpec()->exists() ? ($doctor->docHasSpec->specialist_id == $speciality->id ? 'selected' : '') : '' }}

                                                            value="{{ $speciality->id }}">{{ $speciality->name }}</option>
                                                        @empty
                                                            <option value="">No Speciality Found</option>
                                                        @endforelse
                                                    </select>
                                                </div>

                                            </div>
                                            <div class="row">
                                                 <div class="form-group col-md-4">
                                                    <label>Old Password</label>
                                                    <input type="text" name="oldPassword" placeholder="Old Password" class="form-control">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>Password</label>
                                                    <input type="password" name="password" placeholder="Password" class="form-control">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label> Confirm Password</label>
                                                    <input type="password" name=" confirm_password" placeholder=" Confirm Password" class="form-control">
                                                </div>
                                            </div>


                                            <div class="form-group">
                                                <div class="form-check custom-checkbox">
                                                    <input type="checkbox" class="form-check-input" id="gridCheck">
                                                    <label class="form-check-label" for="gridCheck"> Check me out</label>
                                                </div>
                                            </div>
                                            <button class="btn btn-primary" type="submit">Sign
                                                in</button>
                                        </form>
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



@endsection
@section('script')
<script src="{{ asset('dashboard_assets/vendor/lightgallery/js/lightgallery-all.min.js') }}"></script>
<script src="{{ asset('dashboard_assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>

<script>
    $('#lightgallery').lightGallery({
        thumbnail:true,
    });
</script>

@if ($error == 'true')
<script>
    $(document).ready(function() {
        $('#about-me').removeClass('active show');
        $('#profile-settings').addClass('active show');
        $('#aboutMe').removeClass('active show');
        $('#setting').addClass('active show');


    })
</script>
@endif
@endsection
