<?php

namespace App\Jobs;

use DateTime;
use Dompdf\Dompdf;
use App\Models\Folder;
use App\Models\File as Files;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Dompdf\Exception as DompdfException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GeneratePrescriptionPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $prescriptions;
    /**
     * Create a new job instance.
     */
    public function __construct($prescriptions)
    {
        $this->prescriptions = $prescriptions;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $prescriptions = $this->prescriptions;
        // instantiate and use the dompdf class
 
            // instantiate and use the dompdf class
            $dompdf = new Dompdf();
            $dompdf->loadHtml(view('prescriptionPDF', [
                'prescription' => $prescriptions,
                'age' => $this->calculateAge($prescriptions->patient->date_of_birth, $prescriptions->created_at),
                'weight' => $this->weight($prescriptions->patient->weight_height),
                'height' => $this->height($prescriptions->patient->weight_height),
                'tests' => $this->tests($prescriptions->tests),
                'advice' => $this->prescriptionAdvice($prescriptions->prescription_advice),
            ]));

            $dompdf->setPaper('A4');
            $dompdf->render();
            $output = $dompdf->output();

            // Rest of your code to save the file
            $department_name = $prescriptions->doctor->docHasSpec ? $prescriptions->doctor->docHasSpec->speciality->name : 'UNKNOWN';
            $user = $prescriptions->patient->user;
            $folders = Folder::where('user_id', $user->id)->where('name', $department_name)->first();
            $new_folder = null;
            if (!$folders) {
                $new_folder = new Folder;
                $new_folder->user_id = $user->id;
                $new_folder->name = $department_name;
                $new_folder->save();
            }

            $file_name = 'Prescription' . '_PR-' . rand(1000, 9999) . '.pdf';
            $new_file = new Files;
            $new_file->folder_id = $folders ? $folders->id : $new_folder->id;
            $new_file->name = $file_name;
            $new_file->type = 'prescription';
            $new_file->save();
            $filePath = public_path('uploads/patient/files/' . $file_name);
            file_put_contents($filePath, $output);

    }
    private function calculateAge($birthdate, $currentDate)
    {
        $birthDate = new DateTime($birthdate);
        $currentDate = new DateTime($currentDate);
        $age = $currentDate->diff($birthDate)->y;
        return $age;
    }
     private function weight($input) {
        $weight = null;
        $parts = explode(',', $input);
        if (isset($parts[0])) {
            $weight = trim($parts[0]);
        }
        return $weight;
    }
     private function height($input) {
        $height = null;
        $feet = null;
        $inches = null;
        $parts = explode(',', $input);
        if (isset($parts[1])) {
            $height = trim($parts[1]);
        }
        if ($height !== null) {
            preg_match('/(\d+)\s*FT\s*(\d*)\s*IN*/i', $height, $matches);
            if (isset($matches[1])) {
                $feet = $matches[1];
            }
            if (isset($matches[2])) {
                $inches = $matches[2];
            }
        }
        return $height;
    }
     private function tests($input) {
        $tests = null;
        $parts = $input ? explode('" "', trim($input, '"')) : [];
        if (isset($parts)) {
            $tests = $parts;
        }
        return $tests;
    }
     private function prescriptionAdvice($input) {
        $advice = null;
        $parts = $input ? explode('" "', trim($input, '"')) : [];
        if (isset($parts)) {
            $advice = $parts;
        }
        return $advice;
    }
}
