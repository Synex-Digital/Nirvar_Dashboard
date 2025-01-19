<?php

namespace App\Console\Commands;

use Dompdf\Dompdf;
use App\Models\File;
use App\Models\Folder;
use App\Models\Prescription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GeneratePrescriptionCommand extends Command
{
    protected $signature = 'generate:prescription {prescriptionId}';
    protected $description = 'Generate a PDF for a specific prescription';

    public function handle()
    {
        $prescriptionId = $this->argument('prescriptionId');
        Log::info("Starting PDF generation for Prescription ID: {$prescriptionId}");

        $prescription = Prescription::with('doctor', 'patient.user')->find($prescriptionId);

        if (!$prescription) {
            Log::error("Prescription not found for ID: {$prescriptionId}");
            return;
        }

        try {
            $prescription_name = 'Prescription_' . rand(1000, 9999) . '.pdf';
            $doctor_spec = $prescription->doctor->docHasSpec->speciality->name;
            $patient_id = $prescription->patient->user->id;
            $folder = Folder::where('user_id',$patient_id)->where('name',$doctor_spec)->first();

            if(!$folder){
                $new_folder = new Folder();
                $new_folder->user_id = $patient_id;
                $new_folder->name = $doctor_spec;
                $new_folder->save();
            }
            $file = new File();
            $file->name = $prescription_name;
            $file->folder_id = $folder ? $folder->id: $new_folder->id;
            $file->save();

            // Log before generating the PDF
            Log::info("Generating PDF for Prescription ID: {$prescriptionId}");

            // Generate the PDF
            $dompdf = new Dompdf();
            $dompdf->loadHtml(view('prescriptionPDF', [
                'prescription' => $prescription,
                'age' => $this->calculateAge($prescription->patient->date_of_birth, $prescription->created_at),
                'weight' => $this->weight($prescription->patient->weight_height),
                'height' => $this->height($prescription->patient->weight_height),
                'tests' => $this->tests($prescription->tests),
                'advice' => $this->prescriptionAdvice($prescription->prescription_advice),
            ])->render());

            $dompdf->setPaper('A4');
            $dompdf->render();

            $output = $dompdf->output();

            // Define file path
            $filePath = public_path('uploads/patient/files/'.$prescription_name);

            // Log the file path
            Log::info("Saving PDF to: {$filePath}");

            // Save the PDF
            file_put_contents($filePath, $output);

            Log::info("PDF generated successfully: {$filePath}");
        } catch (\Exception $e) {
            // Log any exception
            Log::error("Error during PDF generation: " . $e->getMessage());
        }
    }


    private function calculateAge($birthdate, $currentDate)
    {
        $birthDate = new \DateTime($birthdate);
        $currentDate = new \DateTime($currentDate);
        return $currentDate->diff($birthDate)->y;
    }

    private function weight($input)
    {
        return explode(',', $input)[0] ?? null;
    }

    private function height($input)
    {
        return explode(',', $input)[1] ?? null;
    }

    private function tests($input)
    {
        return $input ? explode('" "', trim($input, '"')) : [];
    }

    private function prescriptionAdvice($input)
    {
        return $input ? explode('" "', trim($input, '"')) : [];
    }
}
