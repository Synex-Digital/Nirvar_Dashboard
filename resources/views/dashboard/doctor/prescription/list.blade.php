@extends('dashboard.layouts.app')

@section('title') All Prescription @endsection

@section('content')
<div class="page-titles">
    <h4>Prescription</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('home')}}">Dashboard</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">All Prescription</a></li>
    </ol>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">All Prescriptions</h4>
            </div>
            <div class="card-body">
                <table class="table ">
                    <thead></thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Patient Name</th>
                            <th scope="col">Number</th>
                            <th scope="col">Date</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                   <tbody>
                    @forelse ($prescription as $data )
                        <tr>
                            <td> {{$loop->iteration}}</td>
                            <td> {{$data->patient->user? $data->patient->user->name : 'UNKNOWN'}}</td>
                            <td> {{$data->patient->user? $data->patient->user->number : 'UNKNOWN'}}</td>
                            <td> {{$data->created_at->format('d-M-y')}}</td>
                            <td>
                                <a href="{{route('prescription.show',$data->id)}}" class="btn btn-primary btn-xs"> <i class="fa fa-eye "></i> </a>
                            </td>
                        </tr>
                    @empty

                    @endforelse

                   </tbody>
                </table>
                {{ $prescription->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>


@endSection

