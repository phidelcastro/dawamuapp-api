<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ParentMails extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $typeofmail = "";
    public $data = [];

    /**
     * Create a new message instance.
     *
     * @param array $passeddata
     * @param string $emailtype
     */
    public function __construct($passeddata, $emailtype)
    {
        $this->data = $passeddata;
        $this->typeofmail = $emailtype;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $passed = $this->data;

        switch ($this->typeofmail) {
            case "parentWelcome":
                $guardian = $passed['guardian'];
                $password= $passed['password'];
                return $this->view('emails.parents.welcome', compact('guardian','password'))
                    ->subject($passed['subject'] ?? 'Welcome to Our School')
                    ->replyTo('info@dawamuscho.ac.ke', 'School Admin');
            case "newMessage":
                $event = $passed['event'];
                return $this->view('emails.parents.', compact('event'))
                    ->subject($passed['subject'] ?? 'New Message From Dawamu')
                    ->replyTo('info@dawamuscho.ac.ke', 'School Admin');

            default:
                return $this->view('emails.parents.default')
                    ->subject($passed['subject'] ?? 'Parent Communication')
                    ->replyTo('info@dawamuscho.ac.ke', 'School Admin');
        }
    }
}
