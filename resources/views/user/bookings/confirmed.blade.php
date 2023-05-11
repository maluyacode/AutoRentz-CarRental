@extends('user.bookings.navigation')

<style>
    th {
        text-align: center;
    }

    td {
        text-transform: capitalize;
        font-size: 80%;
    }
</style>
@section('booking')
    @include('layouts.session-messages')
    <div class="container" style=" width:100%; align-content:center">
        <section id="pending-bookings" style="margin:0;width:100%; height:100%">
            <h2>Confirm Bookings</h2>
            <table>
                <thead>
                    <tr>
                        <th>Booking #</th>
                        <th>Car Model</th>
                        <th>Pickup Date</th>
                        <th>Return Date</th>
                        <th>Transaction</th>
                        <th>Location(s)</th>
                        <th>Drive Type</th>
                        <th>Total Price</th>
                        <th>Reciept</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($confirms as $confirm)
                        <tr>
                            <td>{{ $confirm->id }}</td>

                            <td>
                                <a href="{{ route('cardetails', $confirm->car_id) }}">
                                    {{ $accessInfo->car($confirm->car_id)->modelname }}
                                    {{ $accessInfo->car($confirm->car_id)->modelyear }} <br>
                                    {{ $accessInfo->car($confirm->car_id)->typename }}
                                    {{ $accessInfo->car($confirm->car_id)->manufacturername }}
                                </a>
                            </td>
                            <td>{{ $accessInfo->formatDate($confirm->start_date) }}</td>
                            <td>{{ $accessInfo->formatDate($confirm->end_date) }}</td>
                            <td>
                                @if ($confirm->address)
                                    Delivery
                                @else
                                    Pickup
                                @endif
                                <br>{{ $accessInfo->countDays($confirm->start_date, $confirm->end_date) }} day(s)
                            </td>
                            <td>
                                @if ($confirm->address)
                                    {{ $confirm->address }}
                                @else
                                    <strong>Pickup Location:</strong><br>
                                    {{ $accessInfo->picklocation($confirm->pickup_location_id) }} <br>
                                    <strong>Return Location:</strong><br>
                                    {{ $accessInfo->returnlocation($confirm->return_location_id) }}
                                @endif

                            </td>
                            <td>
                                @if ($confirm->driver_id)
                                    With Driver: <a href="#">{{ $accessInfo->driverInfo($confirm->driver_id) }}</a>
                                @else
                                    Self Drive
                                @endif
                            </td>
                            <td>
                                PHP
                                {{ $customerClass->computationDisplay($confirm->start_date, $confirm->end_date, $confirm->price_per_day, $accessory, $confirm->car_id) }}
                            </td>
                            <td>
                                <a href="{{ route('print', $confirm->id) }}">Download</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

    </div>
@endsection
