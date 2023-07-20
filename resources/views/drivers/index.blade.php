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
    <style>
        .form-group label {
            font-weight: normal !important;
        }
    </style>
    <div class="modal fade" id="ourModal" tabindex="-1" role="dialog" aria-labelledby="ourModalModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ourModalModalLabel">Add New Driver</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="#" id="driversForm" style="width: 95%; margin: auto;">
                        <div class="form-group">
                            <label for="firstname">First Name: </label>
                            <input type="text" class="form-control" id="firstname" name="firstname">
                        </div>
                        <div class="form-group">
                            <label for="lastname">Last Name: </label>
                            <input type="text" class="form-control" id="lastname" name="lastname">
                        </div>
                        <div class="form-group">
                            <label for="licensed_no">Licensed No: </label>
                            <input type="text" class="form-control" id="licensed_no" name="licensed_no">
                        </div>
                        <div class="form-group">
                            <label for="address">Address: </label>
                            <input type="text" class="form-control" id="address" name="address">
                        </div>
                        <div class="form-group">
                            <label for="description">Description: </label>
                            <textarea class="form-control" id="description" rows="5" name="description"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="images">Upload Image (Optional)</label>
                            <div class="dropzone" id="dropzone-image"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="submitForm">Save</button>
                </div>
            </div>
        </div>
    </div>
    {!! $dataTable->scripts() !!}
    <script>
        var uploadedDocumentMap = {}

        Dropzone.options.dropzoneImage = {
            url: '{{ route('drivers.storeMedia') }}',
            maxFilesize: 2,
            acceptedFiles: 'image/*',
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            success: function(file, response) {
                $('form').append('<input type="hidden" name="document[]" value="' + response.name + '">')
                uploadedDocumentMap[file.name] = response.name
            },
            removedfile: function(file) {
                file.previewElement.remove()
                var name = ''
                if (typeof file.file_name !== 'undefined') {
                    name = file.file_name
                } else {
                    name = uploadedDocumentMap[file.name]
                }
                $('form').find('input[name="document[]"][value="' + name + '"]').remove()
            },
            error: function(file) {
                alert("Only image will be accepted.");
                file.previewElement.remove();
            },
            init: function() {
                @if (isset($project) && $project->document)
                    var files = {!! json_encode($project->document) !!}
                    for (var i in files) {
                        var file = files[i]
                        this.options.addedfile.call(this, file)
                        file.previewElement.classList.add('dz-complete')
                        $('form').append('<input type="hidden" name="document[]" value="' + file.file_name + '">')
                    }
                @endif
            },
        }

        $(document).ready(function() {
            $('.custom-file-input').on("change", function(e) {
                $('.custom-file-label').html(e.target.files[0].name);
            });

            $('.buttons-create').attr({
                "data-toggle": "modal",
                "data-target": "#ourModal"
            });
        })

        $('#submitForm').on('click', function(event) {

            let formData = new FormData($('#driversForm')[0]);
            for (var pair of formData.entries()) {
                console.log(pair[0] + ', ' + pair[1]);
            }
            event.preventDefault();
            $.ajax({
                url: '/api/drivers',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: "json",
                success: function(responseData) {
                    $('#ourModal').modal("hide");
                    $('.buttons-reload').trigger('click');
                    Swal.fire(responseData.created);
                    $('#driversForm').trigger("reset");
                },
                error: function(responseError) {
                    $('#firstname').after($('<div>').addClass('invalid-feedback').css({
                        display: "block"
                    }).html(responseError.responseJSON.errors.firstname))
                    $('#lastname').after($('<div>').addClass('invalid-feedback').css({
                        display: "block"
                    }).html(responseError.responseJSON.errors.lastname))
                    $('#licensed_no').after($('<div>').addClass('invalid-feedback').css({
                        display: "block"
                    }).html(responseError.responseJSON.errors.licensed_no))
                    $('#description').after($('<div>').addClass('invalid-feedback').css({
                        display: "block"
                    }).html(responseError.responseJSON.errors.description))
                    $('#address').after($('<div>').addClass('invalid-feedback').css({
                        display: "block"
                    }).html(responseError.responseJSON.errors.address))
                    $('#document').after($('<div>').addClass('invalid-feedback').css({
                        display: "block"
                    }).html(responseError.responseJSON.errors.document))
                }
            })
        })

        $('input').on('keyup', function(event) {
            $(this).siblings(".invalid-feedback").css({
                display: "none",
            })
        });
        $('textarea').on('keyup', function(event) {
            $(this).siblings(".invalid-feedback").css({
                display: "none",
            })
        });
    </script>
@endsection
