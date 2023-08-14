<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Mail\MailToUser;
use Illuminate\Support\Facades\Mail;
use App\Events\BookConfirmEvent;

class BookConfirmListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        // dd($event->book);
        $mail = new MailToUser($event->book);
        // $mailmessage = $mail->build();
        $mail->from('autorentz24@gmail.com', 'AutoRentz');
        $mail->subject('Reservation Confirmed');
        Mail::to($event->book->customer->user->email)->send($mail);
    }
}
