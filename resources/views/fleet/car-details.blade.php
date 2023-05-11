@extends('layouts.app')

@section('content')
    @include('layouts.session-messages')
    <style>
        .mySlides {
            display: none;
        }
    </style>
    <section class="car-details" style="height: 80vh">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="car-image">
                        <div class="w3-content w3-display-container">
                            @foreach ($images[] = explode('=', $car->image_path) as $key => $image)
                                <div class="w3-display-container mySlides">
                                    <img src="{{ '/storage/images/' . $image }}" style="width:100%">
                                </div>
                            @endforeach
                            <button class="w3-button w3-display-left w3-black" onclick="plusDivs(-1)">&#10094;</button>
                            <button class="w3-button w3-display-right w3-black" onclick="plusDivs(1)">&#10095;</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="car-info">
                        <div style="display: flex;">
                            <h2>{{ $car->modelname . ' ' . $car->typename . ' ' . $car->modelyear }}</h2>
                            <h5 style=" margin:5px; height: 20px">{{ $car->manufacturername }}</h5>
                        </div>
                        <p><strong>Description:</strong> {{ $car->description }} </p>

                        <table>
                            <tr>
                                <td style="width: 300px">
                                    <p><strong>Price:</strong>
                                        ₱{{ $customerClass->computationDisplay(null, null, $car->price_per_day, $accessoryfee, $car->id) }}
                                        /day</p>
                                </td>
                                <td style="width: 300px">
                                    <p><strong>Seats:</strong> {{ $car->seats }} people capacity</p>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 300px">
                                    <p><strong>Transmission:</strong> {{ $car->transmissionname }} </p>
                                </td>
                                <td style="width: 300px">
                                    <p><strong>Fuel Type:</strong> {{ $car->fuelname }} </p>
                                </td>
                            </tr>
                        </table>
                        <p><strong>Additional:</strong>
                            |
                            @foreach ($accessory as $accessories)
                                {{ $accessories->name }} |
                            @endforeach
                        </p>
                        <div class="button-group">
                            <a href="{{ route('addtogarage', $car->id) }}" class="btn btn-secondary">Add to Garage</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        var slideIndex = 1;
        showDivs(slideIndex);

        function plusDivs(n) {
            showDivs(slideIndex += n);
        }

        function showDivs(n) {
            var i;
            var x = document.getElementsByClassName("mySlides");
            if (n > x.length) {
                slideIndex = 1
            }
            if (n < 1) {
                slideIndex = x.length
            }
            for (i = 0; i < x.length; i++) {
                x[i].style.display = "none";
            }
            x[slideIndex - 1].style.display = "block";
        }
    </script>
@endsection
