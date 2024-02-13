<?php

namespace App\Mail\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoginAlert extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    protected $ip_address;

    public function __construct($ip_address)
    {
        $this->ip_address = $ip_address;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $ip_address = $this->ip_address;
        return $this->subject('Login Alert')->view('email-templates.admin.login-alert', ['ip_address' => $ip_address]);
    }
}
