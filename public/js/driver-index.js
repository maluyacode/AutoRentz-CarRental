let table = $('#drivers-table').DataTable({
    ajax: {
        url: '/api/drivers',
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
            data: 'fname'
        },
        {
            data: 'lname'
        },
        {
            data: 'licensed_no'
        },
        {
            data: 'description'
        },
        {
            data: 'address'
        },
        {
            data: null,
            render: function (data) {
                return `<span class=${data.driver_status}>${data.driver_status}</span>`;
            },
            class: 'status'
        },
        {
            data: null,
            render: function (data) {
                return `<div class="action-buttons"><button type="button" data-toggle="modal" data-target="#ourModal" data-id="${data.id}" class="btn btn-primary edit">
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
let validator;
$(function () {

    $('.custom-file-input').on("change", function (e) {
        $('.custom-file-label').html(e.target.files[0].name);
    });

    $('.buttons-create').attr({
        "data-toggle": "modal",
        "data-target": "#ourModal"
    });

    validator = $('#driversForm').validate({
        invalidHandler: function (form, validator) {
            var errors = validator.numberOfInvalids();
            if (errors) {
                var firstInvalidElement = $(validator.errorList[0].element);
                $('.content,.modal-content').scrollTop(firstInvalidElement.offset().top);
                firstInvalidElement.focus();
            }
        },
        rules: {
            firstname: {
                required: true,
            },
            lastname: {
                required: true,
            },
            licensed_no: {
                required: true,
                minlength: 5,
            },
            address: {
                required: true,
            },
            description: {
                required: true,
                minlength: 10,
            },

        },
        errorPlacement: function (error, element) {
            error.addClass('error-messages');
            error.appendTo(element.parent());
        }
    })
})

function createButton() {
    $('.modal-title').html('Add New Driver');
    $('.image-container').remove();
    $('#driversForm').trigger("reset");
    $('#updateForm').html('Save');
}

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



function colorRow() {
    let firstChild = $('#driver-table tbody tr:first-child');
    firstChild.addClass('newRow');
    setTimeout(function () {
        firstChild.removeClass('newRow', 3000);
    }, 3000)
}

$('#driversForm').on('submit', function (event) {
    event.preventDefault();
    if ($('#driversForm').valid()) {

        let formData = new FormData($('#driversForm')[0]);
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
                $('html, body').animate({
                    scrollTop: $(".main-header").offset().top
                }, 'fast');
                console.log(responseData);
                $('#ourModal').modal("hide");
                $('.buttons-reload').trigger('click');
                $('#driversForm').trigger("reset");
                $('.dz-preview').remove()
                $('.dz-message').css({
                    display: "block",
                })
                $('input[name="document[]"]').remove();
                appendToTop(responseData);
                alertTopLeft("New driver successfully added!")
            },
            error: function (responseError) {
                // errorDisplay(responseError.responseJSON.errors);
            }
        })

    }
})

$(document).on('click', 'button.edit', function () {
    $('.modal-title').html('Edit Driver Details');
    $('#submitForm').attr({
        id: "updateForm",
    }).html('Update');

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
    if ($('#driversForm').valid()) {
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
                console.log(responseData);
                $('html, body').animate({
                    scrollTop: $(".main-header").offset().top
                }, 'fast');
                $(`td:contains(${id})`).closest('tr').remove();
                appendToTop(responseData);
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
                // errorDisplay(responseError.responseJSON.errors);
            }
        })
    }
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
                        table.ajax.reload();
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

$('#ourModal').on('hidden.bs.modal', function () {
    validator.resetForm();
    $('.error').css({
        "border-color": "#ced4da"
    })
    console.log("Dasd");
})

function appendToTop(newdata) {
    console.log(newdata);
    let tr = $('<tr>').addClass('newRow');
    tr.append($('<td>').html(newdata.id));
    tr.append($('<td>').html(`<img class="model-image" src="${newdata.media[0]?.original_url}" alt="NONE">`));
    tr.append($('<td>').html(newdata.fname));
    tr.append($('<td>').html(newdata.lname));
    tr.append($('<td>').html(newdata.licensed_no));
    tr.append($('<td>').html(newdata.description));
    tr.append($('<td>').html(newdata.address));
    tr.append($('<td>').html(newdata.driver_status));
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
    $('#drivers-table tbody').prepend(tr);
    setTimeout(function () {
        tr.removeClass('newRow', 3000);
        table.ajax.reload();
    }, 4000)
}


$(document).on('click', 'button.view', function () {
    $('.driver-images').empty();
    let id = $(this).attr('data-id');
    $.ajax({
        url: `/api/drivers/${id}/view/images`,
        type: 'GET',
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-tokens"]').attr('content'),
        },
        dataType: "json",
        success: function (data) {
            console.log(data);
            $.each(data.media, function (i, value) {
                $('.driver-images').append(
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

$('#importExcel').on('submit', function (e) {
    e.preventDefault()
    let formData = new FormData($(this)[0]);
    $.ajax({
        url: '/api/drivers/import',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: "json",
        success: function (responseData) {
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
