<?php

namespace App\Mail;

use App\Models\WakilPerusahaan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WakilPerusahaanAccepted extends Mailable
{
    use Queueable, SerializesModels;

    public $applicant;

    public function __construct(WakilPerusahaan $applicant)
    {
        $this->applicant = $applicant;
    }

    public function build()
    {
        return $this->subject('Pendaftaran Mitra Magang Diterima')
                    ->view('emails.wakil_perusahaan.accepted');
    }
}