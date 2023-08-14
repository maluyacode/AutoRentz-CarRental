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
                <div class="col col-image" decoding="async">
                    <img src="{{ asset('/storage/images/lightning.png') }}" alt="">
                </div>
            </div>
        </div>
    </section>
    <section class="shop" id="list-cars">
        <div class="container-fluid shop-container">
            <div class="row">
                <div class="row js-car-list">

                </div>
            </div>
        </div>
    </section>
@endsection
@section('scripts')
    <script src="{{ asset('/js/car-listing.js') }}"></script>
@endsection
