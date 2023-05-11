<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <script src="jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.4.24/sweetalert2.all.js"></script>

    <style>
        body {
            font-family: "Open Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", Helvetica, Arial, sans-serif;
        }
    </style>
</head>

<body>
    @if (Session::get('update'))
        <p id=update style="display: none;">{!! Session::get('update') !!}</p>
        <script>
            var update = document.getElementById("update").textContent;
            Swal.fire(update)
        </script>
    @endif

    @if (Session::get('created'))
        <p id=created style="display: none;">{!! Session::get('created') !!}</p>
        <script>
            var created = document.getElementById("created").textContent;
            Swal.fire(created)
        </script>
    @endif

    @if (Session::get('deleted'))
        <p id=deleted style="display: none;">{!! Session::get('deleted') !!}</p>
        <script>
            var deleted = document.getElementById("deleted").textContent;
            Swal.fire(deleted)
        </script>
    @endif
    @if (Session::get('inserted'))
        <p id=inserted style="display: none;">{!! Session::get('inserted') !!}</p>
        <script>
            var inserted = document.getElementById("inserted").textContent;
            Swal.fire(inserted)
        </script>
    @endif
    @if (Session::get('already'))
        <p id=already style="display: none;">{!! Session::get('already') !!}</p>
        <script>
            var already = document.getElementById("already").textContent;
            Swal.fire(already)
        </script>
    @endif
    @if (Session::get('success'))
        <p id=success style="display: none;">{!! Session::get('success') !!}</p>
        <script>
            var success = document.getElementById("success").textContent;
            Swal.fire({
                title: success,
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            })
        </script>
    @endif
    @if (Session::get('warning'))
        <p id=warning style="display: none;">{!! Session::get('warning') !!}</p>
        <script>
            var warning = document.getElementById("warning").textContent;
            Swal.fire({
                title: warning,
                icon: 'warning',
                confirmButtonClass: "btn-danger",
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            })
        </script>
    @endif
</body>

</html>
