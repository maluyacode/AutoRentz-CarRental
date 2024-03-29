<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <title>New Car Rental Booking Received</title>
    <style>
        /* Email Body */

        body {
            font-family: Arial, sans-serif;
            font-size: 16px;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 0;
        }

        /* Email Header */

        header {
            background-color: #1e88e5;
            color: #fff;
            padding: 20px;
            text-align: center;
        }

        header h1 {
            font-size: 24px;
            margin: 0;
        }

        /* Email Content */

        .content {
            padding: 20px;
            text-align: center;
        }

        .booking-details {
            margin-top: 20px;
            background-color: #f2f2f2;
            border-radius: 5px;
            padding: 10px;
        }

        .booking-details h2 {
            font-size: 20px;
            margin-top: 0;
        }

        .booking-details dl {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 0;
        }

        .booking-details dt {
            font-weight: bold;
        }

        .booking-details dd {
            margin: 0;
        }

        /* Email Footer */

        footer {
            background-color: #333;
            color: #fff;
            padding: 20px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <h1>New Car Rental Booking Received</h1>
        </header>
        <div class="content">
            <p>Dear Admin,</p>
            <p>A new car rental booking has been received on {{ config('app.name') }}:</p>
            <div class="booking-details">
                <h2>Customer Details</h2>
                <dl>
                    <dt>Name:</dt>
                    <dd>{{ $book->customer->name }}</dd>
                    <dt>Email:</dt>
                    <dd>{{ $book->customer->user->email }}</dd>
                    <dt>Phone:</dt>
                    <dd>{{ $book->customer->phone }}</dd>
                </dl>
                <h2>Booking Details</h2>
                <dl>
                    <dt>Booking ID:</dt>
                    <dd>{{ $book->id }}</dd>
                    <dt>Plate Number:</dt>
                    <a href="#">
                        <dd>{{ $book->car->platenumber }}</dd>
                    </a>
                    <dt>Car Model:</dt>
                    <a href="#">
                        <dd>{{ $book->car->modelo->name }}, {{ $book->car->modelo->year }},
                            {{ $book->car->modelo->type->name }} -
                            {{ $book->car->modelo->manufacturer->name }}</dd>
                    </a>
                    <dt>Pickup Date:</dt>
                    <dd>{{ $book->start_date }}</dd>
                    <dt>Return Date:</dt>
                    <dd>{{ $book->end_date }}</dd>
                    @if ($book->address)
                        <dt>Delivery Address:</dt>
                        <dd>{{ $book->address }}</dd>
                    @else
                        <dt>PickUp Location:</dt>
                        <dd>{{ $book->picklocation->street }} {{ $book->picklocation->baranggay }}
                            {{ $book->picklocation->city }}</dd>
                        <dt>Return Lacation:</dt>
                        <dd>{{ $book->returnlocation->street }} {{ $book->returnlocation->baranggay }}
                            {{ $book->returnlocation->city }}</dd>
                    @endif
                    <dt>Drive Type:</dt>
                    @if ($book->driver_id)
                        <dd>With Driver: Need to assign</dd>
                    @else
                        <dd>Self Drive</dd>
                    @endif
                </dl>
                <a href="{{ route('adminPendings') }}">{{ config('app.name') }} Pendings</a>
            </div>
            <p>Please login to your admin dashboard for more details and to confirm the booking.</p>
            <p>Thank you for using our car rental service!</p>
        </div>
        <footer>
            <p>&copy; 2023 AutoRentz. All rights reserved.</p>
        </footer>
    </div>
</body>

</html>
