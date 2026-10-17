<?php

namespace App\Mail;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Email pengingat perpanjangan langganan ForMysha yang akan segera berakhir.
 *
 * Dikirim secara sinkron (tanpa ShouldQueue) karena di-trigger oleh
 * command harian subscription:send-reminders yang berjalan terjadwal.
 */
class SubscriptionReminder extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Subscription $subscription,
        public int $daysRemaining,
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $planName = $this->subscription->plan?->name ?? 'ForMysha';

        return new Envelope(
            subject: "Pengingat Perpanjangan ForMysha — {$planName} ({$this->daysRemaining} hari lagi)",
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
        $planName = e($this->subscription->plan?->name ?? '-');
        $tenantName = e($this->subscription->tenant?->name ?? '-');
        $endDate = e($this->subscription->ends_at?->translatedFormat('d F Y') ?? '-');
        $daysRemaining = (int) $this->daysRemaining;

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
                    <p style="color: #ffffff; margin: 10px 0 0; font-size: 14px; opacity: 0.9;">Pengingat Perpanjangan Langganan</p>
                </div>
                <div style="padding: 30px;">
                    <h2 style="color: #1e293b; margin-top: 0;">Halo! 👋</h2>
                    <p style="color: #475569; line-height: 1.6;">
                        Langganan <strong>{$tenantName}</strong> pada paket <strong>{$planName}</strong> akan berakhir dalam
                        <strong>{$daysRemaining} hari</strong> lagi. Perpanjang sekarang agar layanan tetap aktif dan kenangan buah hati Anda tetap tersimpan dengan aman.
                    </p>
                    <div style="background-color: #f1f5f9; border-radius: 12px; padding: 20px; margin: 20px 0; text-align: center;">
                        <p style="color: #64748b; margin: 0; font-size: 14px;">Paket</p>
                        <p style="color: #1e293b; margin: 5px 0; font-size: 24px; font-weight: bold;">{$planName}</p>
                        <p style="color: #f59e0b; margin: 5px 0 0; font-size: 14px; font-weight: 600;">Berakhir pada {$endDate}</p>
                    </div>
                    <p style="color: #475569; line-height: 1.6;">
                        Terima kasih telah menggunakan ForMysha untuk menyimpan kenangan berharga buah hati Anda.
                    </p>
                    <div style="text-align: center; margin: 30px 0;">
                        <a href="{{ url('/subscription/current') }}" style="background: linear-gradient(135deg, #fbc2eb 0%, #a6c1ee 100%); color: #ffffff; padding: 14px 32px; border-radius: 12px; text-decoration: none; font-weight: 600; display: inline-block;">
                            Perpanjang Langganan
                        </a>
                    </div>
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
