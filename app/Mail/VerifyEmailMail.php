<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class VerifyEmailMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * instance user
     */
    public $user;
    
    /**
     * url verifikasi
     */
    public $verificationUrl;

    /**
     * buat instance message baru
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        
        // generate url verifikasi
        $this->verificationUrl = url('/email/verify/' . $user->id . '/' . sha1($user->email));
    }

    /**
     * envelope untuk email
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'verifikasi email anda - Karsa',
        );
    }

    /**
     * konten email
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.verification',
            with: [
                'user' => $this->user,
                'verificationUrl' => $this->verificationUrl,
                'expiresIn' => 60 // menit
            ]
        );
    }

    /**
     * attachment
     */
    public function attachments(): array
    {
        return [];
    }
}