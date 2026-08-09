<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Email selamat datang untuk pengguna baru ForMysha.
 */
class WelcomeMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user,
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Selamat Datang di ForMysha! 🎉',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            htmlString: $this->buildHtml(),
        );
    }

    /**
     * Build the HTML content for the email.
     */
    private function buildHtml(): string
    {
        $name = e($this->user->name);

        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
        </head>
        <body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8fafc; margin: 0; padding: 20px;">
            <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                <div style="background: linear-gradient(135deg, #fbc2eb 0%, #a6c1ee 100%); padding: 40px 30px; text-align: center;">
                    <h1 style="color: #ffffff; margin: 0; font-size: 28px;">🎒 ForMysha</h1>
                    <p style="color: #ffffff; margin: 10px 0 0; font-size: 14px; opacity: 0.9;">Digital Life Book untuk Buah Hati</p>
                </div>
                <div style="padding: 30px;">
                    <h2 style="color: #1e293b; margin-top: 0;">Halo, {$name}! 👋</h2>
                    <p style="color: #475569; line-height: 1.6;">
                        Selamat datang di <strong>ForMysha</strong>! Kami sangat senang Anda bergabung.
                    </p>
                    <p style="color: #475569; line-height: 1.6;">
                        ForMysha adalah tempat terbaik untuk menyimpan setiap momen berharga perjalanan hidup anak Anda — dari hari pertama lahir hingga mereka tumbuh dewasa.
                    </p>
                    <div style="background-color: #f1f5f9; border-radius: 12px; padding: 20px; margin: 20px 0;">
                        <h3 style="color: #1e293b; margin-top: 0; font-size: 16px;">Mulai dengan:</h3>
                        <ul style="color: #475569; line-height: 2; padding-left: 20px;">
                            <li>👶 <strong>Tambah profil anak</strong> Anda</li>
                            <li>📸 <strong>Unggah foto & video</strong> kenangan</li>
                            <li>📝 <strong>Ceritakan perjalanan</strong> hidup mereka</li>
                            <li>📊 <strong>Pantau pertumbuhan</strong> dan kesehatan</li>
                        </ul>
                    </div>
                    <div style="text-align: center; margin: 30px 0;">
                        <a href="{{ url('/dashboard') }}" style="background: linear-gradient(135deg, #fbc2eb 0%, #a6c1ee 100%); color: #ffffff; padding: 14px 32px; border-radius: 12px; text-decoration: none; font-weight: 600; display: inline-block;">
                            Mulai Sekarang →
                        </a>
                    </div>
                    <p style="color: #94a3b8; font-size: 13px; text-align: center;">
                        Setiap momen layak dikenang. Setiap keluarga berhak memiliki tempat yang aman untuk menyimpan kenangan.
                    </p>
                </div>
                <div style="background-color: #f8fafc; padding: 20px 30px; text-align: center; border-top: 1px solid #e2e8f0;">
                    <p style="color: #94a3b8; font-size: 12px; margin: 0;">
                        © {{ date('Y') }} ForMysha — formysha.my.id<br>
                        <em>Every Moment, Every Memory, One Lifetime.</em>
                    </p>
                </div>
            </div>
        </body>
        </html>
        HTML;
    }
}
