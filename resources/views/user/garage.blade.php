@extends('layouts.app')

@section('content')
    {{-- {{ dd($garage) }} --}}
    @include('layouts.session-messages')
    <div class="container" style="height: 80vh">
        <h1>My Garage</h1>

        @if ($garage == null)
            <div class="container" style="width:100%flex; text-align:center; ">
                <h2 style="width:auto; height:0px; margin:170px auto">Your garage is empty.</h2>
            </div>
        @else
            <div class="row" style="font-size: 18px">
                @foreach ($garage as $key => $bookInfo)
                    {{-- {{ dd($bookInfo) }} --}}
                    <div class="col-md-12 mb-4">
                        <div class="card">
                            <div class="row no-gutters">
                                <div class="col-md-4">
                                    <div class="row" style="margin: 5%">
                                        @foreach ($images[] = explode('=', $carDetails->CarDetails($bookInfo['car_id'])->image_path) as $key => $image)
                                            <a href="{{ route('cardetails', $bookInfo['car_id']) }}">
                                                <img src="{{ '/storage/images/' . $image }}" alt="car-image" height="200px">
                                            </a>
                                        @break
                                    @endforeach
                                    {{--
                                        <img class="card-img"
                                            src="{{ asset($carDetails->CarDetails($bookInfo['car_id'])->image_path) }}"> --}}
                                </div>
                                <div class="row" style="margin: 5%">
                                    <p class="card-title" style="text-transform: capitalize; width:auto">
                                        Seating capacity:
                                        <strong>{{ $carDetails->CarDetails($bookInfo['car_id'])->seats }}</strong>
                                        people
                                    <ul style="margin: 0px 20px; display:flex; flex-direction:row; flex-wrap: wrap; ">
                                        @foreach ($accessory->where('car_id', $bookInfo['car_id']) as $accessories)
                                            <li style="margin:0px 15px"> {{ $accessories->name }}</li>
                                        @endforeach
                                    </ul>
                                    </p>
                                </div>

                            </div>
                            <div class="col-md-8">
                                <div class="card-body">
                                    <div style="display: flex">
                                        <h4 class="card-title" style="text-transform: capitalize; width:auto">
                                            {{ $carDetails->CarDetails($bookInfo['car_id'])->modelname }}
                                            {{ $carDetails->CarDetails($bookInfo['car_id'])->modelyear }}
                                            {{ $carDetails->CarDetails($bookInfo['car_id'])->typename }}
                                        </h4>
                                        <small
                                            style="margin: 0 5px">{{ $carDetails->CarDetails($bookInfo['car_id'])->manufacturername }}
                                        </small>
                                    </div>
                                    <p class="card-text">{{ $carDetails->CarDetails($bookInfo['car_id'])->description }}
                                    </p>
                                    <p class="card-text" style="margin-bottom: 5px ">
                                        @include('user.book-details')
                                    </p>
                                    <p class="card-text">
                                        <strong>Transaction</strong>
                                        @if ($bookInfo['address'])
                                            <strong>- Delivery: </strong> {{ $bookInfo['address'] }}
                                        @elseif(!$bookInfo['address'] && ($bookInfo['return_id'] && $bookInfo['pick_id']))
                                            <strong>- Pickup: </strong> <br>
                                            <b>Pickup Location </b> -
                                            {{ $location->accessLocation($bookInfo['pick_id'])->street }},
                                            {{ $location->accessLocation($bookInfo['pick_id'])->baranggay }}
                                            {{ $location->accessLocation($bookInfo['pick_id'])->city }} <br>
                                            <b> Return Location </b> -
                                            {{ $location->accessLocation($bookInfo['return_id'])->street }}
                                            {{ $location->accessLocation($bookInfo['pick_id'])->baranggay }}
                                            {{ $location->accessLocation($bookInfo['pick_id'])->city }}
                                        @else
                                            - <em> Please Specify</em>
                                        @endif

                                    </p>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <p class="card-text"><strong>Price:</strong>
                                                ₱{{ number_format($carDetails->computationDisplay(null, null, $carDetails->CarDetails($bookInfo['car_id'])->price_per_day, $accessory, $bookInfo['car_id']), 0, '.', ',') }}
                                                per day
                                            </p>
                                        </div>
                                        <div class="col-md-8" style="text-align: right">
                                            <h4 class="card-text"><strong>Total Price:</strong>
                                                ₱{{ number_format($carDetails->computationDisplay($bookInfo['start_date'], $bookInfo['end_date'], $carDetails->CarDetails($bookInfo['car_id'])->price_per_day, $accessory, $bookInfo['car_id']), 0, '.', ',') }}
                                            </h4>
                                        </div>
                                    </div>
                                    <div style="margin: 10px 0px">
                                        <button class="btn btn-danger delete-button">Remove</button>
                                        <a href="{{ route('editgarage', $bookInfo['car_id']) }}"
                                            class="btn btn-primary">Edit</a>
                                        <button class="btn btn-success confirm-button">Book Now</button>
                                        <input value="{{ $bookInfo['car_id'] }}" hidden class="car-id">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            @include('layouts.confirmation')
        </div>
    @endif
</div>
@endsection
