<?php

namespace App\Notifications;

use App\Models\Message;
use App\Models\User;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class NewMessagePush extends Notification
{
    public function __construct(
        public Message $message,
        public User $sender,
    ) {}

    public function via($notifiable): array
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        $name = $this->sender->username ?? $this->sender->name;

        return (new WebPushMessage)
            ->title($name)
            ->icon($this->sender->profile_photo_url)
            ->body(\Illuminate\Support\Str::limit($this->message->body, 100))
            ->data([
                'url' => route('chats.show', $this->message->party_match_id),
                'match_id' => $this->message->party_match_id,
            ]);
    }
}
