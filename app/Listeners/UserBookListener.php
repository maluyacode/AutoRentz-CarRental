<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\UserBookEvent;
use App\Mail\Booking as MailBooking;
use Illuminate\Support\Facades\Mail;

class UserBookListener
{

    public function __construct()
    {
        //
    }

    public function handle($event)
    {
        $mail = new MailBooking($event->book);
        $mailmessage = $mail->build();
        $mailmessage->from($event->email, $event->name);
        Mail::to('autorentz24@gmail.com')->send($mailmessage);
    }
}
