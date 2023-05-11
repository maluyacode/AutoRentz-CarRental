@extends('user.bookings.navigation')


@section('booking')
    @include('layouts.session-messages')
    <div class="container" style="width: 75%; height:90vh">
        <form action="{{ route('savechanges', $editBook->id) }}" method="POST" class="p-4">
            @csrf
            <h2>Edit Booking Details</h2>
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="bookingID">Booking ID:</label>
                        <input type="text" id="bookingID" name="bookingID" value="{{ $editBook->id }}"
                            class="form-control" readonly>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="start_date">Start Date:</label>
                        <input type="date" id="start_date" name="start_date" value="{{ $editBook->start_date }}"
                            class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="end_date">End Date:</label>
                        <input type="date" id="end_date" name="end_date" value="{{ $editBook->end_date }}"
                            class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check form-check-inline" style="margin-top:30px ">
                        @if (!$editBook->address)
                            <input class="form-check-input" type="radio" name="typeget" id="pickup-radio" value="pickup"
                                checked>
                        @else
                            <input class="form-check-input" type="radio" name="typeget" id="pickup-radio" value="pickup">
                        @endif
                        <label class="form-check-label" for="pickup-radio">Pickup</label>
                    </div>
                    <div class="form-check form-check-inline">
                        @if ($editBook->address)
                            <input class="form-check-input" type="radio" name="typeget" id="delivery-radio"
                                value="delivery" checked>
                        @else
                            <input class="form-check-input" type="radio" name="typeget" id="delivery-radio"
                                value="delivery">
                        @endif
                        <label class="form-check-label" for="delivery-radio">Delivery</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="pickup-location">Pickup Location:</label>
                        @if (!$editBook->address)
                            <select class="form-control" id="pickup-location" name="pick_id">
                                <option value="{{ $editBook->pickup_location_id }}">
                                    {{ $accessInfo->picklocation($editBook->pickup_location_id) }}</option>
                                @foreach ($pickLocations as $locations)
                                    <option value="{{ $locations->id }}">
                                        {{ $locations->street . ' ' . $locations->baranggay . ' ' . $locations->city }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <select class="form-control" id="pickup-location" name="pick_id">
                                <option selected></option>
                                @foreach ($allLocation as $locations)
                                    <option value="{{ $locations->id }}">
                                        {{ $locations->street . ' ' . $locations->baranggay . ' ' . $locations->city }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="return-location">Return Location</label>
                        @if (!$editBook->address)
                            <select type="text" class="form-control" id="return-location" name="return_id">
                                <option value="{{ $editBook->return_location_id }}">
                                    {{ $accessInfo->returnlocation($editBook->return_location_id) }}</option>
                                @foreach ($returnLocations as $locations)
                                    <option value="{{ $locations->id }}">
                                        {{ $locations->street . ' ' . $locations->baranggay . ' ' . $locations->city }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <select type="text" class="form-control" id="return-location" name="return_id">
                                <option selected></option>
                                @foreach ($allLocation as $locations)
                                    <option value="{{ $locations->id }}">
                                        {{ $locations->street . ' ' . $locations->baranggay . ' ' . $locations->city }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="drive-type">Drive Type</label>
                    <select type="text" class="form-control" id="drive-type" name="drivetype">
                        @if ($editBook->driver_id)
                            <option value="1" selected>With Driver</option>
                            <option>Self Drive</option>
                        @else
                            <option value="1">With Driver</option>
                            <option selected>Self Drive</option>
                        @endif
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-9">
                    <div class="form-group" id="delivery-address-group">
                        <label for="delivery-address">Delivery Address</label>
                        <input type="text" class="form-control" id="delivery-address" value="{{ $editBook->address }}"
                            name="address">
                        @error('address')
                            <small>{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
            <div>
                <button type="submit" class="btn btn-primary" style="float: right">Save Changes</button>
            </div>
        </form>
    </div>
    <script>
        const pickup = document.getElementById("pickup-radio");
        const deliver = document.getElementById("delivery-radio");

        const pickupLoc = document.getElementById("pickup-location");
        const returnLoc = document.getElementById("return-location");

        const addressLoc = document.getElementById("delivery-address");

        if (pickup.checked) {
            addressLoc.setAttribute("disabled", "disabled");
            pickupLoc.removeAttribute("disabled");
            returnLoc.removeAttribute("disabled");
        } else {
            returnLoc.setAttribute("disabled", "disabled");
            pickupLoc.setAttribute("disabled", "disabled");
            addressLoc.removeAttribute("disabled");
        }

        pickup.addEventListener("click", function() {
            addressLoc.setAttribute("disabled", "disabled");
            pickupLoc.removeAttribute("disabled");
            returnLoc.removeAttribute("disabled");
        });

        deliver.addEventListener("click", function() {
            returnLoc.setAttribute("disabled", "disabled");
            pickupLoc.setAttribute("disabled", "disabled");
            addressLoc.removeAttribute("disabled");
        });
    </script>
@endsection
