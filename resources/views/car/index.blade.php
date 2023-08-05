@extends('admin.index')
@section('pageStyles')
    <link rel="stylesheet" href="{{ asset('css/car-index.css') }}">
@endsection
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">List of Cars</h3>
                        </div>
                        <div class="card-body">
                            <table id="car-table" class="table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Image</th>
                                        <th>Platenumber</th>
                                        <th>Model</th>
                                        <th>Type</th>
                                        <th>Manufacturer</th>
                                        <th>Seats</th>
                                        <th>Accessories</th>
                                        <th>Rent Price</th>
                                        <th>Status</th>
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
    <div class="modal fade bd-example-modal-lg " id="carModal" tabindex="-1" role="dialog"
        aria-labelledby="carModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="carModalTitle">Add Car</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <form id="carForm" action="#" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-sm-4">
                                    <!-- text input -->
                                    <div class="form-group">
                                        <label>Plate No:</label>
                                        <input type="text" class="form-control" placeholder="Enter car platenumber"
                                            name="platenumber" id="platenumber">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Price Per Day</label>
                                        <input type="text" class="form-control" placeholder="Enter car price per day"
                                            name="price_per_day" id="price_per_day">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Cost Price</label>
                                        <input type="text" class="form-control" placeholder="Enter car cost price"
                                            name="cost_price" id="cost_price">
                                    </div>
                                </div>
                            </div>
                            <div class="row for-image">
                                <div class="col-sm-12">
                                    <!-- textarea -->
                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea type="text" class="form-control" rows="6"
                                            placeholder="Say something about the car. ex. Styles, Engine description" name="description" id="description"></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-12   ">
                                    <div class="form-group" style="margin:0">
                                        <label for="exampleInputFile">Images (Optional)</label>
                                        <div class="dropzone" id="dropzone-image"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="col-form-label" for="inputSuccess">Car Model</label>
                                        <select class="form-control" id="model-select" placeholder="Enter ..."
                                            name="model_id">
                                            <option class="dont-clear" value="">
                                                Select car model
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="col-form-label" for="inputSuccess">No: of Seats</label>
                                        <input type="number" class="form-control no-arrow" placeholder="Seat no:"
                                            id="seats" name="seats" min="2" max="15">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="col-form-label" for="inputWarning">Transmission Type</label>
                                        <select class="form-control" id="transmission-select" placeholder="Enter ..."
                                            name="transmission_id">
                                            <option class="dont-clear" value="">
                                                Select transmission type
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="col-form-label" for="inputError">Fuel Type</label>
                                        <select class="form-control" id="fuel-select" placeholder="Enter ..."
                                            name="fuel_id">
                                            <option class="dont-clear" value="">
                                                Select fuel type
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <h6>Choose existing accessories for car</h6>
                            <div class="row checkbox-container">

                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button id="save" type="button" class="btn btn-primary">Save</button>
                    <button id="update" type="button" class="btn btn-primary">Update</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bd-example-modal-lg" id="imagesModal" tabindex="-1" role="dialog"
        aria-labelledby="imagesModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imagesModalLongTitle">Car Images</h5>
                </div>
                <div class="modal-body car-images">
                    ..
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
                url: '{{ route('cars.storeMedia') }}',
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
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    <script src="{{ asset('js/car-index.js') }}"></script>
    {{-- <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.js"></script> --}}
@endsection
