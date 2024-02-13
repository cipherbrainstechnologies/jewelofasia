<?php

namespace App\Mail\Branch;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BranchRegistration extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    protected $branch;
    protected $password;

    public function __construct($branch, $password)
    {
        $this->branch = $branch;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $branch = $this->branch;
        $password = $this->password;
        return $this->subject('Branch Registration')->view('email-templates.branch.branch-registration', ['branch' => $branch, 'password' => $password]);
    }
}
