<?php

namespace App\Services;

use App\Models\Donation;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use PDF;

class DonationEmailService
{
    public function sendDonationEmail(Donation $donation)
    {
        $template = EmailTemplate::where('type', 'donation')->first();

        if (!$template) {
            return false;
        }

        // Generate PDF receipt
        $pdf = PDF::loadView('emails.donation-receipt', ['donation' => $donation]);
        $pdfPath = 'receipts/donation-' . $donation->id . '.pdf';
        Storage::put('public/' . $pdfPath, $pdf->output());

        // Replace placeholders in template
        $subject = $this->replacePlaceholders($template->subject, $donation);
        $body = $this->replacePlaceholders($template->body, $donation);

        // Send email
        Mail::send('emails.donation', ['body' => $body], function ($message) use ($donation, $subject, $pdfPath) {
            $message->to($donation->email, $donation->full_name)
                   ->subject($subject)
                   ->attach(Storage::path('public/' . $pdfPath), [
                       'as' => 'donation-receipt.pdf',
                       'mime' => 'application/pdf',
                   ]);
        });

        // Clean up PDF
        Storage::delete('public/' . $pdfPath);

        return true;
    }

    private function replacePlaceholders($content, Donation $donation)
    {
        $placeholders = [
            '{donor_name}' => $donation->full_name,
            '{amount}' => number_format($donation->amount, 2),
            '{transaction_id}' => $donation->transaction_id,
            '{date}' => $donation->created_at->format('F j, Y'),
            '{payment_method}' => ucfirst($donation->payment_method)
        ];

        return str_replace(array_keys($placeholders), array_values($placeholders), $content);
    }
}
