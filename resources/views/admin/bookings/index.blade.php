@extends('admin.index')

@section('pageStyles')
    <link rel="stylesheet" href="{{ asset('css/bookings.css') }}">
@endsection
@section('content')
    @include('layouts.session-messages')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Bookings</h3>
                            <div style="float: right;" class="btn-group btn-group-toggle" data-toggle="buttons">
                                <label class="btn btn-outline-secondary active">
                                    <input class="booking-status" type="radio" name="options" value="all"
                                        id="all" autocomplete="off" checked> All
                                </label>
                                <label class="btn btn-outline-secondary">
                                    <input class="booking-status" type="radio" name="options" value="pendings"
                                        id="pendings" autocomplete="off"> Pendings
                                </label>
                                <label class="btn btn-outline-secondary">
                                    <input class="booking-status" type="radio" name="options" value="confirmed"
                                        id="confirmed" autocomplete="off"> Confirmed
                                </label>
                                <label class="btn btn-outline-secondary">
                                    <input class="booking-status" type="radio" name="options" value="finished"
                                        id="finished" autocomplete="off"> Finished
                                </label>
                                <label class="btn btn-outline-secondary">
                                    <input class="booking-status" type="radio" name="options" value="cancelled"
                                        id="cancelled" autocomplete="off"> Cancelled
                                </label>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="bookings-table" class="table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Customer</th>
                                        <th>Car</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Rent Price</th>
                                        {{-- <th>Transaction</th>
                                        <th>Locations(s)</th>
                                        <th>Drive Type</th> --}}
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Excel Records</h5>
                    {{-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button> --}}
                </div>
                <div class="modal-body">
                    <div class="input-group mb-3">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="inputGroupFile03">
                            <label class="custom-file-label" for="inputGroupFile03">Choose file</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('pageScripts')
    <script src="{{ asset('js/bookings.js') }}"></script>
@endsection
