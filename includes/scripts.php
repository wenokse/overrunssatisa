<script src="bower_components/jquery/dist/jquery.min.js"></script>
    <script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <script src="bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
    <script src="bower_components/fastclick/lib/fastclick.js"></script>
    <script src="dist/js/adminlte.min.js"></script>
    <script src="bower_components/ckeditor/ckeditor.js"></script>
    <script src="js/sweetalert2.min.js"></script>
    <script src="js/sweetalert.min.js"></script>
    <script src="js/zoom-image.js"></script>
    <script src="js/main.js"></script>

    <?php
    
    // Detect Wapiti or similar tools
    function detect_wapiti() {
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
            if (strpos($user_agent, 'wapiti') !== false) {
                die('Access Denied: Potential Security Threat Detected.');
            }
        }
    }
    detect_wapiti();

    // Display success or error messages using SweetAlert
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
 // Anti-debugging script with fix for refresh
 let tamperingDetected = false;

const antiDebug = function () {
    setInterval(function () {
        (function () {
            try {
                (function testDevTools() {}.constructor('debugger')());
            } catch (e) {
                if (!tamperingDetected) {
                    tamperingDetected = true;
                    console.warn("Tampering or debugging detected.");
                }
            }
        })();
    }, 500);
};

antiDebug();


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


<!--Magnify -->
<script src="js/zoom-image.js"></script>
<script src="js/main.js"></script>
<script>
  $(function(){
    $('.show').zoomImage();
  });
</script>
