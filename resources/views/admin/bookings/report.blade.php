@extends('admin.index')

@section('header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Date Range Report</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Report</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection
@include('layouts.session-messages')
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card" style="height: 70vh">
                        <div class="card-header">
                            <a href="{{ route('report') }}" class="btn bg-gradient-gray">
                                Refresh
                            </a>
                            </h3>
                            <div class="card-tools">
                                <div class="input-group input-group-sm" style="width: 430px;">
                                    <form action="{{ route('report') }}">
                                        @csrf
                                        <div class="form-row">
                                            <div class="col">
                                                <input type="date" class="form-control" name="start_date">
                                            </div>
                                            <div class="col">
                                                <input type="date" class="form-control" placeholder="Last name"
                                                    name="end_date">
                                            </div>
                                            <button type="submit" class="btn btn-info">Search</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive p-0" style="height: 300px;">
                            <table class="table table-head-fixed text-nowrap">
                                <thead>
                                    <tr>
                                        <th>Book ID</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>MOD</th>
                                        <th>Customer</th>
                                        <th>Car</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bookings as $booking)
                                        <tr>
                                            <td>{{ $booking->id }}</td>
                                            <td>{{ $booking->start_date }}</td>
                                            <td>{{ $booking->end_date }}</td>
                                            @if ($booking->address)
                                                <td>Delivery: {{ $booking->address }}</td>
                                            @else
                                                <td>
                                                    Pickup: {{ $booking->pickuplocation }} <br>
                                                    Return: {{ $booking->returnlocation }}
                                                </td>
                                            @endif
                                            <td>{{ $booking->customer_name }}</td>
                                            <td>{{ $info->concatCarName($booking->car_id) }}</td>
                                            <td>₱{{ number_format($compute->computationDisplay($booking->start_date, $booking->end_date, $booking->price_per_day, $accessory, $booking->car_id), 2, '.', ',') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <h3 class="m-0" style="text-align: right">Total: ₱{{ number_format($totalPrice, 2, '.', ',') }}</h3>
                    <!-- /.card -->
                </div>
            </div>
        </div>
    </section>
@endsection
