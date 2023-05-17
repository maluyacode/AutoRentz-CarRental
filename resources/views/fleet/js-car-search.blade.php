<script>
    let data = {!! $data !!};
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
        if (carData) {
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
                                <div class="shop-item-image">
                                    <img src="/storage/images/${image[0]}" alt="car-image" height="200px">
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
        }
    }
</script>
