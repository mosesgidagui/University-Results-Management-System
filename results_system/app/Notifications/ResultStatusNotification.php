<?php

namespace App\Notifications;

use App\Models\Result;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResultStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        private Result $result,
        private string $event,  // 'rejected_by_hod' | 'published'
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $course = $this->result->course->code;

        return match ($this->event) {
            'rejected_by_hod' => (new MailMessage)
                ->subject("Result Returned for Correction — {$course}")
                ->greeting("Hello {$notifiable->name},")
                ->line("Your result for **{$course}** has been returned by the Head of Department for correction.")
                ->line("**HOD Comment:** " . ($this->result->hod_comment ?? 'No comment provided.'))
                ->action('View Results', url('/lecturer'))
                ->line('Please update and resubmit the result.'),

            'published' => (new MailMessage)
                ->subject("Your Results Are Now Available — {$course}")
                ->greeting("Hello {$notifiable->name},")
                ->line("Your examination result for **{$course}** has been officially published.")
                ->line("**Grade:** {$this->result->grade} — {$this->result->remark}")
                ->action('View My Results', url('/student'))
                ->line('Log in to view your full result sheet and GPA.'),

            default => (new MailMessage)->line('Your result status has been updated.'),
        };
    }

    public function toArray(object $notifiable): array
    {
        return [
            'event'     => $this->event,
            'result_id' => $this->result->id,
            'course'    => $this->result->course->code,
            'message'   => match ($this->event) {
                'rejected_by_hod' => "Result for {$this->result->course->code} returned for correction.",
                'published'       => "Result for {$this->result->course->code} is now published.",
                default           => 'Result status updated.',
            },
        ];
    }
}
