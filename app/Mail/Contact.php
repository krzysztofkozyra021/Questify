<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class Contact extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public array $data,
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Contact from " . $this->data["name"],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $html = "
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        line-height: 1.6;
                        color: #fef3c7;
                        max-width: 600px;
                        margin: 0 auto;
                        padding: 20px;
                        background-color: #1c1917;
                    }
                    .container {
                        background-color: #292524;
                        border-radius: 8px;
                        padding: 30px;
                        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
                    }
                    .header {
                        text-align: center;
                        margin-bottom: 30px;
                        padding-bottom: 20px;
                        border-bottom: 2px solid #44403c;
                    }
                    .logo {
                        max-width: 150px;
                        height: auto;
                        margin-bottom: 20px;
                    }
                    .header h1 {
                        color: #fef3c7;
                        margin: 0;
                        font-size: 24px;
                        font-weight: bold;
                    }
                    .content {
                        background-color: #44403c;
                        padding: 20px;
                        border-radius: 6px;
                        margin-bottom: 20px;
                    }
                    .field {
                        margin-bottom: 15px;
                    }
                    .field strong {
                        color: #fef3c7;
                        display: block;
                        margin-bottom: 5px;
                        font-size: 14px;
                        text-transform: uppercase;
                        letter-spacing: 0.5px;
                    }
                    .field-value {
                        color: #fef3c7;
                        font-size: 16px;
                    }
                    .message-section {
                        margin-top: 25px;
                    }
                    .message-section h2 {
                        color: #fef3c7;
                        font-size: 18px;
                        margin-bottom: 15px;
                        font-weight: bold;
                    }
                    .message-content {
                        background-color: #292524;
                        padding: 15px;
                        border-radius: 4px;
                        border: 1px solid #44403c;
                        color: #fef3c7;
                    }
                    .footer {
                        text-align: center;
                        margin-top: 30px;
                        padding-top: 20px;
                        border-top: 1px solid #44403c;
                        color: #a8a29e;
                        font-size: 14px;
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <img src='" . asset("images/logo.png") . "' alt='Questify Logo' class='logo'>
                        <h1>New Contact Form Submission</h1>
                    </div>
                    
                    <div class='content'>
                        <div class='field'>
                            <strong>Name</strong>
                            <div class='field-value'>{$this->data["name"]}</div>
                        </div>
                        
                        <div class='field'>
                            <strong>Email</strong>
                            <div class='field-value'>{$this->data["email"]}</div>
                        </div>
                        
                        <div class='field'>
                            <strong>Subject</strong>
                            <div class='field-value'>{$this->data["subject"]}</div>
                        </div>
                        
                        <div class='message-section'>
                            <h2>Message</h2>
                            <div class='message-content'>
                                {$this->data["message"]}
                            </div>
                        </div>
                    </div>
                    
                    <div class='footer'>
                        This email was sent from the Questify contact form
                    </div>
                </div>
            </body>
            </html>
        ";

        return new Content(
            htmlString: $html,
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
