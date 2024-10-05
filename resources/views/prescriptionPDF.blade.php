<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Medical Report</title>
<style>
    body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 20px;
    background: #fff;
    color: #333;
}

.container {
    width: 100%;
    margin: 0 auto;
    /* border: 2px solid #ccc; */

}

.header {
    margin-bottom: 40px;


}

.title {
    font-size: 24px;
    color: #008000; /* Adjust the color to match the logo color */
}

.date-ref  {
    position: absolute;
    right: 72px;

}
.date-ref div {
    margin-right: 20px;

}

.doctor-info h2 {
    margin-top: 0;
}

.patient-info, .complaint, .diagnosis, .medicine, .advice {
    margin-bottom: 20px;
}

h3, h4 {
    margin: 5px 0;
}

ul {
    list-style: none;
    padding: 0;
}
.diagnosis ol li{
    list-style-type: disc !important;
}
.diagnosis{
    width: 50%;
}
.details{
    margin-top: 0;
    margin-bottom: 5px;
}
.medicine ol li{
    list-style-type: disc !important;
}
.advice ol li{
    list-style-type: disc !important;
}
.medicine{
    margin-right: 40px;

}
.medicine-advice{
   position: absolute;
   right: 100px;
    margin-top: -77px;
}
</style>
</head>
<body>

<div class="container">
    <div class="header">
       @php
           $logoPath = public_path('dashboard_assets/images/logoN.png');
            $logotextPath = public_path('dashboard_assets/images/logotextN.png');

            $logoData = base64_encode(file_get_contents($logoPath));
            $logotextData = base64_encode(file_get_contents($logotextPath));
       @endphp

        <div class="" style="text-align: center">
            <img src="data:image/png;base64, {{ $logoData }}" width="65" class="mx-2" alt="">
            <img src="data:image/png;base64, {{ $logotextData }}" width="160" class="" alt="">
        </div>

    </div>
    <div class="doctor-info">
        <div class="date-ref" >
            <div>Date: {{ $prescription->created_at->format('d-M-y') }}</div>
            <div>Ref: {{ $prescription->reference }}</div>
        </div>
        <p class="details"><b>Doctor: </b>{{ $prescription->doctor->user->name }}</p>
        <p class="details" style="width: 50%" > <b>Degrees: </b>{{ $prescription->doctor->degrees }}</p>
        <p class="details"><b>Speciality: </b>{{ $prescription->doctor->docHasSpec? $prescription->doctor->docHasSpec->speciality->name : 'UNKNOWN' }}</p>
        <p class="details"><b>REG-ID:</b> {{ $prescription->doctor->registration }}</p>
    </div>
    <div class="patient-info" style="border-bottom: 1px solid #b8b8b8;border-top: 1px solid #b8b8b8; margin: 15px 0px 15px 0px;">

        <p style="margin: 8px 0px 7px 0px;"> <b>Patient Name:</b> {{ $prescription->patient->user->name }} &nbsp; | &nbsp; <b>Age:</b> {{ $age?? 'UNKNOWN' }} &nbsp; | &nbsp; <b>Gender:</b> {{ $prescription->patient->gender }} &nbsp; | &nbsp; <b>Group:</b> {{ $prescription->patient->blood_group }} &nbsp; | &nbsp;
            <br>
            <b>Weight:</b> {{ $weight?? 'UNKNOWN' }} &nbsp; | &nbsp; <b>Height:  </b>{{ $height?? 'UNKNOWN' }} &nbsp; | &nbsp; <b>Contact:</b> 01875134030</p>

    </div>
    <div class="complaint">
        <p> <b>Chief Complaint: </b> Mollit Quasi Id Non</p>
    </div>
    <div class="prescription">
        <div class="diagnosis">
            <h4>Diagnosis Tests:</h4>
            <ol>
                <li>Impedit sunt ut no Impedit sunt ut no Impedit sunt ut no</li>
            </ol>
        </div>
        <div class="medicine-advice">
            <div class="medicine">
                <h4>Medicine:</h4>
                <ol>
                    <li>Alias Id Non Aut Qui</li>
                    <li>Nihil laboris expedi</li>
                    <li>Qui officia quia iur</li>
                    <li>Ut et amet harum re</li>
                    <li>Inventore praesentiu</li>
                </ol>
            </div>
            <div class="advice">
                <h4>Advice:</h4>
                <ol>
                    <li>Dignissimos voluptat</li>
                </ol>
            </div>
        </div>

    </div>


</div>
</body>
</html>
