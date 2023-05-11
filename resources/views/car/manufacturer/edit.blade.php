@extends('admin.index')

@section('header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Manufacturer</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Car</a></li>
                        <li class="breadcrumb-item active">Manufacturer</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection


@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row" >
                <div class="col-12" align="center">
                    <div class="card card-primary" style="width: 50%;">
                        <div class="card-header">
                            <h3 class="card-title">Edit Car Manufacturer</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form action="{{ route('manufacturers.update', $manufacturer->id) }}" method="POST">
                            @method('PUT')
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label>ID</label>
                                    <input type="text" class="form-control" value="{{ $manufacturer->id }}" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" class="form-control" value="{{ $manufacturer->name }}" name="name">
                                </div>
                                <div class="form-group">
                                    <label>Date Created</label>
                                    <input type="text" class="form-control" value="{{ $manufacturer->created_at }}" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Last Update</label>
                                    <input type="text" class="form-control" value="{{ $manufacturer->updated_at }}" disabled>
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
    </section>
@endsection
