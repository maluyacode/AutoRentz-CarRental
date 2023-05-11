<section class="shop">
    @include('layouts.session-messages')
    <div class="container">
        <h2>AutoRentz Cars</h2>
        <div class="row">
            @foreach ($cars as $car)
                <div style="width: 400px; margin-bottom: 20px">
                    <div class="shop-item" style="font-family: serif; font-weight: 200; height:100%;">
                        <div style="display:flex; flex-direction:row; justify-content: space-between;">
                            <h5> <em>
                                    ₱{{ $customerClass->computationDisplay(null, null, $car->price_per_day, $accessory, $car->id) }}
                                    PER DAY </em></h5>
                            <small>Seats: {{ $car->seats }}</small>
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
