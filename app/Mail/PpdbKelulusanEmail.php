<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PpdbKelulusanEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $dari;
    public $subject;
    protected $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    function __construct($dari, $subject, $data)
    {
        $this->dari = $dari;
        $this->subject = $subject;
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(){
        return $this->from($this->dari)
                    ->subject($this->subject)
                    ->view('ppdb.ppdb_kelulusan_email', [
                        'message_custom' => $this->data,
                        'bantuan_wa' => 'info@smkpadang.sch.id',
                        'bantuan_email' => '(0751) 123456'
                    ]);
    }
}
