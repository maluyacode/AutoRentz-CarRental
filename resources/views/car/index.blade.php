@extends('admin.index')

@section('header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">List of Cars</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Car</a></li>
                        <li class="breadcrumb-item active">List of Cars</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection
@include('layouts.session-messages')
@section('content')
    <style>
        details {
            border: 1px solid #aaa;
            border-radius: 4px;
            padding: 0.5em 0.5em 0;
        }

        summary {
            font-weight: bold;
            margin: -0.5em -0.5em 0;
            padding: 0.5em;
        }

        details[open] {
            padding: 0.5em;
        }

        details[open] summary {
            border-bottom: 1px solid #aaa;
            margin-bottom: 0.5em;
        }
    </style>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Rentahan ng kotse ni Earl Russell SY</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            {{-- <a href="{{ route('car.create') }}" class="btn btn-block bg-gradient-primary btn-sm"
                                style="width:150px; margin:5px">
                                Add New
                            </a> --}}
                            {!! $dataTable->table() !!}
                        </div>
                        <!-- /.card-body -->
                    </div>
                </div>
            </div>
        </div>
    </section>
    {!! $dataTable->scripts() !!}
@endsection
