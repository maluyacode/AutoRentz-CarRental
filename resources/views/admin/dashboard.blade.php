@extends('admin.index')

@section('header')
    @include('layouts.session-messages')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard v1</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
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
            <!-- /.row -->
            <!-- Main row -->
            <div class="row">
                <br><br>
            </div>
            <div class="row">
                <div class="col-lg-3 col-6" style="text-align: center">
                    <!-- small box -->
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $cars }}</h3>
                            <p>Vehicles</p>
                        </div>
                        <a href="{{ route('car.index') }}" class="small-box-footer">More info <i
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
                        <a href="{{ route('drivers.index') }}" class="small-box-footer">More info <i
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
            <!-- /.row (main row) -->
            <div class="row">
                <br><br>
                <div class="col-lg-12">
                    {!! $monthlyIncome->container() !!}
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>
                    {!! $monthlyIncome->script() !!}
                </div>
            </div>
            <div class="row" style="padding-bottom: 50px">
                <div class="col-lg-6">
                    <br><br>
                    <div>
                        {!! $carRentChart->container() !!}
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>
                        {!! $carRentChart->script() !!}
                    </div>
                </div>
                <div class="col-lg-6">
                    <br><br>
                    <div>
                        {!! $customerRegister->container() !!}
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>
                        {!! $customerRegister->script() !!}
                    </div>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
@endsection
