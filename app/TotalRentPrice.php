<?php

namespace App;

class TotalRentPrice
{
    private $_booking;

    public function __construct($booking)
    {
        $this->_booking = $booking;
    }

    public function compute()
    {
        $accessoriesFee = $this->_booking->car->accessories->map(function ($accessory) {
            return $accessory->fee;
        })->sum();

        $days = date_diff(
            date_create($this->_booking->start_date),
            date_create($this->_booking->end_date)
        )->format('%a') + 1;
        return ($this->_booking->car->price_per_day + $accessoriesFee) * $days;
    }
}
