<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $folder->name }} - Folder Access</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <style>
        .test-preport, .prescription{
            display: none;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6  mt-5 m-auto">
                <div class="card ">
                    <div class="card-header ">
                        <a href="">
                            <img width="245" class="mx-auto d-block" height="50" src="{{ asset('dashboard_assets/images/logowithtextN.png') }}" alt="">
                        </a>
                        <h3 class="card-title mt-3"> Files in {{ $folder->name }}</h3>
                    </div>
                    <div class="card-body">
                        <h1  class="btn btn-secondary me-2 prBtn">Prescription</h1>
                        <h1  class="btn btn-light trBtn">Test Report</h1>

                        <ul class="list-group prescription active">
                            @foreach ($folder->files as $file)
                                <li class="list-group-item list-group-item-action list-group-item-light">
                                    {{ $loop->iteration.'. ' }}
                                    {{ $file->name }}
                                    <a href="" class="btn btn-primary btn-sm float-end"> Download</a>
                                    <a href="" class="btn btn-light btn-sm float-end me-2">View</a>
                                </li>
                            @endforeach
                        </ul>
                        <ul class="list-group test-preport">
                            @foreach ($folder->files as $file)
                                <li class="list-group-item list-group-item-action list-group-item-light">
                                    {{ $loop->iteration.'. ' }}
                                    {{ $file->name }}
                                    <a href="" class="btn btn-primary btn-sm float-end"> Download</a>
                                    <a href="" class="btn btn-light btn-sm float-end me-2">View</a>
                                </li>
                            @endforeach
                        </ul>

                    </div>
                </div>

            </div>

        </div>

    </div>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script>
    $(document).ready(function() {
        $('.prBtn').click(function() {
            if($(this).hasClass('btn-light')){
                $(this).removeClass('btn-light');
                $(this).addClass('btn-secondary');
                $('.trBtn').removeClass('btn-secondary');
                $('.trBtn').addClass('btn-light');
            }
        });
        $('.trBtn').click(function() {
            if($(this).hasClass('btn-light')){
                $(this).removeClass('btn-light');
                $(this).addClass('btn-secondary');
                $('.prBtn').removeClass('btn-secondary');
                $('.prBtn').addClass('btn-light');

            }
        });

    });
</script>
</body>
</html>
