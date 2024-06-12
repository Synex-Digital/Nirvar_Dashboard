@extends('dashboard.admin.layouts.app')

@section('title') All Patients @endsection

@section('content')
<div class="page-titles">
    <h4>Patients</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('home')}}">Dashboard</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">All Patients</a></li>
    </ol>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">All Patients</h4>
            </div>
            <div class="card-body">
               <div class="responsive">
                <table class="table table-srtiped">
                    <thead></thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Patient Name</th>
                            <th scope="col">Number</th>
                            <th scope="col">Verified</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                   <tbody>
                    @forelse ($patients as $data )
                        <tr>
                            <td> {{$loop->iteration}}</td>
                            <td> {{$data->user->name}}</td>
                            <td> {{$data->user->number}}</td>
                            <td> {{$data->user->regiser_at? 'Yes':'No'}}</td>


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

