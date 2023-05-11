@extends('layouts.app')

@section('content')
    <div class="row">
        <form action="{{ route('drivers', 'search') }}" method="POST" style="display: flex; justify-content: center;">
            @csrf
            <div class="form-group">
                <input type="text" class="form-control" style="width: 300px;" name="searchInput">
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>
    @foreach ($drivers as $driver)
        <div class="card mb-3" style="max-width: 640px;margin:30px auto">
            <div class="row no-gutters">
                <div class="col-md-4">
                    @foreach (explode('=', $driver->image_path) as $key => $image)
                        <img src="{{ '/storage/images/' . $image }}" class="card-img" alt="...">
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
@endsection
