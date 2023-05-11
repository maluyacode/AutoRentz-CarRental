@extends('admin.index')

@section('header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit User</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">User</li>
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
                    <div class="card card-warning" style="width: 60%; margin:auto; border-radius:20px">
                        <div class="card-header">
                            <h3 class="card-title">Add New User</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <style>
                            small {
                                color: red;
                            }
                        </style>
                        <form action="{{ route('users.update', $user->user_id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" class="form-control" name="name" value="{{ $user->name }}">
                                    @error('name')
                                        <small> {{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="text" class="form-control" name="phone" value="{{ $user->phone }}">
                                    @error('phone')
                                        <small> {{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Address</label>
                                    <input type="text" class="form-control" name="address" value="{{ $user->address }}">
                                    @error('address')
                                        <small> {{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control" name="email" value="{{ $user->email }}">
                                    @error('email')
                                        <small> {{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Change Password / Leave empty if not</label>
                                    <input type="text" class="form-control" name="pass">
                                </div>
                                <div class="form-group">
                                    <label>Role</label>
                                    <select name="role" class="form-control" id="inputSuccess">
                                        <option value>Select</option>
                                        <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Administrator
                                        </option>
                                        <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                                        <option value="customer" {{ $user->role == 'customer' ? 'selected' : '' }}>Customer
                                        </option>
                                    </select>
                                    @error('role')
                                        <small> {{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputFile">Image</label>
                                    <div class="input-group">
                                        <input type="file" id="exampleInputFile" name="image_path" accept="image/*">
                                        @error('image_path')
                                            <small> {{ $message }}</small>
                                        @enderror
                                        <img src=" {{ asset($user->image_path) }}" width="100px" height="100px"
                                            style="margin: 5px">
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
    </section>
@endsection
