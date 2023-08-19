$(function () {
    dataTableFill('/api/bookings');
    $('.buttons-import').attr({
        "data-toggle": "modal",
        "data-target": "#importModal"
    })
})
let table;
function dataTableFill(apiLocation) {
    table = $('#bookings-table').DataTable({
        ajax: {
            url: apiLocation,
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
                text: '<i class="fas fa-file-import"></i> Import',
                action: importButton,
                className: "buttons-import",
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
                data: 'customer.name'
            },
            {
                data: null,
                render: function (data) {
                    return `${data.car.modelo.name} ${data.car.modelo.year}`;
                }
            },
            {
                data: 'start_date'
            },
            {
                data: 'end_date'
            },
            {
                data: null,
                render: function (data) {
                    let price = data.car.accessories.map(function (value) {
                        return value.fee;
                    });

                    let pickDate = new Date(data.start_date);
                    let returnDate = new Date(data.end_date);

                    returnDate.setHours(0, 0, 0, 0);
                    pickDate.setHours(0, 0, 0, 0);

                    let timeDifference = returnDate - pickDate;
                    let days = Math.floor(timeDifference / (1000 * 60 * 60 * 24));

                    price.push(data.car.price_per_day);

                    let totalPrice = sum(price) * (days + 1);

                    return totalPrice.toLocaleString('en-PH', { style: 'currency', currency: 'PHP' });
                }
            },
            // {
            //     data: null,
            //     render: function (data) {
            //         return data.address ? "Delivery" : "Pickup"
            //     }
            // },
            // {
            //     data: null,
            //     render: function (data) {
            //         if (data.address) {
            //             return data.address
            //         } else {
            //             return `<b>Pick: </b>${data.picklocation.street}<br>
            //             <b>Return: </b>${data.returnlocation.street}`
            //         }
            //     }
            // },
            // {
            //     data: 'driver_id',
            // },
            {
                data: null,
                render: function (data) {
                    return `<span class=${data.status}>${data.status}</span>`;
                },
                class: 'status',
            },
            {
                data: null,
                render: function (data) {
                    if (apiLocation == '/api/bookings') {
                        return editDeleteButtons(data);
                    } else if (apiLocation == '/api/pendings') {
                        return confirmCancelButtons(data);
                    } else if (apiLocation == '/api/confirms') {
                        return finishedButton(data)
                    } else {
                        return deleteButton(data);
                    }

                }
            }
        ]
    });
}

function deleteButton(data) {
    return `<div class="action-buttons"><button type="button" data-toggle="modal" data-target=".bd-example-modal-lg" data-id="${data.id}" class="btn btn-outline-danger btn-sm delete-finished">
                Delete
            </button>
            </div>`;
}

function finishedButton(data) {
    return `<div class="action-buttons"> <button type="button" data-id="${data.id}" class="btn btn-outline-success btn-sm btn-delete finished">
                Finished
            </button>
            </div>`;
}

function editDeleteButtons(data) {
    return `<div class="action-buttons"><button type="button" data-toggle="modal" data-target=".bd-example-modal-lg" data-id="${data.id}" class="btn btn-outline-primary edit">
    <i class="bi bi-pencil-square"></i>
        </button>
        <button type="button" data-id="${data.id}" class="btn btn-outline-danger btn-delete delete-finished">
            <i class="bi bi-trash3"></i>
        </button>
    </div>`;
}

function confirmCancelButtons(data) {
    return `<div class="action-buttons"><button type="button" data-toggle="modal" data-target=".bd-example-modal-lg" data-driver="${data.driver_id}" data-id="${data.id}" class="btn btn-outline-success btn-sm confirm">
            Confirm
        </button>
        <button type="button" data-id="${data.id}" class="btn btn-outline-danger btn-sm btn-delete cancel">
            Cancel
        </button>
    </div>`;
}

$(document).on('change', '.booking-status', function () {
    table.destroy();
    let data = $(this).val();
    if (data == 'all') {
        dataTableFill('/api/bookings');
    } else if (data == 'pendings') {
        dataTableFill('/api/pendings');
    } else if (data == 'confirmed') {
        dataTableFill('/api/confirms');
    } else if (data == 'finished') {
        dataTableFill('/api/finished');
    } else if (data == 'cancelled') {
        dataTableFill('/api/cancelled');
    }
})

function importButton() {
    console.log("import");
}

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

function createButton() {
    window.location = "/admin/bookings/create";
}

$(document).on('click', '.edit', function (e) {
    let id = $(this).attr('data-id');
    window.location = `/admin/bookings/${id}/edit`;
})


const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-success',
        cancelButton: 'btn btn-danger',
        text: 'font-size-18'
    },
    buttonsStyling: false
})

$(document).on('click', '.delete', function (e) {
    let id = $(this).attr('data-id');

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
            window.location = `/admin/bookings/${id}/delete`;
        } else if (
            /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
        ) {
            // swalWithBootstrapButtons.fire(
            //     'Cancelled',
            //     'Your imaginary file is safe :)',
            //     'error'
            // )
        }
    })
})

$(document).on('click', '.confirm', function () {

    let id = $(this).attr('data-id');
    let driver = $(this).attr('data-driver');


    if (driver == 1) {

        $.ajax({
            url: `/api/drivers`,
            type: "GET",
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (responseData) {

                const { driver_id: fruit } = Swal.fire({
                    title: 'Select driver for this car',
                    input: 'select',
                    inputOptions: generateInputOptions(responseData),
                    inputPlaceholder: 'Select a Driver',
                    showCancelButton: true,
                    confirmButtonText: 'Confirm',
                    inputValidator: (driver_id) => {
                        return new Promise((resolve) => {
                            if (driver_id) {
                                confirmBooking(driver_id, id)
                                resolve()
                            } else {
                                resolve('You need to select driver :)')
                            }
                        })
                    }
                })

            },
            error: function (responseError) {
                alert("error");
            },
        })
    } else {
        Swal.fire({
            title: 'Confirm reservation',
            text: "You won't be able to revert this!",
            // icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Confirm'
        }).then((result) => {
            if (result.isConfirmed) {
                confirmBooking(null, id)
            }
        })
    }
})


function confirmBooking(driver_id, booking_id) {

    loading()


    $.ajax({
        url: `/admin/confirm/${driver_id}`,
        type: 'get',
        data: {
            booking_id: booking_id
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: "json",
        success: function (responseData) {
            Swal.close();
            table.ajax.reload();
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                // timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            })

            Toast.fire({
                icon: 'success',
                title: 'Book confirmed!'
            })

        },
        error: function (responseError) {
            alert("error")
        }
    })
}

function generateInputOptions(data) {
    const inputOptions = {};

    $.each(data, function (i, value) {

        if (value.driver_status == 'available') {
            inputOptions[value.id] = `${value.fname} ${value.lname}`;
        }

    });

    return inputOptions;
}

$(document).on('click', '.cancel', function () {
    let id = $(this).attr('data-id');

    Swal.fire({
        title: 'Cancel reservation',
        text: "Are you sure?",
        // icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'No',
        confirmButtonText: 'Yes'
    }).then((result) => {
        if (result.isConfirmed) {

            loading();

            $.ajax({
                url: `/admin/cancel/${id}`,
                type: 'get',
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (responseData) {
                    Swal.close();
                    table.ajax.reload();
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        // timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    })

                    Toast.fire({
                        icon: 'success',
                        title: 'Book cancelled!'
                    })
                },
                error: function () {
                    alert('error')
                }
            })
        }
    })
})

$(document).on('click', '.finished', function () {
    let id = $(this).attr('data-id');

    Swal.fire({
        title: 'Finished reservation',
        text: "You won't be able to revert this!",
        // icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Finished'
    }).then((result) => {
        if (result.isConfirmed) {

            loading();

            $.ajax({
                url: `/admin/bookings/${id}/finished`,
                type: 'get',
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (responseData) {
                    Swal.close();
                    table.ajax.reload();
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        // timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    })

                    Toast.fire({
                        icon: 'success',
                        title: 'Book finished!'
                    })
                },
                error: function () {
                    alert('error')
                }
            })
        }
    })
})

function loading() {
    Swal.fire({
        // title: '',
        // html: '<b></b>',
        // timer: 10000,
        allowOutsideClick: false,
        // timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading()
            // const b = Swal.getHtmlContainer().querySelector('b')
            timerInterval = setInterval(() => {
                Swal.getTimerLeft()
            }, 100)
        },
        willClose: () => {
            clearInterval(timerInterval)
        }
    }).then((result) => {

    })
}

$(document).on('click', '.delete-finished', function () {
    let id = $(this).attr('data-id');
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

            loading();

            $.ajax({
                url: `/admin/bookings/${id}/delete`,
                type: 'get',
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (responseData) {
                    Swal.close();
                    table.ajax.reload();
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        // timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    })

                    Toast.fire({
                        icon: 'success',
                        title: 'Book record deleted!'
                    })
                },
                error: function () {
                    alert('error')
                }
            })

        } else if (result.dismiss === Swal.DismissReason.cancel) {

        }
    })

})
