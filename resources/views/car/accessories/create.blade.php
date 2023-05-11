@extends('admin.index')

@section('header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Accessory</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Car</a></li>
                        <li class="breadcrumb-item active">Accessory</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection


@section('content')
    <style>
        .image {
            margin-right: 20px;
            font-weight: bold;
            font-size: 18px;
            color: #333;
        }

        .form-group {
            width: 90%
        }

        .btn-primary {
            text-transform: capitalize;
        }
    </style>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12" align="center">
                    <div class="card card-primary" style="width: 50%;">
                        <div class="card-header">
                            <h3 class="card-title">Create Car Accessory</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        {!! Form::open(['route' => 'accessories.store', 'enctype' => 'multipart/form-data']) !!}
                        <div class="card-body">
                            <div class="form-group row">
                                {!! Form::label('name', 'Name') !!}
                                {!! Form::text('name', old('name'), ['class' => 'form-control']) !!}
                                @error('name')
                                    <small style="color: red">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group row">
                                {!! Form::label('fee', 'Fee') !!}
                                {!! Form::text('fee', old('fee'), ['class' => 'form-control']) !!}
                                @error('fee')
                                    <small style="color: red">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group row">
                                {!! Form::label(null, 'Image(s)', ['class' => 'image']) !!}
                                {!! Form::file('image_path[]', ['multiple' => true]) !!}
                                @error('image_path.*')
                                    <small style="color: red">{{ $message }}</small>
                                @enderror
                                @error('image_path')
                                    <small style="color: red">{{ $message }}</small>
                                @enderror
                            </div>
                            {!! Form::submit('submit', ['class' => 'btn btn-primary']) !!}
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
    </section>
@endsection
