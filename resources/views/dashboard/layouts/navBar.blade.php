{{-- @php
    $user = Auth::user();
    $doctor = $user->doctor;
    $count = 0;
    $doctor->degrees == null ? $count ++ : '';
    // // $doctor->description == null ? $count++ : '';
    $doctor->docHasSpec == null ? $count++ : '';

    if ($doctor->degrees == null || !$doctor->docHasSpec()->exists()	 ) {
        $count++;
    }
@endphp --}}

  <!--**********************************
            Nav header start
        ***********************************-->
        <div class="nav-header">
            <a href="{{ route('admin.index') }}" class="brand-logo">
                <img class="logo-abbr" src="{{ asset('dashboard_assets/images/logoN.png') }}" alt="">
                <img class="logo-compact" src="{{ asset('dashboard_assets/images/logotextN.png') }}" alt="">
                <img class="brand-title" src="{{ asset('dashboard_assets/images/logotextN.png') }}" alt="">
            </a>

            <div class="nav-control">
                <div id="hamburger" class= "hamburger ">
                    <span class="line"></span><span class="line"></span><span class="line"></span>
                </div>
            </div>
        </div>
        <!--**********************************
            Nav header end
        ***********************************-->

		<!--**********************************
            Chat box start
        ***********************************-->
		<!--**********************************
            Chat box End
        ***********************************-->

		<!--**********************************
            Header start
        ***********************************-->
        <div class="header">
            <div class="header-content">
                <nav class="navbar navbar-expand">
                    <div class="collapse navbar-collapse justify-content-between">
                        <div class="header-left">
                            <div class="dashboard_bar">
								{{-- <div class="input-group search-area d-lg-inline-flex d-none">
									<input type="text" class="form-control" placeholder="Search here...">
									<div class="input-group-append">
										<span class="input-group-text"><a href="javascript:void(0)"><i class="flaticon-381-search-2"></i></a></span>
									</div>
								</div> --}}
                            </div>
                        </div>
                        <ul class="navbar-nav header-right">
							{{-- <li class="nav-item dropdown notification_dropdown">
                                <a class="nav-link  ai-icon" href="javascript:void(0)" role="button" data-bs-toggle="dropdown">
                                    <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M22.75 15.8385V13.0463C22.7471 10.8855 21.9385 8.80353 20.4821 7.20735C19.0258 5.61116 17.0264 4.61555 14.875 4.41516V2.625C14.875 2.39294 14.7828 2.17038 14.6187 2.00628C14.4546 1.84219 14.2321 1.75 14 1.75C13.7679 1.75 13.5454 1.84219 13.3813 2.00628C13.2172 2.17038 13.125 2.39294 13.125 2.625V4.41534C10.9736 4.61572 8.97429 5.61131 7.51794 7.20746C6.06159 8.80361 5.25291 10.8855 5.25 13.0463V15.8383C4.26257 16.0412 3.37529 16.5784 2.73774 17.3593C2.10019 18.1401 1.75134 19.1169 1.75 20.125C1.75076 20.821 2.02757 21.4882 2.51969 21.9803C3.01181 22.4724 3.67904 22.7492 4.375 22.75H9.71346C9.91521 23.738 10.452 24.6259 11.2331 25.2636C12.0142 25.9013 12.9916 26.2497 14 26.2497C15.0084 26.2497 15.9858 25.9013 16.7669 25.2636C17.548 24.6259 18.0848 23.738 18.2865 22.75H23.625C24.321 22.7492 24.9882 22.4724 25.4803 21.9803C25.9724 21.4882 26.2492 20.821 26.25 20.125C26.2486 19.117 25.8998 18.1402 25.2622 17.3594C24.6247 16.5786 23.7374 16.0414 22.75 15.8385ZM7 13.0463C7.00232 11.2113 7.73226 9.45223 9.02974 8.15474C10.3272 6.85726 12.0863 6.12732 13.9212 6.125H14.0788C15.9137 6.12732 17.6728 6.85726 18.9703 8.15474C20.2677 9.45223 20.9977 11.2113 21 13.0463V15.75H7V13.0463ZM14 24.5C13.4589 24.4983 12.9316 24.3292 12.4905 24.0159C12.0493 23.7026 11.716 23.2604 11.5363 22.75H16.4637C16.284 23.2604 15.9507 23.7026 15.5095 24.0159C15.0684 24.3292 14.5411 24.4983 14 24.5ZM23.625 21H4.375C4.14298 20.9999 3.9205 20.9076 3.75644 20.7436C3.59237 20.5795 3.50014 20.357 3.5 20.125C3.50076 19.429 3.77757 18.7618 4.26969 18.2697C4.76181 17.7776 5.42904 17.5008 6.125 17.5H21.875C22.571 17.5008 23.2382 17.7776 23.7303 18.2697C24.2224 18.7618 24.4992 19.429 24.5 20.125C24.4999 20.357 24.4076 20.5795 24.2436 20.7436C24.0795 20.9076 23.857 20.9999 23.625 21Z" fill="#007A64"/>
									</svg>
									<span class="badge light text-white bg-primary">{{ $count }}</span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <div id="DZ_W_Notification1" class="widget-media dz-scroll p-3 height380">
										<ul class="timeline">

                                            @if ($doctor->degrees == null)
											<li>
													<a href="{{ route('doctorProfile.error' ) }}">
                                                    <div class="timeline-panel">
                                                        <div class="media me-2 media-danger">
                                                            <i class="fa fa-exclamation"></i>
                                                        </div>
                                                        <div class="media-body">
                                                            <h6 class="mb-1">Fill Up Your Degrees </h6>
                                                            <small class="d-block">{{ $doctor->created_at->diffForHumans() }}</small>
                                                        </div>
                                                    </div>
												</a>
                                                </li>
                                            @endif

											@if (empty($doctor->docHasSpec) )
                                            <li>
                                                <a href="{{ route('doctorProfile.error' ) }}">
												<div class="timeline-panel">
                                                    <div class="media me-2 media-danger">
                                                        <i class="fa fa-exclamation"></i>
                                                    </div>
													<div class="media-body">
														<h6 class="mb-1">Fill Up Your Speciality</h6>
														<small class="d-block">{{ $doctor->created_at->diffForHumans() }}</small>
													</div>
												</div>
                                                </a>
											</li>
                                            @endif
                                            @if  ($doctor->degrees == null || !$doctor->docHasSpec()->exists()	 )

                                                <li>
                                                    <a href="{{ route('doctorProfile.error' ) }}">
                                                    <div class="timeline-panel">
                                                        <div class="media me-2 media-danger">
                                                            <i class="fa fa-exclamation"></i>
                                                        </div>
                                                        <div class="media-body">
                                                            <h6 class="mb-1">Please fill up your degress and speciality in order to create prescription</h6>
                                                            <small class="d-block">{{ $doctor->created_at->diffForHumans() }}</small>
                                                        </div>
                                                    </div>
                                                    </a>
                                                </li>
                                            @endif
                                            @if  ($count == 0)
                                                <li>
                                                    <a href="{{ route('doctorProfile.error' ) }}">
                                                    <div class="timeline-panel">
                                                        <div class="media me-2 media-success">
                                                            <i class="fa fa-check"></i>
                                                        </div>
                                                        <div class="media-body">
                                                            <h6 class="mb-1"> No New Notifications </h6>

                                                        </div>
                                                    </div>
                                                    </a>
                                                </li>
                                            @endif

										</ul>
									</div>

                                </div>
                            </li> --}}

                            <li class="nav-item dropdown header-profile">
                                <a class="nav-link" href="javascript:void(0)" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user-md" style="font-size: 25px;"></i>
                                    {{-- <img src="https://ui-avatars.com/api/?name={{Auth::user()->name}}&background=random" width="20" alt=""> --}}
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="" class="dropdown-item ai-icon">
                                        <svg id="icon-user1" xmlns="http://www.w3.org/2000/svg" class="text-primary" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                        <span class="ms-2">Profile </span>
                                    </a>


                                    <a class="dropdown-item ai-icon" href=""
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                       <svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                                       <span class="ms-2">Logout </span>
                                    </a>

                                    <form id="logout-form" action="{{ route('adminLogout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
        <!--**********************************
            Header end ti-comment-alt
        ***********************************-->
