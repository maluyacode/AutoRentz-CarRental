@extends('layouts.app')

@section('content')
    @include('layouts.session-messages')
    <section class="hero">
        <div class="hero-content">
            <h1>Explore the World in Style</h1>
            <p>Rent the car of your dreams and embark on an unforgettable adventure</p>
        </div>
    </section>
    <section class="shop">
        <div class="container">
            <h2>AutoRentz Cars</h2>
            <div class="row">
                <div class="col-md-12">
                    <form action="{{ route('carsearch') }}" method="GET" id="car-search-form">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Search for a car by any name"
                                name="search" id="car-search-input">
                            <button class="btn btn-primary" type="submit">Search</button>
                        </div>
                    </form>
                </div>
                @foreach ($cars as $car)
                    <div style="width: 400px; margin-bottom: 20px">
                        <div class="shop-item box" style="font-family: serif; font-weight: 200; height:100%">
                            <div class="ribbon {{ $car->car_status == 'taken' ? 'red' : '' }}">
                                <span>{{ $car->car_status == 'taken' ? 'Taken' : 'Available' }}</span>
                            </div>
                            <div style="display:flex; flex-direction:row; justify-content: space-between;">
                                <h5>₱{{ number_format($customerClass->computationDisplay(null, null, $car->price_per_day, $accessory, $car->id), 0, '.', ',') }}
                                    per day</h5>
                            </div>
                            <div class="shop-item-image">
                                @foreach ($images[] = explode('=', $car->image_path) as $key => $image)
                                    <img src="{{ '/storage/images/' . $image }}" alt="car-image" height="200px">
                                @break
                            @endforeach
                        </div>
                        <div class="shop-item-details">
                            <div>
                                <h3 style="margin: 0; text-transform: capitalize">
                                    {{ $car->modelname . ' ' . $car->typename }}</h3>
                                <small>{{ $car->manufacturername }}</small>
                            </div>
                            <a href="{{ route('addtogarage', $car->id) }}" class="btn-secondary"
                                style="font-size: 12px">Add to garage</a>
                            <a href="{{ route('cardetails', $car->id) }}" class="btn-secondary"
                                style="font-size: 12px">View Details</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
