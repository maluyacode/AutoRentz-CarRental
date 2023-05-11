@extends('layouts.app')

@section('content')
    <section class="h-100 gradient-custom-2">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col col-lg-9 col-xl-7">
                    <div class="card">
                        <div class="rounded-top text-white d-flex flex-row" style="background-color: #000; height:200px;">
                            <div class="ms-4 mt-5 d-flex flex-column" style="width: 150px;">
                                @foreach (explode('=', $driver->image_path) as $key => $image)
                                    <img src="{{ '/storage/images/' . $image }}" alt="Generic placeholder image"
                                        class="img-fluid img-thumbnail mt-4 mb-2" style="width: 150px; z-index: 1;">
                                @break
                            @endforeach
                        </div>
                        <div class="ms-3" style="margin-top: 130px;">
                            <h5>{{ $driver->fname . ' ' . $driver->lname }}</h5>
                            <p>{{ $driver->address }}</p>
                        </div>
                    </div>
                    <div class="p-4 text-black" style="background-color: #f8f9fa;">
                        <div class="d-flex justify-content-end text-center py-1">
                            <div>
                                <p class="mb-1 h5" style="text-transform: capitalize">{{ $driver->driver_status }}</p>
                                <p class="small text-muted mb-0">Status</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4 text-black">
                        <div class="mb-5">
                            <p class="lead fw-normal mb-1">Description</p>
                            <div class="p-4" style="background-color: #f8f9fa;">
                                <p class="font-italic mb-1">{{ $driver->description }}</p>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <p class="lead fw-normal mb-0">Images</p>
                        </div>
                        <div class="row g-2">
                            @foreach (explode('=', $driver->image_path) as $key => $image)
                                <div class="col mb-2">
                                    <img src="{{ '/storage/images/' . $image }}" alt="image 1" class="w-100 rounded-3">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
