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
            @if ($pendings)
                <h2>Pending Bookings</h2>
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
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pendings as $pending)
                            <tr>
                                <td>{{ $pending->id }}</td>

                                <td>
                                    <a href="{{ route('cardetails', $pending->car_id) }}">
                                        {{ $accessInfo->car($pending->car_id)->modelname }}
                                        {{ $accessInfo->car($pending->car_id)->modelyear }} <br>
                                        {{ $accessInfo->car($pending->car_id)->typename }}
                                        {{ $accessInfo->car($pending->car_id)->manufacturername }}
                                    </a>
                                </td>
                                <td>{{ $accessInfo->formatDate($pending->start_date) }}</td>
                                <td>{{ $accessInfo->formatDate($pending->end_date) }}</td>
                                <td>
                                    @if ($pending->address)
                                        Delivery
                                    @else
                                        Pickup
                                    @endif
                                    <br>{{ $accessInfo->countDays($pending->start_date, $pending->end_date) }} day(s)
                                </td>
                                <td>
                                    @if ($pending->address)
                                        {{ $pending->address }}
                                    @else
                                        <strong>Pickup Location:</strong><br>
                                        {{ $accessInfo->picklocation($pending->pickup_location_id) }} <br>
                                        <strong>Return Location:</strong><br>
                                        {{ $accessInfo->returnlocation($pending->return_location_id) }}
                                    @endif

                                </td>
                                <td>
                                    @if ($pending->driver_id)
                                        With Driver
                                    @else
                                        Self Drive
                                    @endif
                                </td>
                                <td>
                                    PHP
                                    {{ $customerClass->computationDisplay($pending->start_date, $pending->end_date, $pending->price_per_day, $accessory, $pending->car_id) }}
                                </td>
                                <td>
                                    <div style="display: flex; flex-direction: row;">
                                        <a href="{{ route('edit', $pending->id) }}">
                                            <button class="btn btn-danger"
                                                style="height: 35px; margin: 5px; width:60px">Edit</button>
                                        </a>
                                        <button type="submit"
                                            style="background-color:red; height: 35px; margin: 5px; width:60px"
                                            class="cancel-button">Cancel</button>
                                        <input type="text" class="book-id" value="{{ $pending->id }}" hidden>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <h2 style="text-align: center; margin-top: 200;">No Pendings</h2>
            @endif
        </section>

    </div>
    <script>
        const deleteBookingButtons = document.querySelectorAll('.cancel-button');

        deleteBookingButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const ids = this.parentElement.querySelector('.book-id');
                const id = ids.value;
                console.log(id);
                const url = '/user/booking/cancel/' + id;
                console.log(url);
                Swal.fire({
                    title: 'Confirmation',
                    text: 'Are you sure you want to cancel this booking?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Redirect user to booking route

                        window.location.href = url
                    }
                });
            });
        });
    </script>
@endsection
