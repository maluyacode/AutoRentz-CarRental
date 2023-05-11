@extends('admin.index')

@section('header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">transmission</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Car</a></li>
                        <li class="breadcrumb-item active">Transmission</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection


@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12" align="center">
                    <div class="card card-primary" style="width: 50%;">
                        <div class="card-header">
                            <h3 class="card-title">Edit Car Transmission</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        {!! Form::model($transmission, ['route' => ['transmission.update', $transmission->id], 'method' => 'PUT']) !!}
                        <div class="card-body">
                            <div class="form-group row">
                                {!! Form::label('transmissionid', 'ID') !!}
                                {!! Form::text('id', $transmission->id, ['class' => 'form-control', 'disabled']) !!}
                            </div>
                            <div class="form-group row">
                                {!! Form::label('name', 'Name') !!}
                                {!! Form::text('name', $transmission->name, ['class' => 'form-control']) !!}
                                @error('name')
                                    <small>{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group row">
                                {!! Form::label('created-at', 'Created At') !!}
                                {!! Form::text('created_at', $transmission->created_at, ['class' => 'form-control', 'disabled']) !!}
                            </div>
                            <div class="form-group row">
                                {!! Form::label('updated-at', 'Last Update') !!}
                                {!! Form::text('updated_at', $transmission->updated_at, ['class' => 'form-control', 'disabled']) !!}
                            </div>
                            {!! Form::submit('submit', ['class' => 'btn btn-primary']) !!}
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
    </section>
@endsection
