<?php

namespace App\Mail\Deliveryman;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DMRegistration extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    protected $dm;
    protected $password;

    public function __construct($dm, $password)
    {
        $this->dm = $dm;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $dm = $this->dm;
        $password = $this->password;
        return $this->view('email-templates.deliveryman.dm-registration', ['dm' => $dm, 'password' => $password]);
    }
}
