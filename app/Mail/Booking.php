<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Booking as Book;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use App\AccessInformation;
use Illuminate\Support\Facades\Auth;

class Booking extends Mailable
{
    use Queueable, SerializesModels;

    public $book;

    public function __construct($book)
    {
        $this->book = $book;
    }

    public function build()
    {
        //eager loading
        $this->book->with([
            'customer',
            'customer.user',
            'car',
            'car.modelo',
            'car.modelo.type',
            'car.modelo.manufacturer',
            'picklocation',
            'returnlocation'
        ]);

        return $this->view('mail-formats.user-book', ['book' => $this->book]);
    }
}
