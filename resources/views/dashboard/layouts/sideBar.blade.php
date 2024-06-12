
 @php
    $user = Auth::user();
    $doctor = $user->doctor;
    if ($doctor->degrees != null && $doctor->docHasSpec()->exists()) {
        $disablePrescriptions = false;
    }else{
        $disablePrescriptions = true;
    }

 @endphp

    <!--**********************************
            Sidebar start
        ***********************************-->
        <div class="deznav">
            <div class="deznav-scroll">
				<ul class="metismenu" id="menu">
                    <li><a href="{{route('home')}}" class="ai-icon" aria-expanded="false"> <i class="flaticon-381-networking"></i><span class="nav-text">Dashboard</span></a> </li>

                    <li class="{{ $disablePrescriptions ? 'disabled' : '' }}">
                        <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                            <i class="fa fa-prescription"></i>
                            <span class="nav-text">Prescriptions</span>
                        </a>
                        <ul aria-expanded="false">
                            @if ($disablePrescriptions)
                            <li><a href="javascript:void(0)" class="disabled-link">Create Prescription</a></li>
                            <li><a href="javascript:void(0)" class="disabled-link">All Prescriptions</a></li>
                        @else
                            <li><a href="{{route('prescription.create')}}">Create Prescription</a></li>
                            <li><a href="{{route('prescription.index')}}">All Prescriptions</a></li>
                        @endif
                        </ul>
                    </li>

                    {{-- <li><a href="{{route('drug.index')}}" class="ai-icon" aria-expanded="false"> <i class="fa fa-pills"></i><span class="nav-text">Drugs</span></a> </li>
                    <li><a href="{{route('specialist.index')}}" class="ai-icon" aria-expanded="false"> <i class="fa fa-syringe"></i><span class="nav-text">Speciality</span></a> </li> --}}




                </ul>

				<div class="copyright">
					<p><strong>Nirvar  Dashboard</strong> © {{ date('Y') }} All Rights Reserved</p>
					<p>Made with <span class="heart"></span> by Synex Digital</p>
				</div>
			</div>
        </div>
        <!--**********************************
            Sidebar end
        ***********************************-->
