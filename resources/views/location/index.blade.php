@extends('admin.index')

@section('pageStyles')
    <link rel="stylesheet" href="{{ asset('css/location-index.css') }}">
@endsection
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">List of Locations</h3>
                        </div>
                        <div style="padding: 20px">
                            <table id="location-table" class="table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Street</th>
                                        <th>Baranggay</th>
                                        <th>City</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade" id="locationModalCenter" tabindex="-1" role="dialog"
        aria-labelledby="locationModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="location-form" action="#" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label>Street</label>
                                <input type="text" class="form-control" name="street">
                            </div>
                            <div class="form-group">
                                <label>Baranggay</label>
                                <input type="text" class="form-control" name="baranggay">
                            </div>
                            <div class="form-group">
                                <label>City</label>
                                <input type="text" class="form-control" name="city">
                            </div>
                            <div class="form-group">
                                <label for="images">Upload Image (Optional)</label>
                                <div class="dropzone" id="dropzone-image"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button id="save" type="button" class="btn btn-primary">Save New</button>
                    <button id="update" type="button" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('pageScripts')
    <script>
        initilizeDropzone();
        var uploadedDocumentMap = {}

        function initilizeDropzone() {
            Dropzone.options.dropzoneImage = {
                url: '{{ route('location.storeMedia') }}',
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
    <script src="{{ asset('js/location-index.js') }}"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.js"></script>
@endsection
