<?php

namespace App\Http\Controllers;

use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\PatientInvoiceFiles;
use App\Models\PatientReferralFiles;
use App\Helpers\CustomHelper;
use App\Http\Controllers\DropboxController;

class PdfController extends Controller
{
    public function generateReferralPdf($data = null){

        try{
            $dompdf = new Dompdf();

            $html = view('pdf.referral', ['data' => $data])->render();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            // Output the generated PDF to the browser
            // $dompdf->stream('referral.pdf', array('Attachment' => 0));

            $patient_info = '';
            if (isset($data['patient_name'])){
                $patient_info = $data['patient_name'];
            }

            $filename = $patient_info.'-'. 'Demographics-'.date('m-d-Y').'.pdf';
            $file_path = public_path("uploads/referral/$filename");
            file_put_contents($file_path, $dompdf->output());
            // Storage::put($file_path, $dompdf->output());

            $dropbox = new DropboxController();
            $result = $dropbox->uploadStorageFile2Dropbox($patient_info, $filename, 'referral');

            if(!$result){
                CustomHelper::trackInfo($result);
            }
            if(isset($data['id']) && $data['id'] > 0){
                $invoice_rcord = PatientReferralFiles::query()->where('transaction_id', $data['id'])->get()->first();
                if ($invoice_rcord){
                    PatientReferralFiles::query()->where('transaction_id', $data['id'])->update(['referral_file' => $filename, 'updated_at' => date('Y-m-d H:i:s')]);
                } else {
                    $new_record = new PatientReferralFiles();
                    $new_record->transaction_id = $data['id'] ;
                    $new_record->referral_file = $filename;
                    $new_record->save();
                }
            }

        } catch (\Exception $e) {
            // Handle the exception here
            CustomHelper::trackInfo($e);
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }
    public function generateInvoicePdf($data = null)
    {
        // Create a new Dompdf instance
        $dompdf = new Dompdf();

        // Load the HTML content for the PDF

        $html = view('pdf.invoice', ['data' => $data])->render();
        $dompdf->loadHtml($html);

        // Set paper size and orientation (optional)
        $dompdf->setPaper('A4', 'portrait');
        // $dompdf->setPaper('A4', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to the browser
        // $dompdf->stream('invoice.pdf');

        // Save the PDF to the storage directory
        $patient_info = '';
        if (isset($data['patient_name'])){
            $patient_info = $data['patient_name'];
        }
        $filename = $patient_info.'-'.'invoice-'.date('m-d-Y').'.pdf';
        $file_path = public_path("uploads/invoice/$filename");
        file_put_contents($file_path, $dompdf->output());
        // Storage::put($file_path, $dompdf->output());
        // if (!is_dir(storage_path('app/public/invoice'))) {
        //     mkdir(storage_path('app/public/invoice'), 0775, true);
        // }
        $dropbox = new DropboxController();
        $result = $dropbox->uploadStorageFile2Dropbox($patient_info, $filename, 'invoice');
        if(!$result){
            CustomHelper::trackInfo($result);
        }


        if(isset($data['transaction_id']) && $data['transaction_id'] > 0){
            $invoice_rcord = PatientInvoiceFiles::query()->where('transaction_id', $data['transaction_id'])->get()->first();
            if ($invoice_rcord){
                PatientInvoiceFiles::query()->where('transaction_id', $data['transaction_id'])->update(['invoice_file' => $filename, 'updated_at' => date('Y-m-d H:i:s')]);
            } else {
                $new_record = new PatientInvoiceFiles();
                $new_record->transaction_id = $data['transaction_id'] ;
                $new_record->invoice_file = $filename;
                $new_record->save();
            }
        }
    }
}
