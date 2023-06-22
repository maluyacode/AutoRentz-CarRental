@extends('layouts.app')

@section('content')
    @include('layouts.session-messages')
    <section class="hero">
        <div class="container home-container">
            <div class="row" style="height: 100%">
                <div class="col-md-6 col-content">
                    <div class="hero-content">
                        <h1 class="home-title">Explore the World in Style</h1>
                        <p>Rent the car of your dreams and embark on an unforgettable adventure</p>
                        <a href="#list-cars">
                            <button class="btn-rent">Rent</button>
                        </a>
                    </div>
                </div>
                <div class="col col-image">
                    <img src="{{ asset('/storage/images/lightning.png') }}" alt="">
                </div>
            </div>
        </div>
    </section>
    <section class="shop" id="list-cars">
        <div class="container-fluid shop-container">
            <div class="row">
                <div class="col-md-12">
                    <form action="{{ route('carsearch') }}" method="GET" id="car-search-form">
                        <div class="input-group mb-3 search-row">
                            <input type="text" class="form-control search-input" placeholder="Search here..."
                                name="search" id="car-search-input" onkeyup="searchObjects()">
                            <button class="btn btn-warning btn-search" type="submit">Search</button>
                        </div>
                    </form>
                </div>
                <div class="row js-car-list">
                    {{-- @foreach ($cars as $car)
                        <div style="width: 400px; margin-bottom: 40px">
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
                                <a href="{{ route('addtogarage', $car->id) }}" class="add-to-garage"
                                    style="font-size: 12px">Add to garage</a>
                                <a href="{{ route('cardetails', $car->id) }}" class="car-details"
                                    style="font-size: 12px">View Details</a>
                            </div>
                        </div>
                    </div>
                @endforeach --}}
                </div>
            </div>
        </div>
    </section>
    @include('fleet.js-car-search')
@endsection
