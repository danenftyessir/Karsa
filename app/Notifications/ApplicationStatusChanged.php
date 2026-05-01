<?php

namespace App\Notifications;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Notification untuk applicant ketika status lamarannya berubah
 */
class ApplicationStatusChanged extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public JobApplication $application,
        public string $oldStatus,
        public string $newStatus
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $jobTitle = $this->application->jobPosting->title ?? 'Posisi';
        $companyName = $this->application->jobPosting->company->name ?? 'Perusahaan';
        $statusLabel = config('company.application_status_options.' . $this->newStatus, $this->newStatus);

        $mail = (new MailMessage)
            ->subject('Update Status Lamaran: ' . $jobTitle)
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Status lamaran Anda untuk posisi **' . $jobTitle . '** di **' . $companyName . '** telah diupdate.');

        // Custom message based on status
        switch ($this->newStatus) {
            case 'shortlisted':
                $mail->line('🎉 Selamat! Anda masuk ke tahap shortlist.')
                     ->line('Tim HR akan segera menghubungi Anda untuk tahap selanjutnya.');
                break;

            case 'interview':
                $mail->line('🎉 Selamat! Anda dipanggil untuk interview.')
                     ->line('Perhatikan email atau telepon Anda untuk informasi jadwal interview.');
                break;

            case 'offer':
                $mail->line('🎉 Selamat! Anda mendapat penawaran kerja.')
                     ->line('Tim HR akan menghubungi Anda untuk membahas detail penawaran.');
                break;

            case 'hired':
                $mail->line('🎉 Selamat! Anda diterima bekerja di ' . $companyName . '!')
                     ->line('Selamat bergabung dan sukses di pekerjaan baru Anda!');
                break;

            case 'rejected':
                $mail->line('Mohon maaf, lamaran Anda belum berhasil kali ini.')
                     ->line('Jangan menyerah! Terus cari peluang lainnya dan tingkatkan kemampuan Anda.');
                break;

            default:
                $mail->line('Status baru: **' . $statusLabel . '**');
        }

        $mail->action('Lihat Detail Lamaran', route('student.applications.show', $this->application->id))
             ->line('Terima kasih telah melamar di Lapangku!');

        return $mail;
    }

    /**
     * Get the array representation of the notification (for database).
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'application_id' => $this->application->id,
            'job_posting_id' => $this->application->job_posting_id,
            'company_id' => $this->application->jobPosting->company_id ?? null,
            'company_name' => $this->application->jobPosting->company->name ?? 'Unknown',
            'job_title' => $this->application->jobPosting->title ?? 'Unknown Position',
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'message' => 'Status lamaran Anda untuk ' . ($this->application->jobPosting->title ?? 'posisi') . ' diupdate ke ' . config('company.application_status_options.' . $this->newStatus, $this->newStatus),
        ];
    }
}
