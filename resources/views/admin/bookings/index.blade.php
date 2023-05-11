@extends('admin.index')

@section('header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ $header }}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Manage Bookings</a></li>
                        <li class="breadcrumb-item active">{{ $header }}</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection
@include('layouts.session-messages')
@section('content')
    <style>
        details {
            border: 1px solid #aaa;
            border-radius: 4px;
            padding: 0.5em 0.5em 0;
        }

        summary {
            font-weight: bold;
            margin: -0.5em -0.5em 0;
            padding: 0.5em;
        }

        details[open] {
            padding: 0.5em;
        }

        details[open] summary {
            border-bottom: 1px solid #aaa;
            margin-bottom: 0.5em;
        }

        .booking-class {
            text-transform: capitalize;
        }

        .data {
            height: 50px;
            margin: 10px
        }
        .status{
            font-size: 20px;
        }
    </style>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Rentahan ng kotse ni Earl Russell SY</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            {{-- <a href="{{ route('car.create') }}" class="btn btn-block bg-gradient-primary btn-sm"
                                style="width:150px; margin:5px">
                                Add New
                            </a> --}}
                            {!! $dataTable->table() !!}
                            <div class="modal fade bd-example-modal-sm" id="myModal" tabindex="-1" role="dialog"
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
                            </div>

                            {{-- <button type="button" class="btn btn-primary " data-toggle="modal"
                                data-target=".bd-example-modal-sm" value="2">Small modal</button> --}}
                            {{-- <button class="btn-data" ata-toggle="modal" data-target=".bd-example-modal-sm" value="2">sadsad</button> --}}
                        </div>
                        <!-- /.card-body -->
                    </div>
                </div>
            </div>
        </div>
    </section>
    {!! $dataTable->scripts() !!}
    <script>
        $(document).on('click', '.btn-confirm-booking', function() {
            var bookingId = $(this).data('booking-id');
            $('input[name="booking_id"]').val(bookingId);
            $('#myModal').modal('show');
        });
    </script>
@endsection
