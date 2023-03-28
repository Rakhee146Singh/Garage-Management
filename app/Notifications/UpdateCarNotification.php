<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;

class UpdateCarNotification extends Notification
{
    use Queueable, Notifiable;
    protected $car, $service, $owner;

    /**
     * Create a new notification instance.
     */
    public function __construct($car, $service, $owner)
    {
        $this->car = $car;
        $this->service = $service;
        $this->owner = $owner;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('Hello ' . $this->owner->first_name)
            ->line('Notification for Car Update')
            ->line('User Name : ' . $this->car->users->first_name)
            ->line('The user has Updated car with Service Id is :' . $this->service->id)
            ->line('My Car Details Are:')
            ->line('Car Id : ' . $this->car->id)
            ->line('Company Name : ' . $this->car->company_name)
            ->line('Model Name : ' . $this->car->model_name)
            ->line('manufacturing Year : ' . $this->car->manufacturing_year)
            ->line('Thank you for accepting updation!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
