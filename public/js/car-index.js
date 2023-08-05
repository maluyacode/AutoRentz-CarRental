let table = $('#car-table').DataTable({
    ajax: {
        url: '/api/cars',
        dataSrc: '',
        // contentType: 'application/json',
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
                return `<img class="model-image" src="${data.media[0]?.original_url}" alt="NONE">`
            },
            class: "data-image",

        },
        {
            data: 'platenumber'
        },
        {
            data: null,
            render: function (data) {
                return `${data.modelo.name} ${data.modelo.year}`;
            }
        },
        {
            data: 'modelo.type.name'
        },
        {
            data: 'modelo.manufacturer.name'
        },
        {
            data: 'seats'
        },
        {
            data: null,
            render: function (data) {
                let accessories = data.accessories.map(function (value) {
                    return value.name;
                }).join('<br />');
                return accessories;
            }
        },
        {
            data: null,
            render: function (data) {
                let price = data.accessories.map(function (value) {
                    return value.fee;
                });
                price.push(data.price_per_day);
                return "&#8369;" + sum(price).toFixed(2);
            }
        },
        {
            data: 'car_status'
        },
        {
            data: null,
            render: function (data) {
                return `<div class="action-buttons"><button type="button" data-toggle="modal" data-target=".bd-example-modal-lg" data-id="${data.id}" class="btn btn-primary edit">
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
        "data-target": ".bd-example-modal-lg",
    });

    jQuery.validator.addMethod("numberNotStartWithZero", function (value, element) {
        return this.optional(element) || /^[1-9][0-9]*$/i.test(value);
    }, "Please enter a valid number. (Do not start with zero)");

    validator = $('#carForm').validate({
        invalidHandler: function (form, validator) {
            var errors = validator.numberOfInvalids();
            if (errors) {
                var firstInvalidElement = $(validator.errorList[0].element);
                $('.content,.modal-content').scrollTop(firstInvalidElement.offset().top);
                firstInvalidElement.focus();
            }
        },
        rules: {
            platenumber: {
                required: true,
                minlength: 5,
            },
            price_per_day: {
                required: true,
                number: true,
                // numberNotStartWithZero: true,
            },
            cost_price: {
                required: true,
                number: true,
                // numberNotStartWithZero: true,
            },
            description: {
                required: true,
                minlength: 10,
            },
            model_id: {
                required: true,
            },
            seats: {
                required: true,
                number: true,
                range: [2, 10],
                numberNotStartWithZero: true,
            },
            transmission_id: {
                required: true,
            },
            fuel_id: {
                required: true,
            },
            'accessories_id[]': {
                required: true,
            }
        },
        messages: {
            'accessories_id[]': {
                required: "You must check at least 1 box",
            }
        },
        errorPlacement: function (error, element) {
            error.addClass('error-messages');
            error.appendTo(element.parent());
        }
    })
})

function sum(input) {
    if (toString.call(input) !== "[object Array]")
        return false;
    var total = 0;
    for (var i = 0; i < input.length; i++) {
        if (isNaN(input[i])) {
            continue;
        }
        total += Number(input[i]);
    }
    return total;
}

function selectInputs(models, fuels, transmissions) {
    $.each(models, function (i, value) {
        $('#model-select').append(
            $('<option>').attr({
                "value": value.id
            }).css({
                "text-transform": "capitalize"
            }).html(`${value.name} ${value.year}`)
        )
    })
    $.each(transmissions, function (i, value) {
        $('#transmission-select').append(
            $('<option>').attr({
                "value": value.id
            }).css({
                "text-transform": "capitalize"
            }).html(value.name)
        )
    })
    $.each(fuels, function (i, value) {
        $('#fuel-select').append(
            $('<option>').attr({
                "value": value.id
            }).css({
                "text-transform": "capitalize"
            }).html(value.name)
        )
    })
}

function checkBoxes(accessories, check = '') {
    $.each(accessories, function (i, value) {

        let html = ` <div class="col-lg-3 col-sm-6">
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="accessories_id[]"
                                    value="${value.id}" ${check}>
                                <label class="form-check-label">${value.name}</label>
                            </div>
                        </div>
                    </div>`
        $('.checkbox-container').append(html)
    });
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

function createButton() {
    $('#update').hide()
    $('#save').show()
    $('#carModalTitle').html('Add Car')
    $('.image-container').remove();

    $.ajax({
        url: `/api/cars/create`,
        type: 'GET',
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-tokens"]').attr('content'),
        },
        dataType: "json",
        success: function (data) {
            $('select').empty();
            $('.checkbox-container').empty();
            $('#carForm').trigger("reset");


            $('#model-select').append($('<option>').attr({ "value": "" }).html('Select car model'))
            $('#transmission-select').append($('<option>').attr({ "value": "" }).html('Select transmission type'))
            $('#fuel-select').append($('<option>').attr({ "value": "" }).html('Select fuel type'))

            selectInputs(data.models, data.fuels, data.transmissions)
            checkBoxes(data.accessories)
        },
        error: function (error) {
            alert("error");
        }

    })
}

$(document).on('click', 'button.edit', function () {
    let id = $(this).attr('data-id');
    $('#save').hide()
    $('#update').show()
    $('#update').attr({
        "data-id": id
    })
    $('#carModalTitle').html('Edit Car')
    $(this).closest('tr').css({
        "background-color": "#91C8E4"
    })
    $.ajax({
        url: `/api/cars/${id}/edit`,
        type: 'GET',
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-tokens"]').attr('content'),
        },
        dataType: "json",
        success: function (data) {
            $('select').empty();
            $('.checkbox-container').empty();
            $('#carForm').trigger("reset");

            $('#platenumber').val(data.car.platenumber);
            $('#price_per_day').val(data.car.price_per_day);
            $('#cost_price').val(data.car.cost_price);
            $('#description').val(data.car.description);
            $('#seats').val(data.car.seats);

            $('#model-select').append($('<option>').attr({
                "value": data.car.modelo.id
            }).html(`${data.car.modelo.name} ${data.car.modelo.year}`))

            $('#fuel-select').append($('<option>').attr({
                "value": data.car.fuel.id
            }).html(data.car.fuel.name))

            $('#transmission-select').append($('<option>').attr({
                "value": data.car.transmission.id
            }).html(data.car.transmission.name))

            imageDisplay(data.car.media, data.car.id)

            selectInputs(data.models, data.fuels, data.transmissions)

            checkBoxes(data.accessories)
            checkBoxes(data.car.accessories, 'checked')
        },
        error: function (error) {
            alert("error");
        }

    })
});

$(document).on('click', 'i.remove', function () {
    let id = $(this).attr("data-id");
    let modelID = $(this).attr("data-modelID");
    $.ajax({
        url: `/api/cars/${id}/images`,
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

$('#save').on('click', function () {

    if ($("#carForm").valid()) {

        let formData = new FormData($('#carForm')[0]);

        $('.bd-example-modal-lg *').attr({
            "disabled": "disabled",
        })

        $.ajax({
            url: '/api/cars/',
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
                $('.bd-example-modal-lg *').attr({
                    "disabled": false,
                })
                $('.bd-example-modal-lg').modal('hide');
                appendToTop(responseData);
                alertTopLeft('New car successfully added');
                resetDropZone();

            },
            error: function (responseError) {
                // errorDisplay(responseError.responseJSON.errors);
            }
        })
    }
})

$('#update').on('click', function () {

    if ($("#carForm").valid()) {

        let id = $(this).attr("data-id");
        let formData = new FormData($('#carForm')[0]);

        $('.bd-example-modal-lg *').attr({
            "disabled": "disabled",
        })

        formData.append('_method', 'PUT');
        $.ajax({
            url: `/api/cars/${id}`,
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
                appendToTop(responseData);
                $('html, body').animate({
                    scrollTop: $(".dataTables_wrapper").offset().top
                }, 'fast');
                $('.bd-example-modal-lg *').attr({
                    "disabled": false,
                })
                $('.bd-example-modal-lg').modal('hide');
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
                url: `/api/cars/${id}`,
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

$(document).on('click', 'button.view', function () {
    $('.car-images').empty();
    let id = $(this).attr('data-id');
    $.ajax({
        url: `/api/cars/${id}/view/images`,
        type: 'GET',
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-tokens"]').attr('content'),
        },
        dataType: "json",
        success: function (data) {
            if (data.media) {
                $.each(data.media, function (i, value) {
                    $('.car-images').append(
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
                $('.car-images').append(
                    $('<h6>').css({
                        "font-size": "16px",
                        "font-weight": "500",
                    }).html('Description')
                ).append(
                    $('<p>').html(data.description)
                )
            }
        },
        error: function (error) {
            alert("error");
        }

    })
});

function appendToTop(newdata) {
    let tr = $('<tr>').addClass('newRow');
    tr.append($('<td>').html(newdata.id));
    tr.append($('<td>').html(`<img class="model-image" src="${newdata.media[0]?.original_url}" alt="NONE">`));
    tr.append($('<td>').html(newdata.platenumber));
    tr.append($('<td>').html(`${newdata.modelo.name} ${newdata.modelo.year}`));
    tr.append($('<td>').html(newdata.modelo.type.name));
    tr.append($('<td>').html(newdata.modelo.manufacturer.name));
    tr.append($('<td>').html(newdata.seats));

    let accessories = newdata.accessories.map(function (value) {
        return value.name;
    }).join('<br />');
    tr.append($('<td>').html(accessories));

    let price = newdata.accessories.map(function (value) {
        return value.fee;
    });
    price.push(newdata.price_per_day);
    tr.append($('<td>').html("&#8369;" + sum(price).toFixed(2)));

    tr.append($('<td>').html(newdata.car_status));

    tr.append($('<td>').html(
        `<div class="action-buttons"><button type="button" data-toggle="modal" data-target=".bd-example-modal-lg" data-id="${newdata.id}" class="btn btn-primary edit">
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
    $('#car-table tbody').prepend(tr);
    setTimeout(function () {
        tr.removeClass('newRow', 3000);
        table.ajax.reload();
    }, 3000)
}

$('.custom-file-input').on("change", function (e) {
    $('.custom-file-label').html(e.target.files[0].name);
});

$('#importExcel').on('submit', function (e) {
    e.preventDefault()
    let formData = new FormData($(this)[0]);
    $.ajax({
        url: '/api/cars/import',
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
