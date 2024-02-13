<?php

namespace App\Mail\Deliveryman;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DMForgotPassword extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    protected $dm;
    protected $code;

    public function __construct($dm, $code)
    {
        $this->dm = $dm;
        $this->code = $code;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $dm = $this->dm;
        $code = $this->code;
        return $this->view('email-templates.deliveryman.dm-registration', ['dm' => $dm, 'code' => $code]);
    }
}
