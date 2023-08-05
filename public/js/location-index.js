let table = $('#location-table').DataTable({
    ajax: {
        url: '/api/location',
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
            data: null,
            render: function (data) {
                return `<input class="model-id" type="checkbox" value="${data.id}"></input>`
            },
            sorting: false,
            class: "for-action",
        },
        {
            data: 'id'
        },
        {
            data: null,
            render: function (data) {
                return `<img class="model-image" src="${data.media[0]?.original_url}" alt="NONE">`
            },
            class: "data-image",

        },
        {
            data: 'street'
        },
        {
            data: 'baranggay'
        },
        {
            data: 'city'
        },
        {
            data: null,
            render: function (data) {
                return `<div class="action-buttons"><button type="button" data-toggle="modal" data-target="#locationModalCenter" data-id="${data.id}" class="btn btn-primary edit">
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

let save = $('button#save');
let update = $('button#update');
let formInModal = $('#location-form');
let ourModal = $('#locationModalCenter');
let validator;

$(function () {
    $('.buttons-create').attr({
        "data-toggle": "modal",
        "data-target": "#locationModalCenter",
    });
    validator = $('#location-form').validate({
        invalidHandler: function (form, validator) {
            var errors = validator.numberOfInvalids();
            if (errors) {
                var firstInvalidElement = $(validator.errorList[0].element);
                $('.content,.modal-content').scrollTop(firstInvalidElement.offset().top);
                firstInvalidElement.focus();
            }
        },
        rules: {
            street: {
                required: true,
                minlength: 5,
            },
            baranggay: {
                required: true,
                minlength: 5,
            },
            city: {
                required: true,
                minlength: 5,
            },
        },
        errorPlacement: function (error, element) {
            error.addClass('error-messages');
            error.appendTo(element.parent());
        }
    })
})

// function errorDisplay(errors) {
//     $('.invalid-feedback').remove();
//     $('#street').after($('<div>').addClass('invalid-feedback').css({
//         display: "block"
//     }).html(errors.street))
//     $('#baranggay').after($('<div>').addClass('invalid-feedback').css({
//         display: "block"
//     }).html(errors.baranggay))
//     $('#city').after($('<div>').addClass('invalid-feedback').css({
//         display: "block"
//     }).html(errors.city))
// }

// $('input').on('keyup', function (event) {
//     $(this).siblings(".invalid-feedback").css({
//         display: "none",
//     })
// });

// function clearError() {
//     $('input').siblings(".invalid-feedback").css({
//         display: "none",
//     })
// }

function saveButton() {
    save.css({
        display: "block",
    })
    update.css({
        display: "none",
    })
}

function updatebutton() {
    save.css({
        display: "none",
    })
    update.css({
        display: "block",
    })
}

function resetDropZone() {
    $('.dz-preview').remove()
    $('.dz-message').css({
        display: "block",
    })
    $('input[name="document[]"]').remove();
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

function appendToTop(newdata) {
    let tr = $('<tr>').addClass('newRow');
    tr.append($('<td>').html(`<input class="model-id" type="checkbox" value="${newdata.id}"></input>`))
    tr.append($('<td>').html(newdata.id));
    tr.append($('<td>').html(`<img class="model-image" src="${newdata.media[0]?.original_url}" alt="NONE">`));
    tr.append($('<td>').html(newdata.street));
    tr.append($('<td>').html(newdata.baranggay));
    tr.append($('<td>').html(newdata.city));
    tr.append($('<td>').html(
        `<div class="action-buttons"><button type="button" data-toggle="modal" data-target="#locationModalCenter" data-id="${newdata.id}" class="btn btn-primary edit">
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

    $('#location-table tbody').fadeIn(5000, function () {
        $(this).prepend(tr);
    })
    setTimeout(function () {
        tr.removeClass('newRow', 3000);
        table.ajax.reload();
    }, 3000)
}

function fillForm(data) {
    $('#street').val(data.street)
    $('#baranggay').val(data.baranggay)
    $('#city').val(data.city)
}

function imageDisplay(images, id) {
    let imageContainer = $('<div>').addClass('image-container');
    // console.log(imageContainer)
    $.each(images, function (i, image) {
        imageContainer.append(`<div>
                    <i class="bi bi-x-square-fill remove" data-id="${image.id}" data-modelID="${id}"></i>
                    <img src="${image.original_url}">
                </div>`);
    });
    $('.modal-body').append(imageContainer);
}

function createButton() {
    checks = [];
    $('#delete-selected').remove();
    $('tbody tr').css({
        "background-color": "transparent",
    })
    $('.for-action').children('.model-id').prop("checked", false)
    $('.modal-title').html('Add New Location');
    $('.image-container').remove()
    saveButton();
    formInModal.trigger('reset');
    resetDropZone();
}

save.on('click', function () {
    if ($("#location-form").valid()) {
        let formData = new FormData($('#location-form')[0]);
        // for (var pair of formData.entries()) {
        //     console.log(pair[0] + ', ' + pair[1]);
        // }
        $.ajax({
            url: '/api/location',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "json",
            success: function (responseData) {

                $('#locationModalCenter').modal("hide");
                formInModal.trigger("reset");
                resetDropZone()

                appendToTop(responseData);
                alertTopLeft("New location successfully added")

            },
            error: function (responseError) {
                // errorDisplay(responseError.responseJSON.errors);
            }
        })
    }
})

$('.modal').on('hidden.bs.modal', function (event) {
    $('tbody tr').css({
        "background-color": "transparent",
    })
    validator.resetForm();
    $('.error').css({
        "border-color": "#ced4da"
    })
    console.log("Dasd");
})

$(document).on('click', '.edit', function () {
    checks = [];
    $('#delete-selected').remove();
    $('tbody tr').css({
        "background-color": "transparent",
    })
    $('.for-action').children('.model-id').prop("checked", false)
    $('.modal-title').html('Edit Location');
    $('.image-container').remove();
    updatebutton();
    formInModal.trigger('reset');
    resetDropZone();
    let id = $(this).attr('data-id');
    update.attr({
        "data-id": id,
    })
    $(this).closest('tr').css({
        "background-color": "#91C8E4"
    })
    $.ajax({
        url: `/api/location/${id}/edit`,
        type: 'GET',
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-tokens"]').attr('content'),
        },
        dataType: "json",
        success: function (responseData) {
            let location = responseData;
            fillForm(location);
            // console.log(location);
            imageDisplay(location.media, location.id);
        },
        error: function (responseError) {
            alert("error");
        }

    })
})

$(document).on('click', 'i.remove', function () {
    let id = $(this).attr("data-id");
    let modelID = $(this).attr("data-modelID");
    $.ajax({
        url: `/api/location/${id}/images`,
        type: 'DELETE',
        data: {
            "id": modelID
        },
        dataType: "json",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (responseData) {
            let location = responseData;
            $('.image-container').remove();
            imageDisplay(location.media, location.id);
        },
        error: function () {
            alert('error')
        }
    })
})

update.on('click', function () {
    if ($("#location-form").valid()) {
        let id = $(this).attr("data-id");
        let formData = new FormData($('#location-form')[0]);
        for (var pair of formData.entries()) {
            console.log(pair[0] + ', ' + pair[1]);
        }
        formData.append('_method', 'PUT');
        $.ajax({
            url: `/api/location/${id}/update`,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "json",
            success: function (responseData) {
                $('#locationModalCenter').modal("hide");
                formInModal.trigger("reset");

                resetDropZone()
                $(`td:contains(${id})`).closest('tr').remove();
                appendToTop(responseData);
                alertTopLeft("Location updated successfully")

            },
            error: function (responseError) {
                // errorDisplay(responseError.responseJSON.errors);
            }
        })
    }
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
                url: `/api/location/${id}/delete`,
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

let checks = [];

$(document).on('click', 'input.model-id', function (event) {
    console.log("sadsad");
    if (!this.checked) {
        $(this).closest('tr').css({
            "background-color": "transparent"
        })
        checks.pop($(this).val())
        console.log(checks);
    } else {
        $(this).closest('tr').css({
            "background-color": "#FF6464"
        })
        checks.push($(this).val())
        console.log(checks);
    }
    if (checks.length > 0) {
        $('#delete-selected').remove();
        $('.dt-buttons').append(
            $('<button>').attr({
                "class": "btn btn-danger",
                "id": "delete-selected",
            }).html("Delete All")
        )
    } else {
        $('#delete-selected').remove();
    }
})

$(document).on('click', 'button#delete-selected', function (event) {
    let inputCheck = $(this);
    console.log("ASDSAd");
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
                url: `/api/location/multidelete`,
                type: "post",
                data: { "multipleID": checks },
                dataType: "json",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'),
                },
                success: function (responseData) {
                    $('#delete-selected').remove();
                    $.each(checks, function (index, value) {
                        $(`td:contains(${value})`).closest('tr').fadeOut(1000, function () {
                            $(this).remove()
                        })
                    })
                    setTimeout(function () {
                        table.ajax.reload();
                        alertTopLeft("Successfully Deleted!");
                    }, 2000)
                    checks = [];
                },
                error: function () {

                }
            });

        } else if (result.dismiss === Swal.DismissReason.cancel) {
            // $('tr').css({
            //     "background-color": "transparent"
            // })
        }
    })
})

$(document).on('click', 'button.view', function () {
    $('.for-model-images').empty();
    let id = $(this).attr('data-id');
    console.log(id);
    $.ajax({
        url: `/api/location/view/${id}/images`,
        type: 'GET',
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-tokens"]').attr('content'),
        },
        dataType: "json",
        success: function (data) {

            $.each(data, function (i, value) {
                console.log(value.original_url);
                $('.for-model-images').append(
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
