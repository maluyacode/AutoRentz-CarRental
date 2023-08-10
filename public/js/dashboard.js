
Chart.defaults.backgroundColor = '#9BD0F5';
Chart.defaults.font.size = 16;
Chart.defaults.color = '#000';

let originalData;
let incomeChart;
let daysMonths = "months";

let incomeChartType = 'line';

$('.carousel').carousel({
    interval: false,
});


$(function () {
    getData();

    $('#start-date').datepicker({
        changeYear: true,
        changeMonth: true,
    });
    $('#end-date').datepicker({
        changeYear: true,
        changeMonth: true,
    });
})


function setOrignalData(data) { // setting fixed data
    originalData = data
}

function getOriginalData() { // where you can access the fixed data for charts
    return originalData;
}

function getData() { // request data
    $.ajax({
        url: `/api/data/charts`,
        type: 'GET',
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-tokens"]').attr('content'),
        },
        dataType: "json",
        success: function (data) {

            setOrignalData(data);

            processByMonths(data.monthlyIncome);
            rentCount(data.rentCountPerCar);
            monthlyRegistered(data.registeredPerMonth)


        },
        error: function (error) {
            alert("error");
        }
    })
}


// DATE RANGE INCOME
$(document).on('change', '.date-range', function () { // date range for income

    let start = new Date($('#start-date').val());
    let end = new Date($('#end-date').val());
    let filteredData = {};

    // start.setHours(0, 0, 0, 0);
    // end.setHours(0, 0, 0, 0);

    data = getOriginalData();

    $.each(data.monthlyIncome, function (dateKey, income) {
        let currentDate = new Date(dateKey);

        if (currentDate >= start && currentDate <= end) {
            filteredData[dateKey] = income;
        }
    });

    if (daysMonths == "months") {

        processByMonths(filteredData)

    } else if (daysMonths == "days") {

        processByDays(filteredData)

    }
});

$(document).on('change', '.income-radio', function () { // months or date in x-axis of chart
    daysMonths = $(this).val();


    if (daysMonths == "months") {
        let data = getOriginalData();
        processByMonths(data.monthlyIncome);
    } else if (daysMonths == "days") {
        let data = getOriginalData();
        processByDays(data.monthlyIncome);
    }
})

function processByDays(data) { // displays the x-axis in days
    monthlyIncome(data);
}


function processByMonths(data) { // displays the x-axis in months

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

$('#chart-types').on('change', function () { // different types of charts
    incomeChartType = $(this).val();
    if (incomeChartType == 'bar') {
        incomeChart.config.type = 'bar'
        incomeChart.update();
    } else if (incomeChartType == 'line') {
        incomeChart.config.type = 'line'
        incomeChart.update();
    }
})


function monthlyIncome(data) { // initial config for charts

    let ctx = document.getElementById('monthlyIncomeChart');

    if (incomeChart) {
        incomeChart.destroy()
    }

    incomeChart = new Chart(ctx, {
        type: incomeChartType,
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
// DATE RANGE INCOME



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
