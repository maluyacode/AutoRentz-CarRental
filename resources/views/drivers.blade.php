@extends('layouts.app')

@section('styles')
    <link href="{{ asset('css/driver.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="container-fluid driver-container">
        <div class="row">
            <form action="{{ route('drivers', 'search') }}" method="POST" class="driver-search-form">
                @csrf
                <div class="form-group">
                    <input type="text" class="form-control driver-search-input" name="searchInput">
                </div>
                <button type="submit" class="btn btn-warning btn-driver">Search</button>
            </form>
        </div>
        <div class="row driver-row">
            @foreach ($drivers as $driver)
                <div class="card mb-1 driver-card">
                    <div class="row no-gutters" >
                        <div class="col-md-4">
                            @foreach (explode('=', $driver->image_path) as $key => $image)
                                <img src="{{ '/storage/images/' . $image }}" class="driver-image" alt="...">
                            @break
                        @endforeach
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h5 class="card-title">{{ $driver->fname . ' ' . $driver->lname }}</h5>
                            <p class="card-text">{{ $driver->description }}.</p>
                            <a href="{{ route('driver.details', $driver->id) }}" class="btn btn-success">See profile</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
@endsection

@section('scripts')
<script src="{{ asset('js/driver.js') }}" defer></script>
@endsection
