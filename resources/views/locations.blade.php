@extends('layouts.app')

@section('styles')
    <link href="{{ asset('css/location.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="container-fluid location">
        <div class="row">
            <form action="{{ route('locations', 'search') }}" method="POST" class="search-location">
                @csrf
                <div class="form-group">
                    <input type="text" class="form-control search-input-location" name="searchInput">
                </div>
                <button type="submit" class="btn btn-warning btn-location">Search</button>
            </form>
        </div>
        <div class="row row-location">
            @foreach ($locations as $location)
                <div class="col-md-4 location-container">
                    <div class="card container">
                        <div class="slider">
                            @foreach (explode('=', $location->image_path) as $key => $image)
                                <div class="slide">
                                    <img class="card-img-top" src="{{ '/storage/images/' . $image }}" alt="Card image cap">
                                </div>
                            @endforeach
                            <button class="prev btn btn-dark">&#60;</button>
                            <button class="next btn btn-dark">&#62;</button>
                        </div>
                        <div class="card-body">
                            <p class="card-text">
                                <span><strong>{{ $location->street }} -</strong></span>
                                <span><strong>{{ $location->baranggay }} -</strong></span>
                                <span><strong>{{ $location->city }}</strong></span>
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/location.js') }}" defer></script>
@endsection
