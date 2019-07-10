<?php
namespace App\Mail;
 
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
 
class PasswordRecoveryMail extends Mailable {
 
    use Queueable, SerializesModels;
    
    public $user;
    public $token;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
        $this->token = $user->token;
    }
 
    //build the message.
    public function build()
    {
        return $this->view('emails.recovery');
    }
}