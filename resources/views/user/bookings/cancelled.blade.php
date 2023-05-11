@extends('user.bookings.navigation')

@section('booking')

@section('booking')
    @include('layouts.session-messages')
    <div class="container" style=" width:100%; align-content:center; height:90vh">
        <section id="pending-bookings" style="margin:0;width:100%; height:100%">
            {{-- @if ($cancelled) --}}
                <h2>Cancelled Bookings</h2>
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
                        @foreach ($cancelled as $cancell)
                            <tr>
                                <td>{{ $cancell->id }}</td>

                                <td>
                                    <a href="{{ route('cardetails', $cancell->car_id) }}">
                                        {{ $accessInfo->car($cancell->car_id)->modelname }}
                                        {{ $accessInfo->car($cancell->car_id)->modelyear }} <br>
                                        {{ $accessInfo->car($cancell->car_id)->typename }}
                                        {{ $accessInfo->car($cancell->car_id)->manufacturername }}
                                    </a>
                                </td>
                                <td>{{ $accessInfo->formatDate($cancell->start_date) }}</td>
                                <td>{{ $accessInfo->formatDate($cancell->end_date) }}</td>
                                <td>
                                    @if ($cancell->address)
                                        Delivery
                                    @else
                                        Pickup
                                    @endif
                                    <br>{{ $accessInfo->countDays($cancell->start_date, $cancell->end_date) }} day(s)
                                </td>
                                <td>
                                    @if ($cancell->address)
                                        {{ $cancell->address }}
                                    @else
                                        <strong>Pickup Location:</strong><br>
                                        {{ $accessInfo->picklocation($cancell->pickup_location_id) }} <br>
                                        <strong>Return Location:</strong><br>
                                        {{ $accessInfo->returnlocation($cancell->return_location_id) }}
                                    @endif

                                </td>
                                <td>
                                    @if ($cancell->driver_id)
                                        With Driver
                                    @else
                                        Self Drive
                                    @endif
                                </td>
                                <td>
                                    PHP
                                    {{ $customerClass->computationDisplay($cancell->start_date, $cancell->end_date, $cancell->price_per_day, $accessory, $cancell->car_id) }}
                                </td>
                                <td>
                                    <div style="display: flex; flex-direction: row;">
                                        <button type="submit"
                                            style="background-color:red; height: 40px;"
                                            class="cancel-button">Remove</button>
                                        <input type="text" class="book-id" value="{{ $cancell->id }}" hidden>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            {{-- @else
                <h2 style="text-align: center; margin-top: 200;">No cancelled</h2>
            @endif --}}
        </section>

    </div>
    <script>
        const deleteBookingButtons = document.querySelectorAll('.cancel-button');

        deleteBookingButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const ids = this.parentElement.querySelector('.book-id');
                const id = ids.value;
                // console.log(id);
                const url = '/user/booking/remove/cancelled/' + id;
                // console.log(url);
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


@endsection
