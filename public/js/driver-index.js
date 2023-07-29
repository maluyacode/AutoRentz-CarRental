$(function () {

    $('.custom-file-input').on("change", function (e) {
        $('.custom-file-label').html(e.target.files[0].name);
    });

    $('.buttons-create').attr({
        "data-toggle": "modal",
        "data-target": "#ourModal"
    });

    $('.buttons-create').on('click', function () {
        $('.modal-title').html('Add New Driver');
        clearError($('input'), $('textarea'));
        $('.image-container').remove();
        $('#driversForm').trigger("reset");
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

function imageDisplay(images, id) {
    let imageContainer = $('<div>').addClass('image-container');
    console.log(imageContainer)
    $.each(images, function (i, image) {
        imageContainer.append(`<div>
                    <i class="bi bi-x-square-fill remove" data-id="${image.id}" data-driverId="${id}"></i>
                    <img src="${image.original_url}">
                </div>`);
    });
    $('.modal-body').append(imageContainer);
}

function errorDisplay(errors) {
    $('.invalid-feedback').remove();
    $('#firstname').after($('<div>').addClass('invalid-feedback').css({
        display: "block"
    }).html(errors.firstname))
    $('#lastname').after($('<div>').addClass('invalid-feedback').css({
        display: "block"
    }).html(errors.lastname))
    $('#licensed_no').after($('<div>').addClass('invalid-feedback').css({
        display: "block"
    }).html(errors.licensed_no))
    $('#description').after($('<div>').addClass('invalid-feedback').css({
        display: "block"
    }).html(errors.description))
    $('#address').after($('<div>').addClass('invalid-feedback').css({
        display: "block"
    }).html(errors.address))
    $('#document').after($('<div>').addClass('invalid-feedback').css({
        display: "block"
    }).html(errors.document))
}

function clearError(objInputs, objTextArea) {
    $(objInputs).siblings(".invalid-feedback").css({
        display: "none",
    })
    $(objTextArea).siblings(".invalid-feedback").css({
        display: "none",
    })
}

$('input').on('keyup', function (event) {
    $(this).siblings(".invalid-feedback").css({
        display: "none",
    })
});

$('textarea').on('keyup', function (event) {
    $(this).siblings(".invalid-feedback").css({
        display: "none",
    })
});

function colorRow() {
    let firstChild = $('#driver-table tbody tr:first-child');
    firstChild.addClass('newRow');
    setTimeout(function () {
        firstChild.removeClass('newRow', 3000);
    }, 3000)
}

$('#driversForm').on('submit', function (event) {
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
        success: function (responseData) {
            $('#ourModal').modal("hide");
            $('.buttons-reload').trigger('click');
            Swal.fire(responseData.created);
            $('#driversForm').trigger("reset");
            $('.dz-preview').remove()
            $('.dz-message').css({
                display: "block",
            })
            $('input[name="document[]"]').remove();
            alertTopLeft("New driver successfully added!")
        },
        error: function (responseError) {
            errorDisplay(responseError.responseJSON.errors);
        }
    })
})

$(document).on('click', 'button.edit', function () {
    $('.modal-title').html('Edit Driver Details');
    clearError($('input'), $('textarea'));
    $('#submitForm').attr({
        id: "updateForm",
    });

    $('.image-container').remove();
    let id = $(this).attr('data-id');

    $.ajax({
        url: `/api/drivers/${id}/edit`,
        type: "GET",
        dataType: "json",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (responseData) {
            let driver = responseData;
            $('#driver_id').val(driver.id)
            $('#firstname').val(driver.fname);
            $('#lastname').val(driver.lname);
            $('#licensed_no').val(driver.licensed_no);
            $('#address').val(driver.address);
            $('#description').val(driver.description);
            imageDisplay(driver.media, driver.id);
        },
        error: function (responseError) {
            alert("error");
        },
    })
})

$(document).on('click', '#updateForm', function (event) {
    event.preventDefault();
    let id = $('#driver_id').val();
    let formData = new FormData($('#driversForm')[0]);
    for (var pair of formData.entries()) {
        console.log(pair[0] + ', ' + pair[1]);
    }
    formData.append('_method', 'PUT');
    $.ajax({
        url: `/api/drivers/${id}/update`,
        type: 'POST',
        contentType: false,
        processData: false,
        data: formData,
        dataType: "json",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (responseData) {
            $('#ourModal').modal("hide");
            $('#driver-table').DataTable().ajax.reload();

            $('#driversForm').trigger("reset");
            $('#updateForm').attr({
                id: "submitForm",
            });

            $('.dz-preview').remove()
            $('.dz-message').css({
                display: "block",
            })

            $('input[name="document[]"]').remove();
            alertTopLeft("Driver updated successfully")
            setTimeout(function () {
                colorRow()
            }, 1000)
        },
        error: function (responseError) {
            errorDisplay(responseError.responseJSON.errors);
        }
    })
});

$(document).on('click', 'i.remove', function () {
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
        success: function (responseData) {
            let driver = responseData;
            $('.image-container').remove();
            imageDisplay(driver.media, driver.id);
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
                url: `/api/drivers/${id}/delete`,
                type: "DELETE",
                dataType: "json",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'),
                },
                success: function () {
                    objDelete.closest('tr').fadeOut(500, function () {
                        $(this).remove();
                    })
                    setTimeout(function () {
                        $('#driver-table').DataTable().ajax.reload();
                        alertTopLeft("Successfully Deleted!");
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
