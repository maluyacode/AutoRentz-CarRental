@extends('admin.index')

@section('header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Transmissions</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Car</a></li>
                        <li class="breadcrumb-item active">Transmissions</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection

@section('content')
    @include('layouts.session-messages')
    <!-- Button trigger modal -->
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
                            {!! $dataTable->table() !!}
                        </div>
                        <!-- /.card-body -->
                    </div>
                </div>
            </div>
        </div>
    </section>
    {!! $dataTable->scripts() !!}
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Create New Transmission</h5>
                    {{-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button> --}}
                </div>
                <form id="transmissionForm" action="#">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" placeholder="Enter transmission name"
                                name="name">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="cancel" type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button id="submitForm" type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('.dt-buttons').prepend($('<button>') // append button to create to datatablse dt-button
                .attr({
                    "data-toggle": "modal",
                    "data-target": "#exampleModal"
                })
                .addClass('btn btn-secondary')
                .html('Create'));

            $('#exampleModal').on('hidden.bs.modal', function() { // remove input with id value if modal is close
                $('input[type="hidden"]').remove();
                $('#updateForm').attr({
                    'id': 'submitForm'
                });
                $('#transmissionForm').trigger("reset");
            });
        });


        $('#transmissionForm').submit(function(event) { // form submit, store
            event.preventDefault();
            let formData = new FormData($(this)[0]);
            console.log(formData);
            $.ajax({
                type: "POST",
                url: "/api/transmission",
                data: formData,
                contentType: false,
                processData: false,
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(responseData) {
                    $('#exampleModal').modal('hide');
                    $('.buttons-reload').trigger('click');
                    Swal.fire(responseData.created);
                },
                error: function(responseError) {
                    $('small').remove();
                    $('#name').after($('<small>')
                        .html(responseError.responseJSON.errors.name)
                        .css({
                            "color": "red"
                        }))
                }

            });
            $(this).trigger("reset"); // reset the form
        });

        $(document).on('click', '.edit', function() { // prepare data for from update / specific button selection
            $('#transmissionForm').trigger("reset");
            let id = $(this).attr('data-id');
            let name = $(this).attr('data-name');
            $('#transmissionForm').prepend($('<input type="hidden" value="' + id + '" name="transmission_id">'))
            $('#name').val(name);
            $('#submitForm').attr({
                'id': 'updateForm'
            });
        });

        $('#updateForm').on('click', function(event) {
            event.preventDefault();
            let formData = {
                name: $('#name').val()
            }
            $.ajax({
                url: "/api/transmission/" + $('input[type="hidden"]').val(),
                type: "PUT",
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(responseData) {
                    $('#exampleModal').modal('hide');
                    $('.buttons-reload').trigger('click');
                    $('input[type="hidden"]').remove();
                    Swal.fire(responseData.update);
                },
                error: function(responseError) {
                    $('small').remove();
                    $('#name').after($('<small>')
                        .html(responseError.responseJSON.errors.name)
                        .css({
                            "color": "red"
                        }))
                }
            })
        });

        $(document).on('click', '.delete', function() {
            Swal.fire({
                title: 'Confirmation',
                text: 'Are you sure?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "/api/transmission/delete/" + $(this).attr('data-id'),
                        type: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(responseData) {
                            $('.buttons-reload').trigger('click');
                            Swal.fire(responseData.deleted);
                        },
                        error: function(responseError) {
                            alert('Error occured hacker ka siguro!');
                        }
                    })

                }
            });
        });

        // validate error message, remove
        $('#name').on('keyup', function() {
            if ($(this).val().length > 4) {
                if ($('small')) {
                    $('small').remove();
                }
            }
        });
    </script>
@endsection
