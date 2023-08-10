let originalData;
let incomeCanvas;

$('.carousel').carousel({
    interval: false,
});
$(function () {
    getData();
})


Chart.defaults.backgroundColor = '#9BD0F5';
Chart.defaults.font.size = 16;
Chart.defaults.color = '#000';

function setOrignalData(data) {
    originalData = data
}

function getOriginalData() {
    return originalData;
}

function getData() {
    $.ajax({
        url: `/api/data/charts`,
        type: 'GET',
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-tokens"]').attr('content'),
        },
        dataType: "json",
        success: function (data) {

            setOrignalData(data);

            monthlyIncome(data.monthlyIncome);
            rentCount(data.rentCountPerCar);
            monthlyRegistered(data.registeredPerMonth)


        },
        error: function (error) {
            alert("error");
        }
    })
}

$(document).on('change', '.income-radio', function () {
    let value = $(this).val();


    if (value == "months") {
        let data = getOriginalData();
        processByMonths(data.monthlyIncome);
    }
})


function processByMonths(data) {
    incomeCanvas.destroy();
    let keyValue = {};

    $.each(data, function (dateKey, income) {
        var date = new Date(dateKey);
        var month = date.toLocaleString('default', { month: 'long' }); // Get month name

        if (!keyValue[month]) {
            keyValue[month] = 0;
        }

        keyValue[month] += income;
    });

    monthlyIncome(keyValue);
}


function monthlyIncome(data) {
    let chart;

    incomeCanvas = document.getElementById('monthlyIncomeChart');

    chart = new Chart(incomeCanvas, {
        type: 'line',
        data: {
            labels: Object.keys(data),
            datasets: [{
                label: '2023 INCOME',
                data: Object.values(data),
                borderWidth: 2,
                borderColor: '#7EAA92',
                fill: true,
                tension: 0.3,
                backgroundColor: '#9ED2BE',
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}




function rentCount(data) {

    const ctx = document.getElementById('rentCountPerMonthChart');

    chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: Object.keys(data),
            datasets: [{
                label: 'NO. OF RENT PER CAR',
                data: Object.values(data),
                borderWidth: 1,
                backgroundColor: colors(),
                borderRadius: 10,
            }]
        },
        options: {
            indexAxis: 'y',
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}




function monthlyRegistered(data) {
    const ctx = document.getElementById('customerRegisteredChart');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: Object.keys(data),
            datasets: [{
                label: 'Registered Customer',
                data: Object.values(data),
                borderWidth: 1,
                backgroundColor: colors(),
                borderRadius: 5,
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}




function colors() {
    return [
        '#7158e247',
        '#3ae37447',
        '#ff383847',
        "#FF851B47",
        "#7FDBFF47",
        "#B10DC947",
        "#FFDC0047",
        "#001f3f47",
        "#39CCCC47",
        "#01FF7047",
        "#85144b47",
        "#F012BE47",
        "#3D997047",
        "#11111147",
        "#AAAAAA47",
    ]
}
