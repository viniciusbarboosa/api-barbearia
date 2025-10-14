<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetCode extends Mailable
{
    use Queueable, SerializesModels;

    public $code;
    public $name;

    /**
     * Create a new message instance.
     */
    public function __construct(string $code, ?string $name = null)
    {
        $this->code = $code;
        $this->name = $name;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Recuperação de senha - Código de verificação')
                    ->view('emails.password_reset_code')
                    ->with([
                        'code' => $this->code,
                        'name' => $this->name,
                    ]);
    }
}
