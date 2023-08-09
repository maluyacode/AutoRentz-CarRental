{{-- <script>

    let data = {!! $data !!};
    let accessories = {!! $accessories !!};
    // console.log(accessories);
    searchObjects();

    function searchObjects() {
        let searchInput = document.querySelector("#car-search-input").value;
        let results = document.querySelector(".js-car-list");
        results.innerHTML = ""; // Clear previous results

        // Filter objects based on the search input
        let carData = data.filter(function(obj) {
            return (
                obj.modelname.toLowerCase().includes(searchInput.toLowerCase()) ||
                obj.transmissionname.toLowerCase().includes(searchInput.toLowerCase()) ||
                obj.car_status.toLowerCase().includes(searchInput.toLowerCase()) ||
                obj.fuelname.toLowerCase().includes(searchInput.toLowerCase()) ||
                obj.manufacturername.toLowerCase().includes(searchInput.toLowerCase()) ||
                obj.typename.toLowerCase().includes(searchInput.toLowerCase())
            );
        });
        // console.log(carData[id].id);
        // Display the filtered objects in the results list
        // <img src="/storage/images/${image[0]}" alt="car-image" height="200px">
        if (carData.length > 0) {
            carData.forEach(function(carlist) {
                image = carlist.image_path.split('=');
                results.innerHTML += `
            <div style="width: 400px; margin-bottom: 40px">
                            <div class="shop-item box" style="font-family: serif; font-weight: 200; height:100%">
                                <div class="ribbon ${ carlist.car_status == 'taken' ? 'red' : '' }">
                                    <span>${carlist.car_status == 'taken' ? 'Taken' : 'Available' }</span>
                                </div>
                                <div style="display:flex; flex-direction:row; justify-content: space-between;">
                                    <h5></h5>
                                </div>
                                <div class="shop-item-image js-image-container">
                                    <div class="flip-box">
                                        <div class="flip-box-inner">
                                            <div class="flip-box-front">
                                                <img src="/storage/images/${image[0]}" alt="car-image" height:200px">
                                            </div>

                                            <div class="flip-box-back" style="background-image: url('/storage/images/${image[1]}')">
                                                <h4>₱ ${computeTotalPrice(carlist.id, carlist.price_per_day)} per day</h4>
                                                <p class="car-description">Seat Capacity: ${carlist.seats}</p>
                                                <p class="car-description">Transmission: ${carlist.transmissionname}</p>
                                                <p class="car-description">Fuel Type: ${carlist.fuelname}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <div class="shop-item-details">
                                <div>
                                    <h3 style="margin: 0; text-transform: capitalize">
                                         ${carlist.modelname}  ${carlist.typename}</h3>
                                    <small>${carlist.manufacturername}</small>
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
    carImages.forEach(function(image) {
        image.addEventListener('click', flip); // Attach click event listener to each image
    });

    function flip() {
        console.log(this)
        carImages.forEach(function(image) {
            image.classList.remove('image-flip');
        });
        this.classList.add('image-flip');

    }

    function computeTotalPrice(carId, price) {
        console.log(price);
        let total = 0;
        let totalCarPrice = 0;
        accessories.forEach(function(accessory) {
            if (accessory.id === carId) {
                total += Number(accessory.fee);
            }
        });
        totalCarPrice = total + Number(price);
        return totalCarPrice.toLocaleString();
    }
</script> --}}
