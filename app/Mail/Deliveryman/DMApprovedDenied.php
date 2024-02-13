<?php

namespace App\Mail\Deliveryman;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DMApprovedDenied extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    protected $dm;

    public function __construct($dm)
    {
        $this->dm = $dm;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $dm = $this->dm;
        return $this->subject('Deliveryman Registration Status')->view('email-templates.deliveryman.dm-approved-denied', ['dm' => $dm]);
    }
}
