@extends('admin.index')

@section('header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Users</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection
@include('layouts.session-messages')
@section('content')
    <style>
        .role {
            text-transform: capitalize;

        }

        td.lowercase {
            text-transform: lowercase !important;
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
                        <div style="padding: 20px">
                            {!! $dataTable->table() !!}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {!! $dataTable->scripts() !!}
@endsection
