<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentMails extends Mailable implements ShouldQueue
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
            case "studentWelcome":
                $student = $passed['student'];
                $password= $passed['password'];
                return $this->view('emails.students.welcome', compact('student','password'))
                    ->subject($passed['subject'] ?? 'Welcome to Our School')
                    ->replyTo('info@example.com', 'School Admin');

            default:
                return $this->view('emails.students.default')
                    ->subject($passed['subject'] ?? 'Staff Communication')
                    ->replyTo('info@example.com', 'School Admin');
        }
    }
}
