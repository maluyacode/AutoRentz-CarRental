@extends('admin.index')

@section('header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Create</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Car</a></li>
                        <li class="breadcrumb-item active">Book</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection


@section('content')
    <style>
        small {
            color: red;
        }
    </style>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-10" style="margin: auto">
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">Book a Car</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <form action="{{ route('updateBooking', $booking->id) }}" method="POST">
                                @method("PUT")
                                @csrf
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="start_date">Select Customer</label>
                                            <select type="text" class="form-control" id="customer" name="customer_id">
                                                @foreach ($allcustomer as $customer)
                                                    <option value="{{ $customer->id }}"
                                                        {{ $booking->customer_id == $customer->id ? 'selected' : '' }}>
                                                        {{ $customer->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('customer_id')
                                                <small>{{ $message }}</small>
                                            @enderror
                                            <div style="display: flex; flex-direction:row">
                                                <label
                                                    style="height: 30px; margin-top:5px; font-size:12px; width:60px">Customer
                                                    Search</label>
                                                <input type="text" id="searchInput" list="myOptions"
                                                    style="height: 35px; margin-top:5px; width:80%">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="start_date">Start Date:</label>
                                            <input type="date" id="start_date" name="start_date" class="form-control"
                                                min="<?php echo date('Y-m-d'); ?>" value="{{ $booking->start_date }}">
                                            @error('start_date')
                                                <small>{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="end_date">End Date:</label>
                                            <input type="date" id="end_date" name="end_date" class="form-control"
                                                min="<?php echo date('Y-m-d'); ?>" value="{{ $booking->end_date }}">
                                            @error('end_date')
                                                <small>{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check form-check-inline" style="margin-top:30px ">
                                            <input class="form-check-input" type="radio" name="typeget" id="pickup-radio"
                                                value="pickup" {{ $booking->address == null ? 'checked' : '' }}>
                                            <label class="form-check-label" for="pickup-radio">Pickup</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="typeget"
                                                id="delivery-radio" value="delivery"
                                                {{ $booking->address != null ? 'checked' : '' }}>
                                            <label class="form-check-label" for="delivery-radio">Delivery</label>
                                        </div>
                                        @error('typeget')
                                            <small>{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="pickup-location">Pickup Location:</label>
                                            <select class="form-control" id="pickup-location" name="pick_id">
                                                <option selected>Select Location</option>
                                                @foreach ($allLocation as $locations)
                                                    <option value="{{ $locations->id }}"
                                                        {{ $booking->pickup_location_id == $locations->id ? 'selected' : '' }}>
                                                        {{ $locations->street . ', ' . $locations->baranggay . ', ' . $locations->city }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('pick_id')
                                                <small>{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="return-location">Return Location</label>
                                            <select type="text" class="form-control" id="return-location"
                                                name="return_id">
                                                <option selected>Select Location</option>
                                                @foreach ($allLocation as $locations)
                                                    <option value=" {{ $locations->id }}"
                                                        {{ $booking->return_location_id == $locations->id ? 'selected' : '' }}>
                                                        {{ $locations->street . ', ' . $locations->baranggay . ', ' . $locations->city }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('return_id')
                                                <small>{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="drive-type">Drive Type</label>
                                        <select type="text" class="form-control" id="drive-type" name="drivetype">
                                            <option selected>Select</option>
                                            <option value="1" {{ $booking->driver_id == '1' ? 'selected' : '' }}>With
                                                Driver
                                            </option>
                                            <option value="0" {{ $booking->driver_id == null ? 'selected' : '' }}>Self
                                                Drive
                                            </option>
                                        </select>
                                        @error('drivetype')
                                            <small>{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-9">
                                        <div class="form-group" id="delivery-address-group">
                                            <label for="delivery-address">Delivery Address</label>
                                            <input type="text" class="form-control" id="delivery-address"
                                                name="address" value="{{ $booking->address }}">
                                            @error('address')
                                                <small>{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-9">
                                        <div class="form-group" id="delivery-address-group">
                                            <label for="delivery-address">Cars</label>
                                            <select type="text" class="form-control" id="drive-type" name="car_id">
                                                <option>Choose a car you want to book...</option>
                                                @foreach ($allCar as $car)
                                                    <option value="{{ $car->id }}"
                                                        {{ $booking->car_id == $car->id ? 'selected' : '' }}>
                                                        {{ $car->platenumber . ' - ' . $car->modelname . ', ' . $car->modelyear . ', ' . $car->typename . ', ' . $car->manufacturername }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('car_id')
                                                <small>{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="display:flex; justify-content:end">
                                    <button type="submit" class="btn btn-block bg-gradient-warning btn-lg"
                                        style="width: 100px;">Submit</button>
                                </div>
                            </form>
                        </div>
                        <!-- /.card-body -->
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        const selectElement = document.querySelector('#customer');
        const searchInput = document.querySelector('#searchInput');

        searchInput.addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const options = selectElement.options;

            for (let i = 0; i < options.length; i++) {
                const optionText = options[i].text.toLowerCase();
                if (optionText.indexOf(searchValue) !== -1) {
                    options[i].selected = true;
                    break;
                }
            }
        });
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
