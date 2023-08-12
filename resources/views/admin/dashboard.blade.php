@extends('admin.index')

@section('pageStyles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
    </script>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        @if (Auth::user()->role == 'admin')
                            <div class="row">
                                <div class="col-lg-3 col-6">
                                    <!-- small box -->
                                    <div class="small-box bg-info" style="text-align: center">
                                        <div class="inner">
                                            <h3>₱{{ number_format($totalPrice, 2, '.', ',') }}</h3>
                                            <p>Overall Income</p>
                                        </div>
                                        <a href="{{ route('users.index') }}" class="small-box-footer">More info <i
                                                class="fas fa-arrow-circle-right"></i></a>
                                    </div>
                                </div>
                                <!-- ./col -->
                                <div class="col-lg-3 col-6" style="text-align: center">
                                    <!-- small box -->
                                    <div class="small-box bg-secondary">
                                        <div class="inner">
                                            <h3>{{ $pendings }}</h3>

                                            <p>Pendings</p>
                                        </div>
                                        <a href="{{ route('adminPendings') }}" class="small-box-footer">More info <i
                                                class="fas fa-arrow-circle-right"></i></a>
                                    </div>
                                </div>
                                <!-- ./col -->
                                <div class="col-lg-3 col-6" style="text-align: center">
                                    <!-- small box -->
                                    <div class="small-box bg-warning">
                                        <div class="inner">
                                            <h3>{{ $confirmed }}</h3>
                                            <p>Confirmed</p>
                                        </div>
                                        <a href="{{ route('adminConfirms') }}" class="small-box-footer">More info <i
                                                class="fas fa-arrow-circle-right"></i></a>
                                    </div>
                                </div>
                                <!-- ./col -->
                                <div class="col-lg-3 col-6" style="text-align: center">
                                    <!-- small box -->
                                    <div class="small-box bg-success">
                                        <div class="inner">
                                            <h3>{{ $finished }}</h3>

                                            <p>Finished</p>
                                        </div>
                                        <a href="{{ route('adminFinish') }}" class="small-box-footer">More info <i
                                                class="fas fa-arrow-circle-right"></i></a>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-6" style="text-align: center">
                                    <!-- small box -->
                                    <div class="small-box bg-danger">
                                        <div class="inner">
                                            <h3>{{ $cancelled }}</h3>
                                            <p>Cancelled</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- ./col -->
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-lg-3 col-6" style="text-align: center">
                                <!-- small box -->
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3>{{ $cars }}</h3>
                                        <p>Vehicles</p>
                                    </div>
                                    <a href="{{ route('cars.page') }}" class="small-box-footer">More info <i
                                            class="fas fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6" style="text-align: center">
                                <!-- small box -->
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3>{{ $users }}</h3>
                                        <p>Users</p>
                                    </div>
                                    <a href="{{ route('users.index') }}" class="small-box-footer">More info <i
                                            class="fas fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6" style="text-align: center">
                                <!-- small box -->
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3>{{ $drivers }}</h3>
                                        <p>Drivers</p>
                                    </div>
                                    <a href="{{ route('drivers.page') }}" class="small-box-footer">More info <i
                                            class="fas fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6" style="text-align: center">
                                <!-- small box -->
                                <div class="small-box bg-secondary">
                                    <div class="inner">
                                        <h3>{{ $locations }}</h3>
                                        <p>Locations</p>
                                    </div>
                                    <a href="{{ route('location.index') }}" class="small-box-footer">More info <i
                                            class="fas fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- add Item --}}
                    <div class="carousel-item">
                        <div class="chart-container">
                            <div class="row">
                                <div class="btn-group btn-group-toggle col-md-2" data-toggle="buttons">
                                    <label class="btn btn-outline-secondary active">
                                        <input class="income-radio" type="radio" name="options" id="option3"
                                            autocomplete="off" value="months"> Months
                                    </label>
                                    <label class="btn btn-outline-secondary">
                                        <input class="income-radio" type="radio" name="options" id="option3"
                                            autocomplete="off" value="days"> Days
                                    </label>
                                </div>
                                <div class="date-picker col-md-6">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">Start</div>
                                        </div>
                                        <input class="date-range" type="text" class="form-control" id="start-date"
                                            placeholder="YYYY-MM-DD">
                                    </div>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">End</div>
                                        </div>
                                        <input class="date-range" type="text" class="form-control" id="end-date"
                                            placeholder="YYYY-MM-DD">
                                    </div>
                                </div>
                                <div class="col-md-4 select-group">
                                    <select id="chart-types" class="custom-select mb-2">
                                        <option value="line" selected>Line Chart</option>
                                        <option value="bar">Bar Chart</option>
                                    </select>
                                </div>
                            </div>
                            <canvas id="monthlyIncomeChart"></canvas>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="chart-container">
                            <div class="row car-row">
                                <div class="btn-group btn-group-toggle col-md-4" data-toggle="buttons">
                                    <label class="btn btn-outline-secondary active">
                                        <input class="car-radio" type="radio" name="options" id="option3"
                                            autocomplete="off" value="all"> Rent Info
                                    </label>
                                    <label class="btn btn-outline-secondary">
                                        <input class="car-radio" type="radio" name="options" id="option3"
                                            autocomplete="off" value="model"> Model
                                    </label>
                                    <label class="btn btn-outline-secondary">
                                        <input class="car-radio" type="radio" name="options" id="option3"
                                            autocomplete="off" value="manufacturer"> Manufacturer
                                    </label>
                                    <label class="btn btn-outline-secondary">
                                        <input class="car-radio" type="radio" name="options" id="option3"
                                            autocomplete="off" value="type"> Types
                                    </label>
                                </div>
                                <div class="col-md-3 select-group">
                                    <select id="chart-types-car" class="custom-select mb-2">
                                        <option value="bar">Bar Chart</option>
                                        <option value="doughnut">Doughnut Chart</option>
                                        <option value="pie">Pie</option>
                                    </select>
                                </div>
                            </div>
                            <canvas id="rentCountPerMonthChart"></canvas>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="chart-container">
                            <div class="row row-customer">
                                <div class="btn-group btn-group-toggle col-md-3" data-toggle="buttons">
                                    <label class="btn btn-outline-secondary active">
                                        <input class="customer-radio" type="radio" name="options" id="option3"
                                            autocomplete="off" value="months-registered"> Month
                                    </label>
                                    <label class="btn btn-outline-secondary">
                                        <input class="customer-radio" type="radio" name="options" id="option3"
                                            autocomplete="off" value="weeks-registered"> Week
                                    </label>
                                    <label class="btn btn-outline-secondary">
                                        <input class="customer-radio" type="radio" name="options" id="option3"
                                            autocomplete="off" value="days-registered"> Day
                                    </label>
                                </div>
                                <div class="date-picker col-md-6">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">Start</div>
                                        </div>
                                        <input class="date-range-customer" type="text" class="form-control"
                                            placeholder="YYYY-MM-DD" id="date-start-register">
                                    </div>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">End</div>
                                        </div>
                                        <input class="date-range-customer" type="text" class="form-control"
                                            placeholder="YYYY-MM-DD" id="date-end-register">
                                    </div>
                                </div>
                                <div class="col-md-3 select-group">
                                    <select id="chart-types-customer" class="custom-select mb-2">
                                        <option value="bar">Bar Chart</option>
                                        <option value="doughnut">Doughnut Chart</option>
                                        <option value="pie">Pie Chart</option>
                                        <option value="line">Line Chart</option>
                                    </select>
                                </div>
                            </div>
                            <canvas id="customerRegisteredChart"></canvas>
                        </div>
                    </div>
                </div>
                <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>

            {{-- Laravel Charts Disabled for now --}}
            {{-- <div class="row">
                <br><br>
                <div class="col-lg-12">
                    {!! $monthlyIncome->container() !!}
                    {!! $monthlyIncome->script() !!}
                </div>
            </div>

            <div class="row" style="padding-bottom: 50px">
                <div class="col-lg-6">
                    <br><br>
                    <div>
                        {!! $carRentChart->container() !!}
                        {!! $carRentChart->script() !!}
                    </div>
                </div>
                <div class="col-lg-6">
                    <br><br>
                    <div>
                        {!! $customerRegister->container() !!}
                        {!! $customerRegister->script() !!}
                    </div>
                </div>
            </div> --}}
        </div>
    </section>
@endsection
@section('pageScripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script> --}}

    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
@endsection
