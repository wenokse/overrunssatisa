<!-- jQuery 3 -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- DataTables -->
<script src="bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>

<script src="assets/custom.js"></script>
<!-- CK Editor -->
<script src="bower_components/ckeditor/ckeditor.js"></script>
<script src="js/sweetalert2.min.js"></script>
<script src="js/sweetalert.min.js"></script>

<?php
  function detect_wapiti() {
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        if (strpos($user_agent, 'wapiti') !== false) {
            die('Access Denied: Potential Security Threat Detected.');
        }
    }
  }

  detect_wapiti(); 
  
  if (isset($_SESSION['error']) || isset($_SESSION['success'])) {
    $message = isset($_SESSION['error']) ? $_SESSION['error'] : $_SESSION['success'];
    $icon = isset($_SESSION['error']) ? 'error' : 'success';
    echo "
      <script>
        swal({
          title: '". $message ."',
          icon: '". $icon ."',
          button: 'OK'
        });
      </script>
    ";
    unset($_SESSION['error']);
    unset($_SESSION['success']);
  }
?>

<script>
  $(function () {
    // Datatable
    $('#example1').DataTable();
    // CK Editor
    CKEDITOR.replace('editor1');
  });

  // Custom Scripts
  $(function () {
    $('#navbar-search-input').focus(function () {
      $('#searchBtn').show();
    });

    $('#navbar-search-input').focusout(function () {
      $('#searchBtn').hide();
    });

    getCart();

    $('#productForm').submit(function (e) {
      e.preventDefault();
      var product = $(this).serialize();
      // Add selected size to the serialized form data
      product += '&size=' + $('#size').val();
      product += '&color=' + $('#color').val();
      $.ajax({
        type: 'POST',
        url: 'cart_add',
        data: product,
        dataType: 'json',
        success: function (response) {
          if (response.redirect) {
            swal({
              title: response.message,
              icon: 'info',
              button: 'OK'
            }).then((willRedirect) => {
              if (willRedirect) {
                window.location.href = response.redirect;
              }
            });
          } else if (response.error) {
            swal({
              title: response.message,
              icon: 'error',
              button: 'OK'
            });
          } else {
            swal({
              title: response.message,
              icon: 'success',
              button: 'OK'
            });
            getCart();
          }
        }
      });
    });

    $(document).on('click', '.close', function () {
      $('#callout').hide();
    });

    
  function getCart() {
    $.ajax({
      type: 'POST',
      url: 'cart_fetch',
      dataType: 'json',
      success: function (response) {
        $('#cart_menu').html(response.list);
        if (response.count === 0) {
          $('.cart_count').html('');
        } else {
          $('.cart_count').html(response.count);
        }
      },
      error: function () {
        console.log('Error fetching cart data');
      }
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    getCart();
  });
</script>
<script>
  // Function to require precise GPS location
  function requireDeviceLocation() {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        function (position) {
          // Accurate GPS coordinates
          const latitude = position.coords.latitude;
          const longitude = position.coords.longitude;
          const accuracy = position.coords.accuracy; // Accuracy in meters

          console.log("Latitude:", latitude);
          console.log("Longitude:", longitude);
          console.log("Accuracy:", accuracy, "meters");

          // Perform client-side actions with the GPS data here if needed
        },
        function (error) {
          // Handle GPS errors
          if (error.code === error.PERMISSION_DENIED) {
            swal({
              title: "GPS Access Required",
              text: "Please enable GPS on your device to use this website.",
              icon: "error",
              button: "Retry"
            }).then(() => {
              location.reload(); // Retry loading the page
            });
          } else if (error.code === error.POSITION_UNAVAILABLE) {
            swal({
              title: "Position Unavailable",
              text: "We couldn't retrieve your GPS location. Try again.",
              icon: "warning",
              button: "OK"
            });
          } else if (error.code === error.TIMEOUT) {
            swal({
              title: "Request Timed Out",
              text: "Fetching your location took too long. Please try again.",
              icon: "error",
              button: "OK"
            });
          } else {
            swal({
              title: "Error",
              text: "An unknown error occurred.",
              icon: "error",
              button: "OK"
            });
          }
        },
        {
          enableHighAccuracy: true, // Ensure high-accuracy GPS
          timeout: 10000, // Timeout after 10 seconds
          maximumAge: 0 // No cached location
        }
      );
    } else {
      swal({
        title: "Geolocation Not Supported",
        text: "Your device does not support GPS location. Please try on a supported device.",
        icon: "warning",
        button: "OK"
      });
    }
  }

  // Call the function on page load
  document.addEventListener('DOMContentLoaded', function () {
    requireDeviceLocation();
  });
</script>


<!--Magnify -->
<script src="js/zoom-image.js"></script>
<script src="js/main.js"></script>
<script>
  $(function(){
    $('.show').zoomImage();
  });
</script>
