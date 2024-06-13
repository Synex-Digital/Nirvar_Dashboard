@extends('dashboard.admin.layouts.app')
@section('title') Speciality @endsection
@section('style')
<link href="{{asset('dashboard_assets/vendor/datatables/css/jquery.dataTables.min.css')}}" rel="stylesheet">
<link href="{{asset('dashboar_assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css')}}" rel="stylesheet">
    <style>
        .form-control-sm{
        height: 32px !important;
    }
    </style>
@endsection

@section('content')

<div class="page-titles">
    <h4>Speciality</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('home')}}">Dashboard</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">Speciality</a></li>
    </ol>
</div>




<div class="row">
    <div class="col-lg-8 m-auto">
        <button type="button" class="btn btn-primary btn-xs mb-2 " data-bs-toggle="modal" data-bs-target="#basicModal">Add Speciality</button>
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Speciality</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example2" class="display">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($speciality as $data )
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$data->name}}</td>
                                    <td class="d-flex justify-content-center">
                                        <button type="button" class="btn btn-primary btn-xs me-2 edit" data-value="{{$data->id}}"  data-bs-toggle="modal" data-bs-target="#editModal" > <i class="fa fa-edit "></i> </button>
                                        <form action="{{route('specialist.destroy',$data->id)}}" method="POST" ">
                                            @csrf
                                            @method('DELETE')
                                        <button class="btn btn-danger btn-xs"> <i class="fa fa-trash "></i> </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty

                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="basicModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Speciality</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <form action="{{route('specialist.store')}}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Speciality Name</label>
                        <input type="text" class="form-control form-control-sm" name="name" >
                    </div>
                </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger  btn-xs light" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary btn-xs">Create</button>
        </div>
    </form>
    </div>
    </div>
</div>
<!-- edit -->
<div class="modal fade" id="editModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Speciality</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <form id="editForm" action="{{route('specialist.update',0)}}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Speciality Name</label>
                        <input type="text" class="form-control form-control-sm" name="name" id="editSpc">
                    </div>
                </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger  btn-xs light" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary btn-xs">Update</button>
        </div>
    </form>
    </div>
    </div>
</div>



@endsection
@section('script')
          <!-- Datatable -->
    <script src="{{asset('dashboard_assets/vendor/datatables/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('dashboard_assets/js/plugins-init/datatables.init.js')}}"></script>

    <script>
          $(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $('body').on('click', '.edit', function () {
        var id = $(this).data('value');

    // Construct the route dynamically
        var route = "{{ route('specialist.edit', ['specialist' => ':id']) }}";
        route = route.replace(':id', id);
        $.get(route, function(data) {
            $('#editSpc').val(data.name);
        });
        var form = $('#editForm');
            var action = form.attr('action');
            // Replace the last part of the action attribute with the new id
            action = action.substring(0, action.lastIndexOf('/') + 1) + id;
            form.attr('action', action);
    })

    });
    </script>
@endsection
