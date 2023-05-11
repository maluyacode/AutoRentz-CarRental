<script>
    const confirmBookingButtons = document.querySelectorAll('.confirm-button');

    confirmBookingButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const carID = this.parentElement.querySelector('.car-id');
            const id = carID.value;
            console.log(id);
            const url = '/user/book/car/garage/' + id;
            Swal.fire({
                title: 'Confirmation',
                text: 'Are you sure you want to continue with your booking?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect user to booking route
                    window.location.href = url;
                }
            });
        });
    });
    const deleteBookingButtons = document.querySelectorAll('.delete-button');

    deleteBookingButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const carID = this.parentElement.querySelector('.car-id');
            const id = carID.value;
            console.log(id);
            const url = '/user/remove/car/garage/'+id;
            // console.log(url);
            Swal.fire({
                title: 'Confirmation',
                text: 'Are you sure you want to remove this on your garage?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect user to booking route

                    window.location.href = url
                }
            });
        });
    });
</script>
