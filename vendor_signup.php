<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Under Construction</title>
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css" rel="stylesheet">
</head>
<body>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
    <script>
        Swal.fire({
            title: '<strong>This File Is Under Construction</strong>',
            icon: 'info',
            html: 'Please check back later.',
            showCloseButton: true,
            focusConfirm: false,
            confirmButtonText: 'Okay',
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'index';  // Replace with your actual index page URL
            }
        });
    </script>
</body>
</html>
