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
                        <li class="breadcrumb-item active">Create</li>
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
                                    <h3 class="card-title">Add New Location</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <style>
                                    small {
                                        color: red;
                                    }
                                    .form-group{
                                        margin: 20px
                                    }
                                </style>
                                <form action="{{ route('location.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>Street</label>
                                            <input type="text" class="form-control" name="street" value="{{ old('street') }}">
                                            @error('street')
                                                <small> {{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Baranggay</label>
                                            <input type="text" class="form-control" name="baranggay" value="{{ old('baranggay') }}">
                                            @error('baranggay')
                                                <small> {{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>City</label>
                                            <input type="text" class="form-control" name="city" value="{{ old('city') }}">
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
    </section>
@endsection
