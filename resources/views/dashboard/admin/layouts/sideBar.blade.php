{{--
 @php
    $user = Auth::user();
    $doctor = $user->doctor;
    if ($doctor->degrees != null && $doctor->docHasSpec()->exists()) {
        $disablePrescriptions = false;
    }else{
        $disablePrescriptions = true;
    }

 @endphp --}}

    <!--**********************************
            Sidebar start
        ***********************************-->
        <div class="deznav"  >
            <div class="deznav-scroll">
				<ul class="metismenu" id="menu">
                    <li><a href="{{route('admin.index')}}" class="ai-icon" aria-expanded="false"> <i class="flaticon-381-networking"></i><span class="nav-text">Dashboard</span></a> </li>


                    <li><a href="{{ route('adminDoctor') }}" class="ai-icon" aria-expanded="false"> <i class="fa fa-user-md"></i></i><span class="nav-text">Doctors</span></a> </li>

                    <li><a href="{{ route('adminPatient') }}" class="ai-icon" aria-expanded="false"> <i class="fa fa-user"></i></i><span class="nav-text">Patients</span></a> </li>

                    <li><a href="{{ route('adminPrescriptionShow') }}" class="ai-icon" aria-expanded="false"> <i class="fa fa-prescription"></i></i><span class="nav-text">Prescriptions</span></a> </li>
                    <li><a href="{{route('drug.index')}}" class="ai-icon" aria-expanded="false"> <i class="fa fa-pills"></i><span class="nav-text">Drugs</span></a> </li>
                    <li><a href="{{route('specialist.index')}}" class="ai-icon" aria-expanded="false"> <i class="fa fa-syringe"></i><span class="nav-text">Speciality</span></a> </li>
                    <li><a href="{{route('adminNotification')}}" class="ai-icon" aria-expanded="false"> <i class="fa fa-bell"></i><span class="nav-text">Notification</span></a> </li>




                </ul>

				<div class="copyright" style="margin-top:80px !important; margin-bottom:0px !important; ">
					<p><strong>Nirvar Admin Dashboard</strong> © {{ date('Y') }} All Rights Reserved</p>
					<p>Made with <span class="heart"></span> by Synex Digital</p>
				</div>
			</div>
        </div>
        <!--**********************************
            Sidebar end
        ***********************************-->
