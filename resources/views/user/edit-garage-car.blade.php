@extends('layouts.app')

@section('content')
    @include('layouts.session-messages')
    <section id="edit-booking" style="height: 100%;">
        <div class="container" style="padding-top: 20px">
            {{-- <h2 style="text-align: center; margin: 0 0 20px 0;">Edit Booking</h2> --}}
            <div class="row">
                <div class="col-md-4">
                    <figure>
                        <figcaption>
                            <h3>₱{{ $car->CarDetails($carInGarage['car_id'])->price_per_day }} /Day
                        </figcaption>
                        </h3>
                        @foreach ($images[] = explode('=', $car->CarDetails($carInGarage['car_id'])->image_path) as $key => $image)
                            <a href="{{ route('cardetails', $carInGarage['car_id']) }}">
                                <img src="{{ '/storage/images/' . $image }}" alt="car-image" height="200px">
                            </a>
                        @break
                    @endforeach
                </figure>
                <div class="row" style="margin-top: 10px; text-align: center">
                    <h4 style="text-transform: capitalize;">{{ $car->CarDetails($carInGarage['car_id'])->modelname }}
                        {{ $car->CarDetails($carInGarage['car_id'])->modelyear }}
                        {{ $car->CarDetails($carInGarage['car_id'])->typename }} -
                        {{ $car->CarDetails($carInGarage['car_id'])->manufacturername }}
                    </h4>
                </div>
                {{-- <div class="row" style="text-align: justify; text-justify: inter-word; margin: 5px">
                    {{ $car->CarDetails($carInGarage['car_id'])->description }}
                </div> --}}
                <style>
                    .accessories {
                        text-align: left;
                        border: 1px solid black;
                        padding: 5px;
                        text-transform: capitalize;
                    }
                </style>
                <div class="row" style="margin: 5px">
                    <table style="width: 100%; text-align: center; margin: 20px auto;">
                        <tr>
                            <th class="accessories">Transmission: </th>
                            <td class="accessories">{{ $car->CarDetails($carInGarage['car_id'])->transmissionname }}
                            </td>
                        </tr>
                        <tr>
                            <th class="accessories">Fuel:</th>
                            <td class="accessories">{{ $car->CarDetails($carInGarage['car_id'])->fuelname }}</td>
                        </tr>
                        <tr>
                            <th class="accessories">Additional:</th>
                            <td class="accessories">
                                @foreach ($carAccessories as $accessory)
                                    {{ $accessory->name }},
                                @endforeach
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="col-md-8">
                <form action="{{ route('savegarage', $carInGarage['car_id']) }}" method="POST">
                    @csrf
                    <input type="hidden" name="customer_id" value="{{ $carInGarage['customer_id'] }}">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="pickup-date">Start Date</label>
                            <input type="date" class="form-control" id="pickup-date"
                                value="{{ $carInGarage['start_date'] }}" name="start_date" min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="return-date">Return Date</label>
                            <input type="date" class="form-control" id="return-date"
                                value="{{ $carInGarage['end_date'] }}" name="end_date" min="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>
                    <figure style="margin: 10px">
                        <figcaption> Choose wheather you want to pickup or deliver the car </figcaption>
                        <div style="border: 2px solid; padding: 10px;">
                            <div class="row">
                                <div class="form-group">
                                    <div class="form-check form-check-inline">
                                        @if (!$carInGarage['address'])
                                            <input class="form-check-input" type="radio" name="typeget"
                                                id="pickup-radio" value="pickup" checked>
                                        @else
                                            <input class="form-check-input" type="radio" name="typeget"
                                                id="pickup-radio" value="pickup">
                                        @endif
                                        <label class="form-check-label" for="pickup-radio">Pickup</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        @if ($carInGarage['address'])
                                            <input class="form-check-input" type="radio" name="typeget"
                                                id="delivery-radio" value="delivery" checked>
                                        @else
                                            <input class="form-check-input" type="radio" name="typeget"
                                                id="delivery-radio" value="delivery">
                                        @endif
                                        <label class="form-check-label" for="delivery-radio">Delivery</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row" id="pickup-group">
                                <div class="form-group col-md-6">
                                    <label for="pickup-location">Pickup Location</label>
                                    <select class="form-control" id="pickup-location" name="pick_id">
                                        @if ($carInGarage['pick_id'])
                                            <option selected value="{{ $carInGarage['pick_id'] }}">
                                                {{ $location->accessLocation($carInGarage['pick_id'])->street }},
                                                {{ $location->accessLocation($carInGarage['pick_id'])->baranggay }},
                                                {{ $location->accessLocation($carInGarage['pick_id'])->city }}
                                            </option>
                                        @endif
                                        @foreach ($pickLocation as $location)
                                            <option value="{{ $location->id }}">{{ $location->street }},
                                                {{ $location->baranggay }}, {{ $location->city }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="return-location">Return Location</label>
                                    <select type="text" class="form-control" id="return-location" name="return_id">
                                        @if ($carInGarage['return_id'])
                                            <option selected value="{{ $carInGarage['return_id'] }}">
                                                {{ $location->accessLocation($carInGarage['return_id'])->street }},
                                                {{ $location->accessLocation($carInGarage['return_id'])->baranggay }},
                                                {{ $location->accessLocation($carInGarage['return_id'])->city }}
                                            </option>
                                        @endif
                                        @foreach ($returnLocation as $location)
                                            <option value="{{ $location->id }}">{{ $location->street }},
                                                {{ $location->baranggay }}, {{ $location->city }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" id="delivery-address-group" style="display:none;">
                                <label for="delivery-address">Delivery Address</label>
                                <input type="text" class="form-control" id="delivery-address"
                                    value="{{ $carInGarage['address'] }}" name="address">
                            </div>


                            <div class="row" id="map" style="margin-top: 20px;" align="center">
                                <figure>
                                    <figcaption> Navigate the locations you want to know</figcaption>
                                    <iframe
                                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3862.5691525291695!2d121.03332401475795!3d14.509405489860562!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397cfacb41d0deb%3A0x691bdfc6601f578c!2sTechnological%20University%20of%20the%20Philippines%20-%20Taguig%20Campus!5e0!3m2!1sen!2sph!4v1680749914824!5m2!1sen!2sph"
                                        width="80%" height="200px" style="border:0;" allowfullscreen=""
                                        loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                                    </iframe>
                                </figure>
                            </div>
                        </div>
                    </figure>

                    <div class="row" style="margin: 20px 0px 20px 10px">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                @if ($carInGarage['driver_id'] == 0)
                                    <input class="form-check-input" type="radio" name="drivetype" id="self-drive"
                                        value="0" checked>
                                @else
                                    <input class="form-check-input" type="radio" name="drivetype" id="self-drive"
                                        value="0">
                                @endif
                                <label class="form-check-label" for="self-drive">Self Drive</label>
                            </div>
                            <div class="form-check form-check-inline">
                                @if ($carInGarage['driver_id'] == 1)
                                    <input class="form-check-input" type="radio" name="drivetype" id="with-driver"
                                        value="1" checked>
                                @else
                                    <input class="form-check-input" type="radio" name="drivetype" id="with-driver"
                                        value="1">
                                @endif
                                <label class="form-check-label" for="with-driver">With Driver</label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-10" style="float: right">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                        @if (!$carInGarage['start_date'] == null && !$carInGarage['end_date'] == null)
                            <div class="col-md-2">
                                <a href="{{ route('bookcar', $carInGarage['car_id']) }}" class="btn btn-primary">Book
                                    Now</a>
                            </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
    $(document).ready(function() {
        var typegetRadios = $('input[type=radio][name=typeget]'); // Get all typeget radios
        var pickupGroup = $('#pickup-group');
        var pickupLocation = $('#pickup-location');
        var returnLocation = $('#return-location');
        var deliveryAddress = $('#delivery-address');
        var deliveryAddressGroup = $('#delivery-address-group');

        // Check the selected radio on page load
        if (typegetRadios.filter(':checked').val() === 'pickup') {
            pickupGroup.show();
            pickupLocation.prop('disabled', false);
            returnLocation.prop('disabled', false);
            deliveryAddress.prop('disabled', true);
            deliveryAddressGroup.hide();
        } else {
            pickupGroup.hide();
            pickupLocation.prop('disabled', true);
            returnLocation.prop('disabled', true);
            deliveryAddress.prop('disabled', false);
            deliveryAddressGroup.show();
        }

        // Listen to radio changes
        typegetRadios.change(function() {
            if (this.value === 'pickup') {
                pickupGroup.show();
                pickupLocation.prop('disabled', false);
                returnLocation.prop('disabled', false);
                deliveryAddress.prop('disabled', true);
                deliveryAddressGroup.hide();
            } else {
                pickupGroup.hide();
                pickupLocation.prop('disabled', true);
                returnLocation.prop('disabled', true);
                deliveryAddress.prop('disabled', false);
                deliveryAddressGroup.show();
            }
        });
    });
</script>
@endsection
