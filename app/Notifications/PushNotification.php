<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Channels\DatabaseChannel;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;
use NotificationChannels\Fcm\Resources\Notification as NotificationResource;

abstract class PushNotification extends Notification
{
    use Queueable;

    abstract public function getCode(): string;
    abstract public function getTitle(): string;
    abstract public function getBody(): string;
    abstract public function getData(): array;

    public $notifiable;

    public function via($notifiable)
    {
        $this->notifiable = $notifiable;
        return [
            DatabaseChannel::class,
            FcmChannel::class,
        ];
    }

    public function toFcm($notifiable): FcmMessage
    {

        $this->notifiable = $notifiable;
        return (new FcmMessage(notification: new FcmNotification(
            title: $this->getTitle(),
            body: $this->getBody(),
            image: $this->getImage(),
        )))
            ->notification($this->getNotification())
            ->data($this->getFcmData())
            ->custom([
                'android' => [
                    'notification' => [
                        'color' => '#0A0A0A',
                        'channel_id' => 'test', // ex: project name
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                        'image' => $this->getImage(),
                    ],
                    'fcm_options' => [
                        'analytics_label' => 'android-'.$this->getCode(),
                    ],
                ],
                'apns' => [
                    'fcm_options' => [
                        'analytics_label' => 'ios-'.$this->getCode(),
                        'image' => $this->getImage(),
                    ],
                ],
            ]);
    }

    public function getNotification(): ?NotificationResource
    {
        return NotificationResource::create()
            ->title($this->getTitle())
            ->image($this->getImage())
            ->body($this->getBody());
    }

    public function toDatabase($notifiable)
    {
        $this->notifiable = $notifiable;
        $this->locale($notifiable->locale);

        return [
            'title' => $this->getTitle(),
            'body' => $this->getBody(),
            'code' => $this->getCode(),
            'data' => $this->getData() ?: null,
            'sender_name' => $this->getSenderName(),
            'sender_image' => $this->getImage(),
        ];
    }

    public function getImage(): ?string
    {
        return asset('images/example.svg'); //push notification image
    }

    public function getSenderName(): ?string
    {
        return ''; // application name
    }

    public function getFcmData(): array
    {
        $data = $this->getData();
        return [
            'code' => $this->getCode(),
            'data' => $data ? json_encode($data) : null,
        ];
    }
}
