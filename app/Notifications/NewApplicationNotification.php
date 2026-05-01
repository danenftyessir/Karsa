<?php

namespace App\Notifications;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Notification untuk company ketika ada lamaran baru masuk
 */
class NewApplicationNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public JobApplication $application
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        // Kirim email jika enabled di config
        if (config('company.notifications.new_application_email', true)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $applicantName = $this->application->user->name ?? 'Kandidat';
        $jobTitle = $this->application->jobPosting->title ?? 'Posisi';

        return (new MailMessage)
            ->subject('Lamaran Baru: ' . $jobTitle)
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Anda mendapat lamaran baru untuk posisi **' . $jobTitle . '**.')
            ->line('Pelamar: **' . $applicantName . '**')
            ->action('Lihat Lamaran', route('company.applications.show', $this->application->id))
            ->line('Segera tinjau lamaran ini untuk mendapatkan talent terbaik!');
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
            'applicant_id' => $this->application->user_id,
            'applicant_name' => $this->application->user->name ?? 'Unknown',
            'job_title' => $this->application->jobPosting->title ?? 'Unknown Position',
            'message' => 'Lamaran baru untuk posisi ' . ($this->application->jobPosting->title ?? 'Unknown'),
        ];
    }
}
