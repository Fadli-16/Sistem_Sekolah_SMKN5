<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TidakLulusDaftarUlangMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $notes;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $notes = null)
    {
        $this->name = $name;
        $this->notes = $notes;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Mohon Maaf, Anda Belum Lulus Daftar Ulang')
                    ->view('emails.daftar_ulang_tidak_lulus');
    }
}