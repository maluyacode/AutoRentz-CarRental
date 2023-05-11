@extends('user.bookings.navigation')

@section('booking')

@section('booking')
    @include('layouts.session-messages')
    <div class="container" style=" width:100%; align-content:center; height:90vh">
        <section id="pending-bookings" style="margin:0;width:100%; height:100%">
            {{-- @if ($cancelled) --}}
            <h2>Finished Bookings</h2>
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
                    @foreach ($finished as $finish)
                        <tr>
                            <td>{{ $finish->id }}</td>

                            <td>
                                <a href="{{ route('cardetails', $finish->car_id) }}">
                                    {{ $accessInfo->car($finish->car_id)->modelname }}
                                    {{ $accessInfo->car($finish->car_id)->modelyear }} <br>
                                    {{ $accessInfo->car($finish->car_id)->typename }}
                                    {{ $accessInfo->car($finish->car_id)->manufacturername }}
                                </a>
                            </td>
                            <td>{{ $accessInfo->formatDate($finish->start_date) }}</td>
                            <td>{{ $accessInfo->formatDate($finish->end_date) }}</td>
                            <td>
                                @if ($finish->address)
                                    Delivery
                                @else
                                    Pickup
                                @endif
                                <br>{{ $accessInfo->countDays($finish->start_date, $finish->end_date) }} day(s)
                            </td>
                            <td>
                                @if ($finish->address)
                                    {{ $finish->address }}
                                @else
                                    <strong>Pickup Location:</strong><br>
                                    {{ $accessInfo->picklocation($finish->pickup_location_id) }} <br>
                                    <strong>Return Location:</strong><br>
                                    {{ $accessInfo->returnlocation($finish->return_location_id) }}
                                @endif

                            </td>
                            <td>
                                @if ($finish->driver_id)
                                    With Driver
                                @else
                                    Self Drive
                                @endif
                            </td>
                            <td>
                                PHP
                                {{ $customerClass->computationDisplay($finish->start_date, $finish->end_date, $finish->price_per_day, $accessory, $finish->car_id) }}
                            </td>
                            <td>
                                <div style="display: flex; flex-direction: row;">
                                    <button type="submit" style="background-color:red; height: 40px;"
                                        class="finish-button">Remove</button>
                                    <input type="text" class="book-id" value="{{ $finish->id }}" hidden>
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
        const deleteBookingButtons = document.querySelectorAll('.finish-button');

        deleteBookingButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const ids = this.parentElement.querySelector('.book-id');
                const id = ids.value;
                // console.log(id);
                const url = '/user/booking/remove/cancelled/' + id;
                // console.log(url);
                Swal.fire({
                    title: 'Confirmation',
                    text: 'Are you sure you want to this record?',
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
