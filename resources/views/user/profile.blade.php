@extends('layouts.app')
@section('content')
    <style>
        .btn-primary {
            margin: 15px
        }

        .password {
            background-color: red
        }

        .profile-pic {
            width: 200px;
            height: 200px;
        }
    </style>
    @include('layouts.session-messages')
    <section id="profile">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header" style=" background-color: #F6F1E9;">
                            <h4>My Profile</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('editprofile', $user->id) }}" method="POST" enctype="multipart/form-data">
                                @method('PUT')
                                @csrf
                                <div class="text-center">
                                    <img class="profile-pic" src="{{ $user->image_path }}" alt="{{ $user->image_path }}">
                                    <input type="file" id="file-input" style="display:none" onchange="displayFileName()"
                                        name="image_path">
                                    <p id="file-name"></p>
                                    <a class="btn btn-primary edit-profile-pic-btn"
                                        onclick="document.getElementById('file-input').click()">Upload File</a>
                                    <script>
                                        function displayFileName() {
                                            var input = document.getElementById('file-input');
                                            var fileName = input.files[0].name;
                                            var output = document.getElementById('file-name');
                                            output.innerText = fileName;
                                        }
                                    </script>
                                </div>
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" id="name" value="{{ $user->name }}"
                                        name="name">
                                </div>
                                <div class="form-group">
                                    <label for="address">Address</label>
                                    <input type="text" class="form-control" id="address" value="{{ $user->address }}"
                                        name="address">
                                </div>
                                <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="text" class="form-control" id="phone" value="{{ $user->phone }}"
                                        name="phone">
                                </div>
                                <div class="form-group">
                                    <label for="email">Email address</label>
                                    <input type="email" class="form-control" id="email" value="{{ $user->email }}"
                                        name="email">
                                </div>
                                <button type="submit" class="btn btn-primary">Save</button>
                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-danger" data-toggle="modal"
                                    data-target="#exampleModalCenter">
                                    Change Password
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal -->
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('changePassword', $user->user_id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="exampleInputPassword1">Previous Password</label>
                            <input type="text" class="form-control" id="exampleInputPassword1" placeholder="Password" name="prevpass">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">New Password</label>
                            <input type="text" class="form-control" id="exampleInputPassword1" placeholder="Password" name="newpass">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Confirm Password</label>
                            <input type="text" class="form-control" id="exampleInputPassword1" placeholder="Password" name="confirmpass">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                </form>
            </div>
        </div>
    </div>
    </div>
@endsection
