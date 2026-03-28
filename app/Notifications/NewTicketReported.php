<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTicketReported extends Notification
{
    use Queueable;
    protected $issue;

    public function __construct($issue) { $this->issue = $issue; }

    public function via($notifiable) { return ['database', 'mail']; }

    public function toMail($notifiable) {
        return (new MailMessage)
            ->subject('New Ticket: ' . $this->issue->issue_id)
            ->line('New report from ' . $this->issue->reported_by)
            ->action('View Ticket', route('issues.view', $this->issue->id));
    }

    public function toArray($notifiable) {
        return [
            'issue_id' => $this->issue->issue_id,
            'id_primary' => $this->issue->id,
            'message' => 'New ticket from ' . $this->issue->reported_by,
        ];
    }
}