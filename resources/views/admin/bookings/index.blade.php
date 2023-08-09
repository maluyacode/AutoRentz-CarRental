@extends('admin.index')

@section('pageStyles')
    <link rel="stylesheet" href="{{ asset('css/bookings.css') }}">
@endsection
@section('content')
    @include('layouts.session-messages')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Bookings</h3>
                            <div style="float: right;" class="btn-group btn-group-toggle" data-toggle="buttons">
                                <label class="btn btn-outline-secondary active">
                                    <input class="booking-status" type="radio" name="options" value="all"
                                        id="all" autocomplete="off" checked> All
                                </label>
                                <label class="btn btn-outline-secondary">
                                    <input class="booking-status" type="radio" name="options" value="pendings"
                                        id="pendings" autocomplete="off"> Pendings
                                </label>
                                <label class="btn btn-outline-secondary">
                                    <input class="booking-status" type="radio" name="options" value="confirmed"
                                        id="confirmed" autocomplete="off"> Confirmed
                                </label>
                                <label class="btn btn-outline-secondary">
                                    <input class="booking-status" type="radio" name="options" value="finished"
                                        id="finished" autocomplete="off"> Finished
                                </label>
                                <label class="btn btn-outline-secondary">
                                    <input class="booking-status" type="radio" name="options" value="cancelled"
                                        id="cancelled" autocomplete="off"> Cancelled
                                </label>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="bookings-table" class="table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Customer</th>
                                        <th>Car</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Rent Price</th>
                                        {{-- <th>Transaction</th>
                                        <th>Locations(s)</th>
                                        <th>Drive Type</th> --}}
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>

                            {{-- {!! $dataTable->table() !!} --}}
                            {{-- <div class="modal fade bd-example-modal-sm" id="myModal" tabindex="-1" role="dialog"
                                aria-labelledby="mySmallModalLabel" aria-hidden="true" style="margin:auto">
                                <div class="modal-dialog modal-md">
                                    <form action="{{ route('confirmBooking', 0) }}" method="GET">
                                        @csrf
                                        <div class="modal-content"
                                            style="text-align:center; height:250px; padding:20px; font-size:20px">
                                            <input type="text" class="input-data data" name="booking_id" hidden>
                                            <label for="">Assign Driver</label>
                                            <select name="driver_id" class="data">
                                                @foreach ($drivers as $driver)
                                                    <option value="{{ $driver->id }}">{{ $driver->fname }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" style="width:50%; margin: auto">Submit</button>
                                        </div>
                                    </form>
                                </div>
                            </div> --}}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- {!! $dataTable->scripts() !!} --}}
    {{-- <script>
        $(document).on('click', '.btn-confirm-booking', function() {
            var bookingId = $(this).data('booking-id');
            $('input[name="booking_id"]').val(bookingId);
            $('#myModal').modal('show');
        });
    </script> --}}
@endsection

@section('pageScripts')
    <script src="{{ asset('js/bookings.js') }}"></script>
@endsection
