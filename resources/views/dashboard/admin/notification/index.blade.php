@extends('dashboard.admin.layouts.app')

@section('title') Notification @endsection

@section('content')
<div class="page-titles">
    <h4>Notifications</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('home')}}">Dashboard</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">Notifications</a></li>
    </ol>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Notifications</h4>
            </div>
            <div class="card-body">
               <div class="responsive">
                <table class="table table-srtiped">
                    <thead></thead>
                        <tr>
                            <th scope="col">Notification Type</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                        <tr>
                            <td>Weekly Notification for Blood Pressure  </td>
                            <td>
                                <a href="{{ route('adminNotification.weeklyBloodPressure') }}" class="btn btn-primary btn-xs">Send <i class="ms-2 fa fa-bell "></i> </a>
                            </td>
                        </tr>
                        <tr>
                            <td>Weekly Notification for Diabetis    </td>
                            <td>
                                <a href="#" class="btn btn-primary btn-xs">Send <i class="ms-2 fa fa-bell "></i> </a>
                            </td>
                        </tr>
                   </tbody>
                </table>
            {{-- {{ $doctors->links('pagination::bootstrap-5') }} --}}
               </div>
            </div>
        </div>
    </div>
</div>


@endSection

