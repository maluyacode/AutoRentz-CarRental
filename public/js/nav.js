const links = document.querySelectorAll('.nav-link-color');

links.forEach((link) => {
    const linkId = link.getAttribute('id');
    if (localStorage.getItem(linkId)) {
        link.classList.add('clicked');
    }
});

links.forEach((link) => {
    const linkId = link.getAttribute('id');
    localStorage.removeItem(linkId);
    link.addEventListener('click', function () {
        link.classList.add('clicked');
        const linkId = link.getAttribute('id');
        localStorage.setItem(linkId, true);
    });
});


$(function () {
    let srcData = [];
    $.ajax({
        url: `/api/search/any`,
        type: 'GET',
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-tokens"]').attr('content'),
        },
        dataType: "json",
        success: function (data) {
            $.each(data.cars, function (i, value) {
                srcData.push(`${value.platenumber} ${value.modelo.name}`);
            })
            $.each(data.drivers, function (i, value) {
                srcData.push(`${value.fname} ${value.lname}`);
            })
            $.each(data.locations, function (i, value) {
                srcData.push(value.street);
            })
            // console.log(srcData);
        },
        error: function (error) {

        }

    })
    $("#tags").autocomplete({
        source: srcData
    });
});
