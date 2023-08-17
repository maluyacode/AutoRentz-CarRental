let table = $('#accessories-table').DataTable({
    ajax: {
        url: '/api/accessories',
        dataSrc: '',
        contentType: 'application/json',
    },
    responsive: true,
    autoWidth: false,
    dom: 'Bfrtip',
    buttons: [
        {
            text: '<i class="fas fa-plus"></i> Create',
            action: createButton,
            className: "buttons-create",
        },
        {
            text: '<i class="fas fa-copy"></i> Copy',
            extend: 'copyHtml5'
        },
        {
            text: '<i class="far fa-file-excel"></i> Excel',
            extend: 'excelHtml5'
        },
        {
            text: '<i class="fas fa-file-csv"></i> CSV',
            extend: 'csvHtml5'
        },
        {
            text: '<i class="fas fa-file-pdf"></i> PDF',
            extend: 'pdfHtml5'
        },
    ],
    columns: [
        {
            data: 'id'
        },
        {
            data: null,
            render: function (data) {
                return `<img class="model-image" src="${data.media[0]?.original_url || '/storage/images/Logo.png'}" alt="NONE">`
            },
            class: "data-image",

        },
        {
            data: 'name'
        },
        {
            data: null,
            render: function (data) {
                return "&#8369;" + data.fee;
            }
        },
        {
            data: null,
            render: function (data) {
                return `<div class="action-buttons"><button type="button" data-toggle="modal" data-target="#accessoriesModal" data-id="${data.id}" class="btn btn-primary edit">
            <i class="bi bi-pencil-square"></i>
                </button>
                <button type="button" data-id="${data.id}" class="btn btn-danger btn-delete delete">
                    <i class="bi bi-trash3" style="color:white"></i>
                </button>
                <button type="button" data-id="${data.id}" class="btn btn-warning btn-delete view" data-toggle="modal" data-target="#imagesModal">
                    <i class="bi bi-eye" style="color:white"></i>
                </button>
            </div>`;
            }
        }
    ]
});

let validator
$(function () {
    $('.buttons-create').attr({
        "data-toggle": "modal",
        "data-target": "#accessoriesModal",
    });
    validator = $('#accessories-form').validate({
        invalidHandler: function (form, validator) {
            var errors = validator.numberOfInvalids();
            if (errors) {
                var firstInvalidElement = $(validator.errorList[0].element);
                $('.content,.modal-content').scrollTop(firstInvalidElement.offset().top);
                firstInvalidElement.focus();
            }
        },
        rules: {
            name: {
                required: true,
                minlength: 5,
            },
            fee: {
                required: true,
                number: 5,
                range: [10, 300]
            },
        },
        errorPlacement: function (error, element) {
            error.addClass('error-messages');
            error.appendTo(element.parent());
        }
    })
})

function alertTopLeft(message) {
    Swal.fire({
        position: 'top-end',
        icon: 'success',
        title: message,
        showConfirmButton: false,
        timer: 3000,
        width: "175px",
        backdrop: false,
        background: '#E8FFCE',
    })
}

function resetDropZone() {
    $('.dz-preview').remove()
    $('.dz-message').css({
        display: "block",
    })
    $('input[name="document[]"]').remove();
}

function imageDisplay(images, id) {
    $('.image-container').remove();
    let imageContainer = $('<div>').addClass('image-container');
    // console.log(imageContainer)
    $.each(images, function (i, image) {
        imageContainer.append(`<div>
                    <i class="bi bi-x-square-fill remove" data-id="${image.id}" data-modelID="${id}"></i>
                    <img src="${image.original_url}">
                </div>`);
    });
    $('.for-image').append(imageContainer);
}

function createButton() {
    $('#update').hide()
    $('#save').show()
    $('#accessoriesModalLabel').html('Add Accessory')
    $('.image-container').remove();
    $('#accessories-form').trigger("reset");
}

$(document).on('click', 'button.edit', function () {
    let id = $(this).attr('data-id');
    $('#save').hide()
    $('#update').show()
    $('#update').attr({
        "data-id": id
    })
    $('#accessoriesModalLabel').html('Edit Accessory')

    $.ajax({
        url: `/api/accessories/${id}/edit`,
        type: 'GET',
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-tokens"]').attr('content'),
        },
        dataType: "json",
        success: function (data) {
            $('#name').val(data.name);
            $('#fee').val(data.fee);
            imageDisplay(data.media, data.id)
        },
        error: function (error) {
            alert("error");
        }

    })
})

$('#save').on('click', function () {

    if ($("#accessories-form").valid()) {
        let formData = new FormData($('#accessories-form')[0]);
        $('#accessoriesModal *').attr({
            "disabled": "disabled",
        })
        $.ajax({
            url: '/api/accessories/',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "json",
            success: function (responseData) {
                console.log(responseData);
                $('#accessoriesModal *').attr({
                    "disabled": false,
                })
                $('#accessoriesModal').modal('hide');
                appendToTop(responseData);
                alertTopLeft('New accessory successfully added');
                resetDropZone();

            },
            error: function (responseError) {
                // errorDisplay(responseError.responseJSON.errors);
            }
        })
    }

})


$('#update').on('click', function () {

    if ($("#accessories-form").valid()) {

        let id = $(this).attr("data-id");
        let formData = new FormData($('#accessories-form')[0]);
        $('#accessoriesModal *').attr({
            "disabled": "disabled",
        })
        formData.append('_method', 'PUT');
        $.ajax({
            url: `/api/accessories/${id}`,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "json",
            success: function (responseData) {
                $(`td:contains(${id})`).closest('tr').remove();
                console.log(responseData);
                appendToTop(responseData);
                $('html, body').animate({
                    scrollTop: $(".dataTables_wrapper").offset().top
                }, 'fast');
                $('#accessoriesModal *').attr({
                    "disabled": false,
                })
                $('#accessoriesModal').modal('hide');
                alertTopLeft('Car updated successfully');
                resetDropZone();

            },
            error: function (responseError) {
                // errorDisplay(responseError.responseJSON.errors);
                alert("error")
            }
        })
    }
})

$(document).on('click', 'i.remove', function () {
    let id = $(this).attr("data-id");
    let modelID = $(this).attr("data-modelID");
    $.ajax({
        url: `/api/accessories/${id}/images`,
        type: 'DELETE',
        data: {
            "id": modelID
        },
        dataType: "json",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (responseData) {
            $('.image-container').remove();
            imageDisplay(responseData.media, responseData.id);
        },
        error: function () {
            alert('error')
        }
    })
})

const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-success',
        cancelButton: 'btn btn-danger'
    },
    buttonsStyling: false
})

$(document).on('click', 'button.delete', function () {
    let id = $(this).attr('data-id');
    let objDelete = $(this);
    objDelete.closest('tr').css({
        "background-color": "#FF6464"
    })
    swalWithBootstrapButtons.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/api/accessories/${id}`,
                type: "DELETE",
                dataType: "json",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'),
                },
                success: function () {
                    objDelete.closest('tr').fadeOut(500, function () {
                        $(this).remove();
                        alertTopLeft("Successfully Deleted!");
                    })
                    setTimeout(function () {
                        table.ajax.reload();
                    }, 1000)
                },
                error: function () {

                }
            });

        } else if (result.dismiss === Swal.DismissReason.cancel) {
            objDelete.closest('tr').css({
                "background-color": "transparent"
            })
        }
    })
})

function appendToTop(newdata) {
    let tr = $('<tr>').addClass('newRow');
    tr.append($('<td>').html(newdata.id));
    tr.append($('<td>').html(`<img class="model-image" src="${newdata.media[0]?.original_url}" alt="NONE">`));
    tr.append($('<td>').html(newdata.name));
    tr.append($('<td>').html(newdata.fee));
    tr.append($('<td>').html(
        `<div class="action-buttons"><button type="button" data-toggle="modal" data-target="#accessoriesModal" data-id="${newdata.id}" class="btn btn-primary edit">
            <i class="bi bi-pencil-square"></i>
                </button>
                <button type="button" data-id="${newdata.id}" class="btn btn-danger btn-delete delete">
                    <i class="bi bi-trash3" style="color:white"></i>
                </button>
                <button type="button" data-id="${newdata.id}" class="btn btn-warning btn-delete view" data-toggle="modal" data-target="#imagesModal">
                    <i class="bi bi-eye" style="color:white"></i>
                </button>
        </div>`
    ));
    $('#accessories-table tbody').prepend(tr);
    setTimeout(function () {
        tr.removeClass('newRow', 3000);
        table.ajax.reload();
    }, 4000)
}
$(document).on('click', 'button.view', function () {
    $('.accessories-images').empty();
    let id = $(this).attr('data-id');
    $.ajax({
        url: `/api/accessories/${id}/view/images`,
        type: 'GET',
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-tokens"]').attr('content'),
        },
        dataType: "json",
        success: function (data) {
            console.log(data);
            $.each(data.media, function (i, value) {
                $('.accessories-images').append(
                    $('<img>').attr({
                        "src": value.original_url
                    }).css({
                        "width": "200px",
                        "height": "200px",
                        "object-fit": "cover",
                        "border": "1px solid black",
                        "margin": "10px",
                    })
                );
            })
        },
        error: function (error) {
            alert("error");
        }

    })
});

$('#ourModal').on('hidden.bs.modal', function () {
    validator.resetForm();
    $('.error').css({
        "border-color": "#ced4da"
    })
    console.log("Dasd");
})

$('.custom-file-input').on("change", function (e) {
    $('.custom-file-label').html(e.target.files[0].name);
});

$('#importExcel').on('submit', function (e) {
    e.preventDefault()
    let formData = new FormData($(this)[0]);
    $.ajax({
        url: '/api/accessories/import',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: "json",
        success: function (responseData) {
            console.log("asdsad");
            $('.custom-file-label').html('Import Excel Records');
            alertTopLeft('Imported Successfully');
            $('#importExcel').trigger("reset");
            table.ajax.reload();
        },
        error: function (responseError) {
            // errorDisplay(responseError.responseJSON.errors);
        }
    })
})
