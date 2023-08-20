@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/car-details.css') }}">
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="card mb-3">
                <h3 class="card-title"><span>{{ $car->modelo->name }} {{ $car->modelo->type->name }}
                        {{ $car->modelo->manufacturer->name }}</span> <span><a href="{{ route('addtogarage', $car->id) }}"
                            class="btn btn-warning">ADD TO GARAGE</a></span></h3>
                <div class="card-img-top">
                    <img src="{{ isset($car->media[0]) ? $car->media[0]->original_url : asset('/storage/images/Logo.png') }}"
                        alt="Card image cap">
                    <img src="{{ isset($car->media[1]) ? $car->media[1]->original_url : asset('/storage/images/Logo.png') }}"
                        alt="Card image cap">
                    <img src="{{ isset($car->media[2]) ? $car->media[2]->original_url : asset('/storage/images/Logo.png') }}"
                        alt="Card image cap">
                </div>

                <div class="card-body">
                    <p class="card-text">{{ $car->description }}</p>
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col" colspan="4">Car Details </th>
                                    </tr>
                                </thead>
                                <tbody class="model">
                                    <tr>
                                        <th scope="row">Model</th>
                                        <td>{{ $car->modelo->name }} {{ $car->modelo->year }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Manufacturer</th>
                                        <td>{{ $car->modelo->manufacturer->name }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Car Type</th>
                                        <td>{{ $car->modelo->type->name }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Transmisson</th>
                                        <td>{{ $car->transmission->name }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Fuel</th>
                                        <td>{{ $car->fuel->name }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col" colspan="4">Car Accessories </th>
                                    </tr>
                                </thead>
                                <tbody class="model">
                                    @foreach ($car->accessories as $accessory)
                                        <tr>
                                            <th scope="row">{{ $accessory->name }}</th>
                                            <td><i class="bi bi-check-circle"></i></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@endsection
