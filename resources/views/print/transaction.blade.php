<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Auto Rentz Invoice</title>

    <style>
        html,
        body {
            margin: 10px;
            padding: 10px;
            font-family: sans-serif;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        p,
        span,
        label {
            font-family: sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0px !important;
        }

        table thead th {
            height: 28px;
            text-align: left;
            font-size: 16px;
            font-family: sans-serif;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 14px;
        }

        .heading {
            font-size: 24px;
            margin-top: 12px;
            margin-bottom: 12px;
            font-family: sans-serif;
        }

        .small-heading {
            font-size: 18px;
            font-family: sans-serif;
        }

        .total-heading {
            font-size: 18px;
            font-weight: 700;
            font-family: sans-serif;
        }

        .order-details tbody tr td:nth-child(1) {
            width: 20%;
        }

        .order-details tbody tr td:nth-child(3) {
            width: 20%;
        }

        .text-start {
            text-align: left;
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .company-data span {
            margin-bottom: 4px;
            display: inline-block;
            font-family: sans-serif;
            font-size: 14px;
            font-weight: 400;
        }

        .no-border {
            border: 1px solid #fff !important;
        }

        .bg-blue {
            background-color: #414ab1;
            color: #fff;
        }
    </style>
</head>

<body>

    <table class="order-details">
        <thead>
            <tr>
                <th width="50%" colspan="2">
                    <h2 class="text-start">AutoRentz</h2>
                </th>
                <th width="50%" colspan="2" class="text-end company-data">
                    <span>Book Id: #{{ $book->id }}</span> <br>
                    <span>Booking Date: {{ $accessInfo->formatDate($book->created_at) }}</span> <br>
                    <span>Confirmed Date: : {{ $accessInfo->formatDate($book->updated_at) }}</span> <br>
                </th>
            </tr>
            <tr class="bg-blue">
                <th width="50%" colspan="2">Booking Details</th>
                <th width="50%" colspan="2">Customer Details</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Book Id:</td>
                <td>{{ $book->id }}</td>

                <td>Full Name:</td>
                <td>{{ $customer->name }}</td>
            </tr>
            <tr>
                <td>Start Date:</td>
                <td>{{ $accessInfo->formatDate($book->start_date) }}</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>End Date:</td>
                <td>{{ $accessInfo->formatDate($book->end_date) }}</td>

                <td>Email Address:</td>
                <td>{{ $customer->email }}</td>
            </tr>
            <tr>
                @if (!$book->address)
                    <td>Pickup Location:</td>
                    <td>{{ $accessInfo->picklocation($book->pickup_location_id) }}</td>
                @else
                    <td></td>
                    <td></td>
                @endif

                <td>Phone:</td>
                <td>{{ $customer->phone }}</td>
            </tr>
            <tr>
                @if (!$book->address)
                    <td>Return Location:</td>
                    <td>{{ $accessInfo->returnlocation($book->return_location_id) }}</td>
                @else
                    <td></td>
                    <td></td>
                @endif

                <td>Address:</td>
                <td>{{ $customer->address }}</td>
            </tr>
            <tr>
                @if ($book->address)
                    <td>Delivery Address:</td>
                    <td>{{ $book->address }}</td>
                @else
                    <td></td>
                    <td></td>
                @endif
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>Status:</td>
                <td style="text-transform: capitalize">{{ $book->status }}</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                @if ($book->driver_id)
                    <td>Driver:</td>
                    <td style="text-transform: capitalize"><a href="">
                            {{ $driver->fname }} {{ $driver->lname }} -
                            Licensed NO:
                            {{ $driver->licensed_no }}
                        </a>
                    </td>
                @else
                    <td>Driver:</td>
                    <td style="text-transform: capitalize">Self Drive
                    </td>
                @endif
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <table>
        <thead>
            <tr>
                <th class="no-border text-start heading" colspan="5">
                    Car Rented
                </th>
            </tr>
            <tr class="bg-blue">
                <th>Plate Number</th>
                <th>Car Model</th>
                <th>Seat Capacity</th>
                <th>Transmission Type</th>
                <th>Fuel Type</th>
                <th>Price Per Day</th>
                <th>Days Rented</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $car->platenumber }}</td>
                <td>
                    <a href="">{{ $car->modelname }} {{ $car->modelyear }} {{ $car->typename }} -
                        {{ $car->manufacturername }}
                    </a>
                </td>
                <td>{{ $car->seats }}</td>
                <td>{{ $car->transname }}</td>
                <td style="text-transform: capitalize">{{ $car->fuelname }}</td>
                <td>{{ $car->price_per_day }}</td>
                <td>{{ $accessInfo->countDays($book->start_date, $book->end_date) }}</td>
            </tr>
            <tr>
                <td colspan="4" class="total-heading">Total Rent Price</td>
                <td colspan="5" class="total-heading">PHP {{ $totalPrice }}</td>
            </tr>
        </tbody>
    </table>

    <br>
    <p class="text-center">
        Thank your for using AutoRentz
    </p>

</body>

</html>
