<?php

namespace App\Mail\Branch;

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
    protected $name;

    public function __construct($ip_address, $name)
    {
        $this->ip_address = $ip_address;
        $this->name = $name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $ip_address = $this->ip_address;
        $name = $this->name;
        return $this->subject('Login Alert')->view('email-templates.branch.login-alert', ['ip_address' => $ip_address, 'name' => $name]);
    }
}
