$(function () {
    $.ajax({
        url: '/api/car/listing',
        type: 'GET',
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-tokens"]').attr('content'),
        },
        dataType: "json",
        success: function (data) {
            console.log(data);
            displayCar(data)
        },
        error: function () {

        }
    })
})

function displayCar(carData) {

    let results = document.querySelector(".js-car-list");
    results.innerHTML = "";

    if (carData.length > 0) {
        carData.forEach(function (carlist) {
            results.innerHTML += `
                <div style="width: 400px; margin-bottom: 40px">
                        <div class="shop-item box" style="font-family: serif; font-weight: 200; height:100%">
                            <div class="ribbon ${carlist.car_status == 'taken' ? 'red' : ''}">
                                <span>${carlist.car_status == 'taken' ? 'Taken' : 'Available'}</span>
                            </div>
                            <div style="display:flex; flex-direction:row; justify-content: space-between;">
                                <h5></h5>
                            </div>
                            <div class="shop-item-image js-image-container">
                                <div class="flip-box">
                                    <div class="flip-box-inner">
                                        <div class="flip-box-front">
                                            <img class="images-car" src="${carlist.media[0]?.original_url || '/storage/images/Logo.png'}" alt="car-image">
                                        </div>

                                        <div class="flip-box-back" style="background-image: url('/storage/images/Logo.png')">
                                            <h4>₱ ${computeTotalPrice(carlist.price_per_day, carlist.accessories)} per day</h4>
                                            <p class="car-description">Seat Capacity: ${carlist.seats}</p>
                                            <p class="car-description">Transmission: ${carlist.transmission.name}</p>
                                            <p class="car-description">Fuel Type: ${carlist.fuel.name}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <div class="shop-item-details">
                            <div>
                                <h3 style="margin: 0; text-transform: capitalize">
                                        ${carlist.modelo.name}  ${carlist.modelo.type.name}</h3>
                                <small>${carlist.modelo.manufacturer.name}</small>
                            </div>
                            <a href="http://127.0.0.1:8000/user/addtogarage/${carlist.id}" class="add-to-garage"
                                style="font-size: 12px">Add to garage</a>
                            <a href="http://127.0.0.1:8000/fleet/car/details/${carlist.id}" class="car-details"
                                style="font-size: 12px">View Details</a>
                        </div>
                    </div>
                </div>
        `;
        });
    } else {
        results.innerHTML = '<div class="no-results" style="height: 500px; width: 100%"><h1>No results</h1<div/>';
        console.log('NONE');
    }
}

let carImages = document.querySelectorAll('.js-image-container img');
// console.log(carImages);
carImages.forEach(function (image) {
    image.addEventListener('click', flip); // Attach click event listener to each image
});

function flip() {
    console.log(this)
    carImages.forEach(function (image) {
        image.classList.remove('image-flip');
    });
    this.classList.add('image-flip');

}

function computeTotalPrice(price, accessories) {
    console.log(price);

    let total = 0;
    let totalCarPrice = 0;

    accessories.forEach(function (accessory) {
        total += Number(accessory.fee);
    });
    totalCarPrice = total + Number(price);
    return totalCarPrice.toLocaleString();
}
