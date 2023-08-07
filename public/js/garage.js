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
        changeYear: true
    });

    $("#return-date").datepicker({
        prevText: "click for previous months",
        nextText: "click for next months",
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true
    });

    $('#pickup-group').css({
        "display": "flex"
    })

    $('#address-group').css({
        "display": "none"
    })

    validator = $('#bookForm').validate({
        rules: {
            start_date: {
                required: true,
            },
            end_date: {
                required: true,
            },
            drivetype: {
                required: true,
            },
        },
        errorPlacement: function (error, element) {
            if (element.attr("type") == "radio") {
                // Place the error message at the bottom of the radio buttons div
                console.log("rrr");
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

$('#submit').on('click', function () {
    if ($("#bookForm").valid()) {
        let formData = new FormData($('#bookForm')[0]);
        for (var pair of formData.entries()) {
            console.log(pair[0] + ', ' + pair[1]);
        }
    }
})
