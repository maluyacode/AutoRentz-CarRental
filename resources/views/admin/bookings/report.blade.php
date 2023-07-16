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
    <style>
        .data-height td {
            height: 75px;
        }
    </style>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card" style="height: 70vh">
                        <div class="card-header">
                            <button class="btn bg-gradient-gray" onclick="ajaxCall()">
                                Refresh
                            </button>
                            </h3>
                            <div class="card-tools">
                                <div class="input-group input-group-sm" style="width: 430px;">
                                    <div class="form-row">
                                        <div class="col">
                                            <input type="date" class="form-control" name="start_date" id="start_date">
                                        </div>
                                        <div class="col">
                                            <input type="date" class="form-control" placeholder="Last name"
                                                name="end_date" id="end_date" disabled>
                                        </div>
                                        <button type="submit" class="btn btn-info" id="search">Search</button>
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
            ajaxCall()
        });

        function ajaxCall() {
            $.ajax({
                type: "GET",
                url: "/api/report/sales",
                dataType: "json",
                success: function(data) {
                    $('#tableBody tr').remove();
                    updateTable(data);
                }
            })
        }

        $('#start_date').on('change', function() {
            $('#end_date').prop({
                'disabled': false,
                'min': $('#start_date').val()
            });
            // $('#end_date').attr('min', $('#start_date').val());
        })

        $('#search').on('click', function(e) {
            $('#end_date').prop('disabled', true);
            dataPass = {
                start: $('#start_date').val(),
                end: $('#end_date').val(),
            }
            $.ajax({
                type: "GET",
                url: "/api/report/search",
                data: dataPass,
                success: function(data) {
                    $('#tableBody tr').remove();
                    updateTable(data);
                },
                error: function(error) {
                    console.log('error');
                }
            })
        });

        function updateTable(data) {

            $.each(data.booking, function(key, data) {
                let locations = '';
                let start_date_format = moment(data.start_date).format('MM/DD/yy');
                let end_date_format = moment(data.end_date).format('MM/DD/yy');
                if (jQuery.type(data.locations) === "object") {
                    locations = `<strong>Pick: </strong>${data.locations["pick"]} <br />
                                        <strong>Return: </strong>${data.locations.return}`;
                } else {
                    locations = data.locations;
                }
                // console.log(data);
                var tr = $("<tr>");
                tr.append($("<td>").html(data.id));
                tr.append($("<td>").html(start_date_format));
                tr.append($("<td>").html(end_date_format));
                tr.append($("<td>").html(data.mode_of_transac));
                tr.append($("<td>").html(locations));
                tr.append($("<td>").html(data.customer));
                tr.append($("<td>").html(data.car));
                tr.append($("<td>").html(`&#8369;${data.total}`));


                $('#tableBody').append(tr);
                // $('tr').fadeIn(200)

            })
            $('tr').addClass('data-height');
        }
    </script>
@endsection
