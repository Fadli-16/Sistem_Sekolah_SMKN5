<?php

namespace App\Mail;

use App\Models\WakilPerusahaan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WakilPerusahaanRejected extends Mailable
{
    use Queueable, SerializesModels;

    public $applicant;

    public function __construct(WakilPerusahaan $applicant)
    {
        $this->applicant = $applicant;
    }

    public function build()
    {
        return $this->subject('Pendaftaran Mitra Magang Ditolak')
                    ->view('emails.wakil_perusahaan.rejected');
    }
}