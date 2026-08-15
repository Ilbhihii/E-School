<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GeneralNotification extends Notification
{
    use Queueable;

    public string $title;
    public string $message;
    public string $category;
    public string $icon;
    public ?string $url;
    public array $extra;
    public string $priority;

    public function __construct(
        string $title,
        string $message,
        string $category = 'general',
        ?string $url = null,
        string $icon = 'bi bi-bell-fill',
        array $extra = [],
        string $priority = 'normal'
    ) {
        $this->title = trim($title);
        $this->message = trim($message);
        $this->category = trim($category) ?: 'general';
        $this->url = $url;
        $this->icon = $this->sanitizeIcon($icon);
        $this->extra = $extra;
        $this->priority = in_array($priority, ['low', 'normal', 'high'], true)
            ? $priority
            : 'normal';
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'category' => $this->category,
            'icon' => $this->icon,
            'url' => $this->url,
            'priority' => $this->priority,
            'extra' => $this->extra,
        ];
    }

    protected function sanitizeIcon(string $icon): string
    {
        return preg_match('/^bi bi-[a-z0-9-]+$/i', $icon)
            ? $icon
            : 'bi bi-bell-fill';
    }
}
