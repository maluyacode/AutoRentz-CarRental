
Chart.defaults.backgroundColor = '#9BD0F5';
Chart.defaults.font.size = 16;
Chart.defaults.color = '#000';

let originalData;

let incomeChart;
let rentCarChart;
let regiteredChart;

let daysMonths = "months";
let incomeChartType = 'line';

let DAY_MONTH_WEKK;


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

    $('.date-range-customer').datepicker({
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
            // console.log(data.rentCountPerCar);
            setOrignalData(data);

            processByMonths(data.monthlyIncome);
            rentCount(data.rentCountPerCar);
            monthlyRegistered(data.registered)
            console.log(data.registered)

        },
        error: function (error) {
            alert("error");
        }
    })
}


// ---------- DATE RANGE INCOME ----------------

$(document).on('change', '.date-range', function () { // date range for income

    let start = new Date($('#start-date').val());
    let end = new Date($('#end-date').val());
    let filteredData = {};

    // start.setHours(0, 0, 0, 0);
    end.setHours(23, 59, 59, 999);
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
        $('.date-range').val('')
        let data = getOriginalData();
        processByMonths(data.monthlyIncome);
    } else if (daysMonths == "days") {
        $('.date-range').val('')
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
// ---------- DATE RANGE INCOME ----------------


// --------------RENT COUNT OR INCOME PER CARS -------------

$(document).on('change', '.car-radio', function () {
    let value = $(this).val();
    let data;
    console.log(value);

    if (value == 'all') {
        data = getOriginalData();
        let all = count_and_rent(data.rentCountPerCar);
        console.log(all.countCarRent);
        console.log(all.totalPriceCarRent);


        // rentCarChart.config.data.datasets = [];


        rentCarChart.config.data.labels = Object.keys(all.countCarRent)
        rentCarChart.update();
        rentCarChart.config.data.datasets = [
            {
                label: 'Rent Count',
                data: Object.values(all.countCarRent),
                borderWidth: 1,
                backgroundColor: colors(),
                borderRadius: 10,
                borderColor: '#7EAA92',
                fill: true,
                tension: 0.3,
            },
            {
                label: 'Rent Total Income',
                data: Object.values(all.totalPriceCarRent),
                borderWidth: 1,
                backgroundColor: colors(),
                borderRadius: 10,
                borderColor: '#7EAA92',
                fill: true,
                tension: 0.3,
            }
        ];

        rentCarChart.update();
    } else if (value == 'model') {

        data = getOriginalData();

        let carModels = groupByModels(data.rentCountPerCar);

        rentCarChart.config.data.labels = Object.keys(carModels);
        rentCarChart.config.data.datasets = [
            {
                label: 'Cars Model ',
                data: Object.values(carModels),
                borderWidth: 1,
                backgroundColor: colors(),
                borderRadius: 10,
                borderColor: '#7EAA92',
                fill: true,
                tension: 0.3,
            }
        ]
        rentCarChart.update();

    } else if (value == 'manufacturer') {

        data = getOriginalData();
        let carManufacturers = groupByManufacturers(data.rentCountPerCar);

        rentCarChart.config.data.labels = Object.keys(carManufacturers);
        rentCarChart.config.data.datasets = [
            {
                label: 'Manufacturers of cars',
                data: Object.values(carManufacturers),
                borderWidth: 1,
                backgroundColor: colors(),
                borderRadius: 10,
                borderColor: '#7EAA92',
                fill: true,
                tension: 0.3,
            }
        ]
        rentCarChart.update();


    } else if (value == 'type') {

        data = getOriginalData();

        let carTypes = groupByTypes(data.rentCountPerCar);

        rentCarChart.config.data.labels = Object.keys(carTypes);
        rentCarChart.config.data.datasets = [
            {
                label: 'Type of Cars',
                data: Object.values(carTypes),
                borderWidth: 1,
                backgroundColor: colors(),
                borderRadius: 10,
                borderColor: '#7EAA92',
                fill: true,
                tension: 0.3,
            }
        ]
        rentCarChart.update();
    }
})

// for rent info
function count_and_rent(carWithBookings) {

    let countCarRent = [];
    let totalPriceCarRent = [];

    $.each(carWithBookings, function (i, value) {

        let countRent = computeCount(value);
        let priceRent = computeRentPrice(value);

        countCarRent[value.platenumber] = countRent;
        totalPriceCarRent[value.platenumber] = priceRent;
    })

    return { countCarRent, totalPriceCarRent }
}

//compute total count rent per car
function computeCount(car) {

    let count_of_rent = Object.keys(car.bookings).length
    return count_of_rent;

}

// compute total income in speific car
function computeRentPrice(car) {

    let accessoriesFee = 0;
    let totalRent = 0;
    let carTotalRentIncome = 0

    $.each(car.accessories, function (i, value) {
        accessoriesFee += Number(value.fee);
    })
    totalRent = Number(accessoriesFee) + Number(car.price_per_day)

    $.each(car.bookings, function (i, value) {
        carTotalRentIncome += (totalRent * getBookingLengthDays(value))
    })
    return carTotalRentIncome;
}

// get days between start data and end date of booking
function getBookingLengthDays(booking) {

    var startDate = new Date(booking.start_date);
    var endDate = new Date(booking.end_date);

    var timeDiff = endDate.getTime() - startDate.getTime();
    const daysDiff = timeDiff / (1000 * 60 * 60 * 24);

    return daysDiff;
}
// for rent info

$(document).on('change', '#chart-types-car', function () {
    let value = $(this).val();
    if (value == 'bar') {
        rentCarChart.config.type = 'bar'
        rentCarChart.update()
    } else if (value == 'doughnut') {
        rentCarChart.config.type = 'doughnut'
        rentCarChart.update()
    } else if (value == 'pie') {
        rentCarChart.config.type = 'pie'
        rentCarChart.update()
    }
})

// charts for rent_count chart
function rentCount(data) {

    const ctx = document.getElementById('rentCountPerMonthChart');

    rentCarChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ["ASDSA", "ASDSAD", "ASDSAD"],
            datasets: [
                {
                    label: 'Dataset 1',
                    data: [34, 32, 32],
                },
                {
                    label: 'Dataset 2',
                    data: [34, 32, 32],
                }
            ],

        },
        options: {
            // indexAxis: 'y',
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}


function groupByModels(cars) {

    let models = [];
    $.each(cars, function (i, value) {
        const modelName = value.modelo.name;

        if (models[modelName] === undefined) {
            models[modelName] = 1;
        } else {
            models[modelName]++;
        }
    });

    return models;
}

function groupByManufacturers(cars) {

    let manufacturers = [];
    $.each(cars, function (i, value) {
        const manufacturerName = value.modelo.manufacturer.name;

        if (manufacturers[manufacturerName] === undefined) {
            manufacturers[manufacturerName] = 1;
        } else {
            manufacturers[manufacturerName]++;
        }
    });

    return manufacturers;
}

function groupByTypes(cars) {

    let types = [];
    $.each(cars, function (i, value) {
        const typeName = value.modelo.type.name;

        if (types[typeName] === undefined) {
            types[typeName] = 1;
        } else {
            types[typeName]++;
        }
    });

    return types;
}

// --------------RENT COUNT OR INCOME PER CARS -------------



// --------------- EWAN --------------------

$(document).on('change', '#chart-types-customer', function () {
    let value = $(this).val();
    if (value == 'bar') {
        regiteredChart.config.type = 'bar'
        regiteredChart.update()
    } else if (value == 'doughnut') {
        regiteredChart.config.type = 'doughnut'
        regiteredChart.update()
    } else if (value == 'pie') {
        regiteredChart.config.type = 'pie'
        regiteredChart.update()
    } else if (value == 'line') {
        regiteredChart.config.type = 'line'
        regiteredChart.update()
    }
})

$(document).on('change', '.date-range-customer', function () {
    let start = new Date($('#date-start-register').val())
    let end = new Date($('#date-end-register').val())
    end.setHours(23, 59, 59, 999);
    // if (start && end) {
    //     return "";
    // }

    let data = getOriginalData();

    let customersData = []
    $.each(data.registered, function (i, value) {
        let date = new Date(value.created_at)
        // console.log(i);
        if (date >= start && date <= end) {

            customersData.push(value);
        }
    })

    if (DAY_MONTH_WEKK == "months-registered") {

        groupByMonth(customersData)

    } else if (DAY_MONTH_WEKK == "weeks-registered") {

        groupByWeek(customersData)

    } else if (DAY_MONTH_WEKK == "days-registered") {

        groupByDay(customersData, start, end)

    }
})

$(document).on('change', '.customer-radio', function () { // months or date in x-axis of chart
    DAY_MONTH_WEKK = $(this).val();

    let data = getOriginalData()


    if (DAY_MONTH_WEKK == "months-registered") {
        $('.date-range-customer').val('')
        groupByMonth(data.registered)

    } else if (DAY_MONTH_WEKK == "weeks-registered") {
        $('.date-range-customer').val('')
        groupByWeek(data.registered)

    } else if (DAY_MONTH_WEKK == "days-registered") {
        $('.date-range-customer').val('')
        groupByDay(data.registered, '2023-01-01', '2023-12-31')

    }
})

function groupByMonth(customers) {
    const monthsData = {};

    // Generate data for January to December
    for (let month = 1; month <= 12; month++) {
        let date = new Date();
        date.setMonth(month - 1);
        monthsData[date.toLocaleString('en-US', { month: 'long' })] = 0;
    }

    $.each(customers, function (i, value) {
        let date = new Date(value.created_at);
        let monthName = date.toLocaleString('en-US', { month: 'long' })

        if (monthsData.hasOwnProperty(monthName)) {
            monthsData[monthName]++;
        }

    })

    regiteredChart.config.data.labels = Object.keys(monthsData);
    regiteredChart.config.data.datasets = [
        {
            label: 'Monthly Registration',
            data: Object.values(monthsData),
            borderWidth: 1,
            backgroundColor: colors(),
            borderRadius: 10,
            borderColor: '#7EAA92',
            fill: true,
            tension: 0.3,
        }
    ]
    regiteredChart.update();
}

function groupByWeek(customers) {

    const weekday = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    const weekDayData = {};

    $.each(weekday, function (i, value) {
        weekDayData[value] = 0;
    })

    $.each(customers, function (i, value) {
        let date = new Date(value.created_at);
        let dayName = date.toLocaleString('en-US', { weekday: 'long' });

        if (weekDayData.hasOwnProperty(dayName)) {
            weekDayData[dayName]++;
        }

    })
    console.log(weekDayData);
    regiteredChart.config.data.labels = Object.keys(weekDayData);
    regiteredChart.config.data.datasets = [
        {
            label: 'Week Registration',
            data: Object.values(weekDayData),
            borderWidth: 1,
            backgroundColor: colors(),
            borderRadius: 10,
            borderColor: '#7EAA92',
            fill: true,
            tension: 0.3,
        }
    ]
    regiteredChart.update();

}


function groupByDay(customers, start, end) {

    let startDate = new Date(start);
    let endDate = new Date(end);

    startDate.setHours(23, 59, 59, 999);
    endDate.setHours(23, 59, 59, 999);


    let yearDates = [];

    let currentDate = startDate;
    while (currentDate <= endDate) {
        const formattedDate = currentDate.toISOString().split('T')[0];
        yearDates[formattedDate] = 0;
        currentDate.setDate(currentDate.getDate() + 1);
    }



    $.each(customers, function (i, value) {
        let date = new Date(value.created_at)
        let format = date.toISOString().split('T')[0];
        if (yearDates.hasOwnProperty(format)) {
            yearDates[format]++;
        }
    })
    regiteredChart.config.data.labels = Object.keys(yearDates);
    regiteredChart.config.data.datasets = [
        {
            label: 'Dates Registration',
            data: Object.values(yearDates),
            borderWidth: 1,
            backgroundColor: colors(),
            borderRadius: 10,
            borderColor: '#7EAA92',
            fill: true,
            tension: 0.3,
        }
    ]
    regiteredChart.update();
}


function monthlyRegistered(data) {
    const ctx = document.getElementById('customerRegisteredChart');

    regiteredChart = new Chart(ctx, {
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



// --------------- EWAN --------------------




function colors() {
    return [
        '#7158e2',
        '#3ae374',
        '#ff3838',
        "#FF851B",
        "#7FDBFF",
        "#B10DC9",
        "#FFDC00",
        "#001f3f",
        "#39CCCC",
        "#01FF70",
        "#85144b",
        "#F012BE",
        "#3D9970",
        "#111111",
        "#AAAAAA",
    ]
}
