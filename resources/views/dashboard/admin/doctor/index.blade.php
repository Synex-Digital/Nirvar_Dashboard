@extends('dashboard.admin.layouts.app')

@section('title') All Prescription @endsection

@section('content')
<div class="page-titles">
    <h4>Doctors</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('home')}}">Dashboard</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">All Doctors</a></li>
    </ol>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">All Doctors</h4>
            </div>
            <div class="card-body">
               <div class="responsive">
                <table class="table table-srtiped">
                    <thead></thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Doctor Name</th>
                            <th scope="col">Number</th>
                            <th scope="col">Speciality</th>
                            <th scope="col">Registration ID</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                   <tbody>
                    @forelse ($doctors as $data )
                        <tr>
                            <td> {{$loop->iteration}}</td>
                            <td> {{$data->user->name}}</td>
                            <td> {{$data->user->number}}</td>
                            <td> {{$data->docHasSpec? $data->docHasSpec->speciality->name: 'UNKNOWN'}}</td>
                            <td> {{$data->registration}}</td>

                            <td>
                                <a href="#" class="btn btn-primary btn-xs"> <i class="fa fa-eye "></i> </a>
                            </td>
                        </tr>
                    @empty

                    @endforelse

                   </tbody>
                </table>
            {{-- {{ $doctors->links('pagination::bootstrap-5') }} --}}
               </div>
            </div>
        </div>
    </div>
</div>


@endSection

