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

        .image-container {
            display: flex;
            flex-direction: row;
            margin: 0 auto;
            flex-wrap: wrap;
        }

        .image-container div {
            width: fit-content;
        }

        .image-container img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin: 10px 10px;
            border: 1px solid rgba(0, 0, 0, .3);
        }

        .remove {
            position: relative;
            left: 80%;
            bottom: 31%;
            cursor: pointer;
        }

        .remove:hover {
            color: red;
        }
    </style>
    <div class="modal fade" id="ourModal" tabindex="-1" role="dialog" aria-labelledby="ourModalModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ourModalModalLabel" style="font-weight: 400;">Add New Driver</h5>
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

            $('.buttons-create').on('click', function() {
                $('.image-container').remove();
                $('#driversForm').trigger("reset");
            })
        })

        $('#submitForm').on('click', function(event) {
            event.preventDefault();
            let formData = new FormData($('#driversForm')[0]);
            // for (var pair of formData.entries()) {
            //     console.log(pair[0] + ', ' + pair[1]);
            // }
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
                    $('.invalid-feedback').remove();
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

        $(document).on('click', 'button.edit', function() {
            let id = $(this).attr('data-id');
            console.log(id);
            $.ajax({
                url: `/api/drivers/${id}/edit`,
                type: "GET",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(responseData) {
                    let driver = responseData;
                    $('#firstname').val(driver.fname);
                    $('#lastname').val(driver.lname);
                    $('#licensed_no').val(driver.licensed_no);
                    $('#address').val(driver.address);
                    $('#description').val(driver.description);
                    imageDisplay(driver.media, driver.id);
                },
                error: function(responseError) {
                    alert("error");
                },
            })
        })

        $(document).on('click', 'i.remove', function() {
            let id = $(this).attr("data-id");
            let driver_id = $(this).attr("data-driverId");
            $.ajax({
                url: `/api/drivers/${id}/images`,
                type: 'DELETE',
                data: {
                    "id": driver_id
                },
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(responseData) {
                    let driver = responseData;
                    $('.image-container').remove();
                    imageDisplay(driver.media, driver.id);
                },
                error: function() {
                    alert('error')
                }
            })
        })

        function imageDisplay(images, id) {
            let imageContainer = $('<div>').addClass('image-container');
            console.log(imageContainer)
            $.each(images, function(i, image) {
                imageContainer.append(`<div>
                            <i class="bi bi-x-square-fill remove" data-id="${image.id}" data-driverId="${id}"></i>
                            <img src="${image.original_url}">
                        </div>`);
            });
            $('.modal-body').append(imageContainer);
        }

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
