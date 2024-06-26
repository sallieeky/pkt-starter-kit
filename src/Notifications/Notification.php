<?php

namespace Pkt\StarterKit\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification as NotificationTrait;
use Illuminate\Support\HtmlString;

class Notification extends NotificationTrait
{
    use Queueable;

    private string $title;
    private HtmlString|string $message;
    private ?string $url;

    /**
     * Create a new notification instance.
     * @param string $title
     * @param HtmlString|string $message
     * @param string $url
     */
    public function __construct(string $title, HtmlString|string $message, string $url = null)
    {
        $this->title = $title;
        $this->message = $message;
        $this->url = $url;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
        ]);
    }
}
