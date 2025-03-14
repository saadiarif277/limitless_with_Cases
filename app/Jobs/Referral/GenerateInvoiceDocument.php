<?php

namespace App\Jobs\Referral;

use App\Models\Referral;
use App\Models\DocumentType;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GenerateInvoiceDocument implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The the referral to create an invoice for.
     *
     * @var Referral
     */
    protected $referral;

    /**
     * Create a new job instance.
     */
    public function __construct(Referral $referral)
    {
        $this->referral = $referral;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $documentType = DocumentType::findOrFail(DocumentType::INVOICE);

        // Eager load the necessary relationships
        $this->referral->load([
            'appointment',
            'attorneyUser.lawFirm.state',
            'clinic.state',
            'documents',
            'doctorUser.state',
            'patientUser.state',
            'referralReasons',
            'referralStatus',
        ]);

        // Render your Blade view into HTML
        $html = view('pdf.invoice', ['referral' => $this->referral])->render();

        // Load the HTML into DOMPDF
        $pdf = PDF::loadHTML($html);

        // Generate the output
        $output = $pdf->output();

        // Generate a file name for the PDF
        $fileName = "";
        $fileName .= Str::slug($this->referral->patientUser->name);
        $fileName .= '-'.Str::slug($documentType->name);
        $fileName .= '-'.now()->format('Y-m-d-H:i:s');
        $fileName .= '-REF#'.$this->referral->getKey();
        $fileName .= '.pdf';

        // Upload the file to the specified path and get the stored file path
        $path = "public/uploads/documents/{$fileName}";

        // Use the Storage facade to save the PDF
        Storage::put($path, $output);

        $invoiceDocument = $this->referral
            ->documents()
            ->where('document_type_id', DocumentType::INVOICE)
            ->first();

        if (!$invoiceDocument) {
            $this->referral->documents()->create([
                'document_type_id' => DocumentType::INVOICE,
                'name' => $fileName,
                'path' => $path,
            ]);
        } else {
            $invoiceDocument->update([
                'name' => $fileName,
                'path' => $path,
            ]);
        }
    }
}
