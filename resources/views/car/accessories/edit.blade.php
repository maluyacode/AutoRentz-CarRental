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
                            <h3 class="card-title">Edit Car Accessory</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        {!! Form::model($accessory, [
                            'route' => ['accessories.update', $accessory->id],
                            'method' => 'PUT',
                            'enctype' => 'multipart/form-data',
                        ]) !!}
                        <div class="card-body">
                            <div class="form-group row">
                                {!! Form::label('accessoryid', 'ID') !!}
                                {!! Form::text('id', $accessory->id, ['class' => 'form-control', 'disabled']) !!}
                            </div>
                            <div class="form-group row">
                                {!! Form::label('name', 'Name') !!}
                                {!! Form::text('name', $accessory->name, ['class' => 'form-control']) !!}
                                @error('name')
                                    <small>{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group row">
                                {!! Form::label('fee', 'Fee', ['class' => 'image']) !!}
                                {!! Form::text('fee', $accessory->fee, ['class' => 'form-control']) !!}
                                @error('fee')
                                    <small>{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group row" style="margin: 50px 0">
                                {!! Form::label(null, 'Image(s)', ['class' => 'image']) !!}
                                {!! Form::file('image_path[]', ['multiple' => true]) !!}
                                @error('image_path.*')
                                    <small style="color: red">{{ $message }}</small>
                                @enderror
                                @error('image_path')
                                    <small style="color: red">{{ $message }}</small>
                                @enderror
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#exampleModalCenter">
                                    View Images
                                </button>
                            </div>
                            <div class="form-group row">
                                {!! Form::label('created-at', 'Created At') !!}
                                {!! Form::text('created_at', $accessory->created_at, ['class' => 'form-control', 'disabled']) !!}
                            </div>
                            <div class="form-group row">
                                {!! Form::label('updated-at', 'Last Update') !!}
                                {!! Form::text('updated_at', $accessory->updated_at, ['class' => 'form-control', 'disabled']) !!}
                            </div>
                            {!! Form::submit('submit', ['class' => 'btn btn-primary']) !!}
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
    </section>
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">
                        {{ $accessory->name }}
                        - Images</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        @foreach ($images[] = explode('=', $accessory->image_path) as $key => $image)
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
@endsection
