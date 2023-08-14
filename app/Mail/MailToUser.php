<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
// use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF;


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
        $carPrice = $additionalFee + $this->book->car->price_per_day;
        $total = $carPrice * $days;

        $pdf = app(PDF::class);
        $view = view('print.transaction', ['book' => $this->book, 'total' => $total, 'carPrice' => $carPrice]);
        $pdf->loadHTML($view->render());
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions(['defaultFont' => 'Arial']);

        return $this->view('mail-formats.admin-confirmed', ['book' => $this->book, 'total' => $total])
            ->attachData($pdf->output(), 'invoice.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
