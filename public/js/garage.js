let validator;
$(function () {

    $.ajax({
        url: "/api/garage",
        type: "get",
        dataType: "json",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'),
        },
        success: function (data) {
            console.log(data);
        },
        error: function (error) {

        }
    })


    $("#pickup-date").datepicker({
        prevText: "click for previous months",
        nextText: "click for next months",
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        minDate: new Date(),
    });

    $("#return-date").datepicker({
        prevText: "click for previous months",
        nextText: "click for next months",
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        minDate: new Date(),
    });

    $('#pickup-group').css({
        "display": "flex"
    })

    $('#address-group').css({
        "display": "none"
    })

    $.validator.addMethod("date_limit", function (value, element) {
        const selectedDate = new Date(value);
        selectedDate.setHours(0, 0, 0, 0);

        const currentDate = new Date();
        currentDate.setHours(0, 0, 0, 0);

        return selectedDate >= currentDate;
    }, "Please select a valid date");

    $.validator.addMethod("return_limit", function (value, element) {
        const returndate = new Date(value);
        returndate.setHours(0, 0, 0, 0);

        const pickdate = new Date($('#pickup-date').val());
        pickdate.setHours(0, 0, 0, 0);

        return returndate > pickdate;
    }, "Please select a valid date");

    validator = $('#bookForm').validate({
        rules: {
            start_date: {
                required: true,
                date_limit: true,
            },
            end_date: {
                required: true,
                return_limit: true,
            },
            drivetype: {
                required: true,
            },
            pick_id: {
                required: true,
            },
            return_id: {
                required: true,
            }
        },
        errorPlacement: function (error, element) {
            if (element.attr("type") == "radio") {
                error.addClass('error-messages').attr({
                    "display": "block",
                });
                $(error).insertAfter('.radio-drive-type');
            } else {
                error.addClass('error-messages');
                error.appendTo(element.parent());
            }
        }
    })
})

$("#pickup-date").on('change', function () {
    $(this).siblings('.error-messages').remove();
})

$("#return-date").on('change', function () {
    $(this).siblings('.error-messages').remove();
})

$('#pickup-radio').on('change', function () {
    // console.log(validator.settings.rules);
    if ($(this).is(':checked')) {
        $('#pickup-group').css({
            "display": "flex"
        })
        $('#address-group').css({
            "display": "none"
        })
        validator.settings.rules.pick_id = {
            required: true,
        }
        validator.settings.rules.return_id = {
            required: true,
        }
        delete validator.settings.rules.address
    } else {
        $('#pickup-group').css({
            "display": "none"
        })
    }
    console.log(validator.settings.rules);
})

$('#delivery-radio').on('change', function () {
    if ($(this).is(':checked')) {
        $('#pickup-group').css({
            "display": "none"
        })
        $('#address-group').css({
            "display": "block"
        })
        validator.settings.rules.address = {
            required: true,
        }
        delete validator.settings.rules.pick_id
        delete validator.settings.rules.return_id
    }
    console.log(validator.settings.rules);
})


let buttonBook;


$(document).on('click', '.confirm-button', function () {
    buttonBook = $(this);

    let id = $(this).attr('data-id');

    $('#submit').attr({
        "data-id": id
    });

    $('#car_id').attr({
        value: id,
    });
})

$('#submit').on('click', function () {


    if ($("#bookForm").valid()) {

        $('#closeModalForm').trigger('click');


        Swal.fire({
            // title: '',
            // html: '<b></b>',
            timer: 10000,
            allowOutsideClick: false,
            timerProgressBar: true,
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

        let formData = new FormData($('#bookForm')[0]);

        $.ajax({
            url: `/user/book/car/garage`,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "json",
            success: function (responseData) {
                buttonBook.closest('.cars-row').fadeOut(1000, function () {
                    $(this).remove();
                })

                Swal.close();

                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 5000,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                })

                Toast.fire({
                    icon: 'success',
                    title: 'Reserved successfully, kindly check your bookings tab or email for updates.'
                }).then((result) => {
                    if (result.dismiss === Swal.DismissReason.timer) {
                        // After the toast is displayed, reload the page
                        location.reload();
                    }
                });

            },
            error: function (responseError) {
                console.log(responseError);
            }
        })
    }
})

$(document).on('click', 'button.delete-button', function () {
    let id = $(this).attr('data-id');
    Swal.fire({
        title: 'Confirmation',
        text: 'Are you sure you want to remove this from your garage?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'No',
        customClass: {
            title: 'swal-title',
            cancelButton: 'swal-button',
            confirmButton: 'swal-button'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `/user/remove/car/garage/${id}`;
        }
    });


})

// $('#save').on('click', function () {

//     if ($("#bookForm").valid()) {



//         let formData = new FormData($('#bookForm')[0]);
//         for (var pair of formData.entries()) {
//             console.log(pair[0] + ', ' + pair[1]);
//         }

//         formData.append('_method', 'PUT');
//         $.ajax({
//             url: `/save/bookinfo/${id}`,
//             type: 'POST',
//             data: formData,
//             contentType: false,
//             processData: false,
//             headers: {
//                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//             },
//             dataType: "json",
//             success: function (responseData) {



//             },
//             error: function (responseError) {
//                 // errorDisplay(responseError.responseJSON.errors);
//             }
//         })
//     }
// })

