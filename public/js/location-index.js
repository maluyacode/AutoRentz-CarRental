let table = $('#location-table').DataTable({
    ajax: {
        url: '/api/location',
        dataSrc: ''
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
    columns: [{
        data: 'id'
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
                </button></div>`;
        }
    }
    ]
});

let save = $('button#save');
let update = $('button#update');
let formInModal = $('#location-form');
let ourModal = $('#locationModalCenter');

$(function () {
    $('.buttons-create').attr({
        "data-toggle": "modal",
        "data-target": "#locationModalCenter",
    });
})

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

function alertTopLeft(message){

}

function createButton() {
    saveButton();
}


$(document).on('click', '.edit', function () {
    updatebutton();
})

save.on('click', function () {

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
            $('.buttons-reload').trigger('click');

            formInModal.trigger("reset");

            resetDropZone()
            alertTopLeft("New driver successfully added!")
        },
        error: function (responseError) {
            errorDisplay(responseError.responseJSON.errors);
        }
    })

})
