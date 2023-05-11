@extends('admin.index')

@section('header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Location</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Location</a></li>
                        <li class="breadcrumb-item active">Edit</li>
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
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Rentahan ng kotse ni Earl Russell SY</h3>
                        </div>
                        <div style="padding: 20px">
                            <div class="card card-warning" style="width: 60%; margin:auto; border-radius:20px">
                                <div class="card-header">
                                    <h3 class="card-title">Edit Location</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <style>
                                    small {
                                        color: red;
                                    }

                                    .form-group {
                                        margin: 20px
                                    }
                                </style>
                                <form action="{{ route('location.update', $location->id) }}" method="POST" enctype="multipart/form-data">
                                    @method('PUT')
                                    @csrf
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>Street</label>
                                            <input type="text" class="form-control" name="street"
                                                value="{{ $location->street }}">
                                            @error('street')
                                                <small> {{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Baranggay</label>
                                            <input type="text" class="form-control" name="baranggay"
                                                value="{{ $location->baranggay }}">
                                            @error('baranggay')
                                                <small> {{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>City</label>
                                            <input type="text" class="form-control" name="city"
                                                value="{{ $location->city }}">
                                            @error('city')
                                                <small> {{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputFile">Images</label>
                                            <div class="input-group">
                                                <input type="file" id="exampleInputFile" name="image_path[]" multiple>
                                                @error('image_path.*')
                                                    <small> {{ $message }}</small>
                                                @enderror
                                                @error('image_path')
                                                    <small> {{ $message }}</small>
                                                @enderror
                                            </div>
                                            <!-- Button trigger modal -->
                                            <button type="button" class="btn btn-primary" data-toggle="modal"
                                                data-target="#exampleModalCenter" style="margin:10px">
                                                View Images
                                            </button>
                                        </div>
                                    </div>
                                    <!-- /.card-body -->
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">
                            {{ $location->street . ' ' . $location->baranggay . ' ' . $location->city }}
                            - Images</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            @foreach ($images[] = explode('=', $location->image_path) as $key => $image)
                                <img src="{{ '/storage/images/' . $image }}" alt="" width="100px" height="100px"
                                    style="margin:5px">
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
