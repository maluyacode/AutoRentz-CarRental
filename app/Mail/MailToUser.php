<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\CustomerClass;
use App\Models\Booking as Book;
use App\AccessInformation;
use App\Models\Customer;
use App\Models\Driver;
use Illuminate\Support\Facades\DB;

class MailToUser extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $book;
    public function __construct($book)
    {
        $this->book = $book;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $additionalFee = $this->book->car->accessories->map(function ($accessory) {
            return $accessory->fee;
        })->sum();
        $days = date_diff(
            date_create($this->book->end_date),
            date_create($this->book->start_date)
        )->format('%a') + 1;
        $total = ($additionalFee + $this->book->car->price_per_day) * $days;

        return $this->view('mail-formats.admin-confirmed', ['book' => $this->book, 'total' => $total]);
    }
}
