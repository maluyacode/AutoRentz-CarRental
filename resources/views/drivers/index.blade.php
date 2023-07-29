@extends('admin.index')
@include('layouts.session-messages')

@section('pageStyles')
    <link rel="stylesheet" href="{{ asset('css/driver-index.css') }}">
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">List of Drivers</h3>
                            <form action="{{ route('drivers.import') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="input-group mb-3" style="width: 50%">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="inputGroupFile04"
                                            name="excel">
                                        <label class="custom-file-label" for="inputGroupFile04">Import Excel
                                            Records</label>
                                    </div>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="submit">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div style="padding: 20px">
                            {!! $dataTable->table() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="ourModal" tabindex="-1" role="dialog" aria-labelledby="ourModalModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ourModalModalLabel" style="font-weight: 400;"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="driversForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <input id="driver_id" type="hidden" name="driver_id">
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
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="submitForm">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {!! $dataTable->scripts() !!}

    <script>
        initilizeDropzone();
        var uploadedDocumentMap = {}

        function initilizeDropzone() {
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
                    $('.dz-message').css({
                        display: "block",
                    })
                },
                init: function() {
                    @if (isset($project) && $project->document)
                        var files = {!! json_encode($project->document) !!}
                        for (var i in files) {
                            var file = files[i]
                            this.options.addedfile.call(this, file)
                            file.previewElement.classList.add('dz-complete')
                            $('form').append('<input type="hidden" name="document[]" value="' + file.file_name +
                                '">')
                        }
                    @endif
                },
            }
        }
    </script>
@endsection

@section('pageScripts')
    <script src="{{ asset('js/driver-index.js') }}"></script>
@endsection
