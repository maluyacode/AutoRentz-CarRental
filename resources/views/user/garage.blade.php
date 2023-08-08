@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/garage.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
@endsection

@section('content')
    {{-- {{ dd($carInGarage) }} --}}
    @include('layouts.session-messages')
    <div class="container" style="height: 100%; padding-top: 20px">
        @if ($carInGarage == null)
            <div class="container" style="width:100%flex; text-align:center; ">
                <h2 style="width:auto; height:0px; margin:170px auto">Your garage is empty.</h2>
            </div>
        @else
            <div class="row" style="font-size: 18px">
                @foreach ($carInGarage as $key => $bookInfo)
                    {{-- {{ dd($bookInfo['car']->media[0]->original_url) }} --}}
                    <div class="col-md-12 mb-4 cars-row">
                        <div class="card">
                            <div class="row no-gutters">
                                <div class="col-md-4">
                                    <div class="row" style="margin: 5%">
                                        <a href="{{ route('cardetails', $key) }}">
                                            @if (count($bookInfo['car']->media) > 0)
                                                <img src="{{ $bookInfo['car']->media[0]->original_url }}" alt="car-image">
                                            @else
                                                No images in media
                                            @endif
                                        </a>
                                    </div>
                                    {{-- <div class="row" style="margin: 5%">
                                    <p class="card-title" style="text-transform: capitalize; width:auto">
                                        Seating capacity:
                                        <strong>{{ $carDetails->CarDetails($bookInfo['car_id'])->seats }}</strong>
                                        people
                                    <ul style="margin: 0px 20px; display:flex; flex-direction:row; flex-wrap: wrap; ">
                                        @foreach ($accessory->where('car_id', $bookInfo['car_id']) as $accessories)
                                            <li style="margin:0px 15px"> {{ $accessories->name }}</li>
                                        @endforeach
                                    </ul>
                                    </p>
                                </div> --}}
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body">
                                        <div style="display: flex">
                                            <h4 class="card-title lt-strong" style="text-transform: capitalize; width:auto">
                                                {{ $bookInfo['car']->modelo->name }}
                                                {{ $bookInfo['car']->modelo->year }}
                                                {{ $bookInfo['car']->modelo->type->name }}
                                            </h4>
                                            <small style="margin: 0 5px">{{ $bookInfo['car']->modelo->manufacturer->name }}
                                            </small>
                                        </div>
                                        <p class="card-text">{{ $bookInfo['car']->description }}</p>
                                        <p class="card-text car-info" style="margin-bottom: 5px ">
                                            <strong class="lt-strong"><i class="fa-solid fa-chair"></i></strong> -
                                            <em>{{ $bookInfo['car']->seats }}</em> |
                                            <strong class="lt-strong"><i class="fa-solid fa-gas-pump"></i></strong> - <em>
                                                {{ $bookInfo['car']->fuel->name }}</em> |
                                            <strong class="lt-strong"><i class="fa-solid fa-gears"></i></strong> - <em>
                                                {{ $bookInfo['car']->transmission->name }}</em> |
                                            <strong class="lt-strong"><i class="fa-solid fa-car-side"></i></strong> - <em>[
                                                @foreach ($bookInfo['car']->accessories as $accessory)
                                                    {{ $accessory->name }},
                                                @endforeach
                                            </em>]
                                        </p>
                                        {{-- <p class="card-text ">
                                            <strong class="lt-strong">Transaction</strong>
                                            @if ($bookInfo['address'])
                                                <strong>- Delivery: </strong> {{ $bookInfo['address'] }}
                                            @elseif(!$bookInfo['address'] && ($bookInfo['return_id'] && $bookInfo['pick_id']))
                                                <strong>- Pickup: </strong> <br>
                                                <b class="lt-strong">Pickup Location </b> -
                                                {{ $bookInfo['locations']['pick'] }}<br>
                                                <b class="lt-strong"> Return Location </b> -
                                                {{ $bookInfo['locations']['return'] }}
                                            @else
                                                - <em> Please Specify</em>
                                            @endif

                                        </p> --}}
                                        <div class="row">
                                            <div class="col-md-12" style="text-align: right">
                                                <p class="card-text"><strong class="lt-strong">Price:</strong>
                                                    ₱{{ number_format($bookInfo['car']->price_per_day, 0, '.', ',') }}
                                                    per day
                                                </p>
                                            </div>
                                            {{-- <div class="col-md-8" style="text-align: right">
                                                <h4 class="card-text"><strong class="lt-strong">Total Price:</strong>
                                                    {{ number_format($bookInfo['totalPrice'] * $bookInfo['days'], 0, '.', ',') }}
                                                </h4>
                                            </div> --}}
                                        </div>
                                        <div class="buttons-garage">
                                            <button class="btn btn-danger delete-button" data-id="{{ $bookInfo['car_id'] }}">Remove</button>
                                            {{-- <a href="{{ route('editgarage', $bookInfo['car_id']) }}"
                                                class="btn btn-primary">Edit</a> --}}
                                            <button class="btn btn-success confirm-button"
                                                data-id="{{ $bookInfo['car_id'] }}" data-toggle="modal"
                                                data-target="#bookModal">Book </button>
                                            <input value="{{ $bookInfo['car_id'] }}" hidden class="car-id">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                {{-- @include('layouts.confirmation') --}}
            </div>
        @endif
    </div>
    {{-- <button type="button" class="btn btn-primary">ASDSAD</button> --}}

    <div class="modal fade bd-example-modal-lg" id="bookModal" tabindex="-1" role="dialog"
        aria-labelledby="bookModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Reservation Details</h5>
                    <button type="button" class="close btn btn-danger" data-dismiss="modal" aria-label="Close" id="closeModalForm">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="bookForm" action="" method="POST">
                        @csrf
                        <input type="hidden" name="customer_id" value="{{ Auth::user()->customer->id }}">
                        <input type="hidden" name="car_id" id="car_id">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="pickup-date">Start Date</label>
                                <input type="text" class="form-control date-pick" id="pickup-date" name="start_date">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="return-date">Return Date</label>
                                <input type="text" class="form-control date-pick" id="return-date" name="end_date">
                            </div>
                        </div>
                        <div>
                            <div class="row pickup-deliver">
                                <div class="form-group">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="typeget" id="pickup-radio"
                                            value="pickup" checked>
                                        <label class="form-check-label" for="pickup-radio">Pickup</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="typeget"
                                            id="delivery-radio" value="delivery">
                                        <label class="form-check-label" for="delivery-radio">Delivery</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="pickup-group">
                                <div class="form-group col-md-6">
                                    {{-- <label for="pickup-location">Pickup Location</label> --}}
                                    <select class="form-control" id="pickup-location" name="pick_id">
                                        <option value="">Select pickup location</option>
                                        @foreach ($locations as $key => $location)
                                            <option value="{{ $key }}">{{ $location }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    {{-- <label for="return-location">Return Location</label> --}}
                                    <select type="text" class="form-control" id="return-location" name="return_id">
                                        <option value="">Select return location</option>
                                        @foreach ($locations as $key => $location)
                                            <option value="{{ $key }}">{{ $location }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group " id="address-group">
                                {{-- <label for="delivery-address">Delivery Address</label> --}}
                                <input type="text" class="form-control" id="delivery-address" value=""
                                    placeholder="Enter delivery address" name="address">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12 radio-drive-type">
                                <span>Select Drive Type: </span>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="drivetype" id="self-drive"
                                        value="0">
                                    <label class="form-check-label" for="self-drive">Self Drive</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="drivetype" id="with-driver"
                                        value="1">
                                    <label class="form-check-label" for="with-driver">With Driver</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button id="submit" type="button" class="btn btn-success">Submit</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
    integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
</script> --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
    integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
</script> --}}
    <script src="{{ asset('js/garage.js') }}"></script>
@endsection
{{-- <div class="col-md-12 mb-4 garage-card-item">
    <div class="card">
        <div class="row no-gutters">
            <div class="col-md-4">
                <div class="row" style="margin: 5%">
                    <a href="{{ route('cardetails', $bookInfo['car_id']) }}">
                        <img src="/storage/images/FakeCarImage1.png" alt="car-image">
                    </a>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card-body">
                    <div style="display: flex">
                        <h4 class="card-title" style="text-transform: capitalize; width:auto">
                            Sovereign 1990 MPV
                        </h4>
                        <small style="margin: 0 5px">Studebaker
                        </small>
                    </div>
                    <p class="card-text">Corrupti quis corrupti id sit omnis officiis doloribus fugit.
                        Laborum est
                        consequatur facere possimus. Expedita necessitatibus cumque voluptatibus et atque
                        quam.
                        Perspiciatis voluptate quasi corporis vel non distinctio qui voluptas. Recusandae
                        labore
                        incidunt debitis omnis vel laboriosam. Dolorem eos ut ut est eum totam ullam.
                        Tenetur nam
                        laboriosam in et eligendi molestias quos. Repellat sunt sint non quo. Non iste sint
                        quidem
                        architecto. Eligendi culpa debitis qui.
                    </p>
                    <p class="card-text" style="margin-bottom: 5px ">
                        <strong>Start Date:</strong> Aug 08, 2023 |
                        <strong>End Date: </strong> Aug 10, 2023 | 3 day(s)
                        <strong>
                            Self Drive
                        </strong>
                    </p>
                    <p class="card-text">
                        <strong>Transaction</strong>
                        <strong>- Pickup: </strong> <br>
                        <b>Pickup Location </b> -
                        111 Benton Court Apt. 350, Stacey River South Colt<br>
                        <b> Return Location </b> -
                        1400 Caroline Throughway Apt. 658 Stacey River South Colt
                    </p>

                    <div class="row">
                        <div class="col-md-4">
                            <p class="card-text"><strong>Price:</strong>
                                ₱1,566 per day
                            </p>
                        </div>
                        <div class="col-md-8" style="text-align: right">
                            <h4 class="card-text"><strong>Total Price:</strong>
                                ₱4,699 Kem
                            </h4>
                        </div>
                    </div>
                    <div style="margin: 10px 0px">
                        <button class="btn btn-danger delete-button">Remove</button>
                        <a href="#" class="btn btn-primary">Edit</a>
                        <button class="btn btn-success confirm-button" data-toggle="modal"
                            data-target="#bookModal">Book Now</button>
                        <input value="" hidden class="car-id">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> --}}
