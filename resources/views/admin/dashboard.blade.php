@extends('admin.index')

@section('pageStyles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
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
                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                <label class="btn btn-outline-secondary active">
                                    <input class="income-radio" type="radio" name="options" id="option2" autocomplete="off" value="default"> Default
                                </label>
                                <label class="btn btn-outline-secondary">
                                    <input class="income-radio" type="radio" name="options" id="option3" autocomplete="off" value="days"> Days
                                </label>
                                <label class="btn btn-outline-secondary">
                                    <input class="income-radio" type="radio" name="options" id="option3" autocomplete="off" value="months"> Months
                                </label>
                            </div>
                            <canvas id="monthlyIncomeChart"></canvas>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="chart-container">
                            <canvas id="rentCountPerMonthChart"></canvas>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="chart-container">
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
    <script src="{{ asset('js/dashboard.js') }}"></script>
@endsection
