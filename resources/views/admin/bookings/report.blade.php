@extends('admin.index')

@section('header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Date Range Report</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Report</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection
@include('layouts.session-messages')
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card" style="height: 70vh">
                        <div class="card-header">
                            <a href="{{ route('report') }}" class="btn bg-gradient-gray">
                                Refresh
                            </a>
                            </h3>
                            <div class="card-tools">
                                <div class="input-group input-group-sm" style="width: 430px;">
                                    <form action="{{ route('report') }}">
                                        @csrf
                                        <div class="form-row">
                                            <div class="col">
                                                <input type="date" class="form-control" name="start_date">
                                            </div>
                                            <div class="col">
                                                <input type="date" class="form-control" placeholder="Last name"
                                                    name="end_date">
                                            </div>
                                            <button type="submit" class="btn btn-info">Search</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive p-0" style="height: 300px;">
                            <table class="table table-head-fixed text-nowrap">
                                <thead>
                                    <tr>
                                        <th>Book ID</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Mode of Transaction</th>
                                        <th>Location(s)</th>
                                        <th>Customer</th>
                                        <th>Car</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">

                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
    </section>
    <script>
        $(document).ready(function() {
            $.ajax({
                type: "GET",
                url: "/api/report/sales",
                dataType: "json",
                success: function(data) {
                    $.each(data.booking, function(key, data) {
                        let modeOfTransac = data.address ? "Deliver" : "Pickup";
                        let totalFee = 0;
                        let days = 0;
                        let rentPrice = 0;

                        $.each(data.car.accessories.map(function(accsessory) {
                            return accsessory.fee;
                        }), function() {
                            totalFee += Number(this);
                        });

                        let startDate = new Date(data.start_date);
                        let endDate = new Date(data.end_date);
                        let timeDiff = endDate.getTime() - startDate.getTime();

                        days = Math.ceil(timeDiff / (1000 * 3600 * 24) + 1);

                        rentPrice = days * (Number(totalFee) + Number(data.car.price_per_day));


                        var tr = $("<tr>");
                        tr.append($("<td>").html(data.id));
                        tr.append($("<td>").html(data.start_date));
                        tr.append($("<td>").html(data.end_date));
                        tr.append($("<td>").html(modeOfTransac));
                        tr.append($("<td>").html(data.address ||
                            `<b>Pickup:</b> ${data.picklocation.street} ${data.picklocation.baranggay} ${data.picklocation.city} <br>
                            <b>Return:</b> ${data.returnlocation.street} ${data.returnlocation.baranggay} ${data.returnlocation.city}`
                        ));
                        tr.append($("<td>").html(data.customer.name));
                        tr.append($("<td>").html(data.car.platenumber));
                        tr.append($("<td>").html(`&#8369;${rentPrice.toFixed(2)}`));
                        $('#tableBody').append(tr);

                    })
                }
            })

        });
    </script>
@endsection
