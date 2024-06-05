@extends('dashboard.layouts.app')

@section('title') Create Prescription @endsection
@section('style')
<link rel="stylesheet" href="{{asset('dashboard_assets/vendor/select2/css/select2.min.css')}}">
{{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" /> --}}
@endsection

@section('content')
<div class="page-titles">
    <h4>Prescription</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('home')}}">Dashboard</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">Create Prescription</a></li>
    </ol>
</div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Patient Info</h4>
                </div>
                <div class="card-body">
                    <form action="">
                        <div class="mb-3">
                            <label class="form-label">Search Patient Number</label>
                            <select class="js-data-example-ajax w-100 dynamic-option-creation">

                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="" class="form-label">Name</label>
                            <input type="text" class="form-control form-control-sm" name="name" id="name" required>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
<script src="{{asset('dashboard_assets/vendor/select2/js/select2.full.min.js')}}"></script>
<script src="{{asset('dashboard_assets/js/plugins-init/select2-init.js')}}"></script>
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Include Select2 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script> --}}
<script>
    $(".js-data-example-ajax").select2({
        tags: true,
        width: "100%",
        ajax: {
            url: "{{ route('selectUser') }}",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;

                return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true
        },
        placeholder: 'Find or Create',
        escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
        minimumInputLength: 10,
        templateResult: formatRepo,
        templateSelection: formatRepoSelection
    });

    function formatRepo(repo) {
        if (repo.loading) {
            return repo.text;
        }

        var markup = "<div class='select2-result-repository clearfix'>" +
            "<div class='select2-result-repository__meta'>" +
            "<div class='select2-result-repository__title' id='select_result' value='" + repo.id + "'>" + repo.text + "</div>";

        markup += "</div></div>";

        return markup;
    }

    function formatRepoSelection(repo) {
        return repo.text || repo.phone_number;
    }
</script>
<script>
    $(".js-data-example-ajax").on('select2:select', function (e) {
            var data = e.params.data;

            // Show the selected value in an alert

            if(data.id.toString().startsWith("0")){
                // alert('Selected Value: ' + data.id);
                // $.ajax({
                //     type:"GET",
                //     url:"/get-designations/"+departmentId,
                //     success:function(res){
                //         if(res){
                //             $("#designation").empty();
                //             $.each(res,function(key,value){
                //                 $("#designation").append('<option value="'+value.id+'">'+value.designation+'</option>');
                //             });
                //         }else{
                //             $("#designation").empty();
                //         }
                //     }
                // });
            }else{
                // $("#designation").empty();
            }
        });
</script>
@endsection
