<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $folder->name }} - Folder Access</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">

    <style>
        .test-report, .prescription{
            display: none;
        }
        .active {
            display: block;
        }
        .masonry {
            column-count: 3;
            column-gap: 1rem;
        }

        .item {
            break-inside: avoid;
        }
        .icon{
            display: none;
            position: absolute;
            margin-top: -39px;
        }

        .item:hover .icon{
            display: flex;
        }
       .icon a {
    display: inline-block;
    margin: 0 10px;
    color: #fff; /* White color for the icons */
    font-size: 11px; /* Icon size */
    background: rgba(0, 0, 0, 0.5); /* Semi-transparent black background */
    padding: 7px;
    border-radius: 50%;
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
                        <div class="prescription active">
                            <div class="container">
                                <div class="masonry">
                                    @foreach ($folder->prescription_files as $file)
                                        <div class="item mb-4    ">
                                            @php
                                                $tempUrl = URL::temporarySignedRoute('image.show', now()->addMinutes(10), ['fileId' => $file->id]);
                                            @endphp
                                            <img src="{{ $tempUrl }}" class="img-fluid rounded shadow-sm " alt="Prescription Image">
                                            <div class="icon">

                                                <a href="{{ $tempUrl }}" class="view-icon " title="View"><i class="fas fa-eye"></i></a>
                                                <a href="{{ $tempUrl }}" download class="download-icon " title="Download"><i class="fas fa-download"></i></a>
                                            </div>
                                        </div>

                                    @endforeach
                                </div>
                            </div>


                        </div>
                        <div class="test-report">
                            <div class="container">
                                <div class="masonry">
                                    @foreach ($folder->test_report_files as $file)
                                        <div class="item mb-4">
                                            @php
                                                $tempUrl = URL::temporarySignedRoute('image.show', now()->addMinutes(10), ['fileId' => $file->id]);
                                            @endphp
                                            <img src="{{ $tempUrl }}" class="img-fluid rounded shadow-sm" alt="Test Report Image">
                                            <div class="icon">
                                                <a href="{{ $tempUrl }}" class="view-icon " title="View"><i class="fas fa-eye"></i></a>
                                                <a href="{{ $tempUrl }}" download class="download-icon " title="Download"><i class="fas fa-download"></i></a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>



            </div>
            <div class="row">
                <div class="col-lg-6">

                </div>
            </div>

        </div>

    </div>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
<!-- or -->
<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const gridContainer = document.querySelector('.grid-container');
        const gridItems = gridContainer.querySelectorAll('.grid-item');

        if (gridItems.length >= 4) {
            // Insert the fourth item after the second item in the DOM
            gridItems[1].after(gridItems[3]);
        }
    });
    </script>
<script>
    $(document).ready(function() {
        $('.prBtn').click(function() {
            if($(this).hasClass('btn-light')){
                $(this).removeClass('btn-light');
                $(this).addClass('btn-secondary');
                $('.trBtn').removeClass('btn-secondary');
                $('.trBtn').addClass('btn-light');
            }

            $('.prescription').addClass('active');
            $('.test-report').removeClass('active');


        });
        $('.trBtn').click(function() {
            if($(this).hasClass('btn-light')){
                $(this).removeClass('btn-light');
                $(this).addClass('btn-secondary');
                $('.prBtn').removeClass('btn-secondary');
                $('.prBtn').addClass('btn-light');
            }
            $('.test-report').addClass('active');
            $('.prescription').removeClass('active');


        });


        $('.test-report').click(function() {
            if($(this).hasClass('active')){
                $(this).addClass('active');
                $('.prescription').removeClass('active');
            }
        });

    });
</script>
</body>
</html>
