@extends('admin.index')

@section('header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Drivers</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Drivers</li>
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
                            <form action="{{ route('drivers.import') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="input-group mb-3" style="width: 50%">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="inputGroupFile04"
                                            name="excel">
                                        <label class="custom-file-label" for="inputGroupFile04">Choose file</label>
                                    </div>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="submit">Submit</button>
                                    </div>
                                </div>
                            </form>

                            {!! $dataTable->table() !!}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {!! $dataTable->scripts() !!}
    <script>
        $('.custom-file-input').on("change", function(e) {
            $('.custom-file-label').html(e.target.files[0].name);
        });
    </script>
@endsection
