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

<!-- <script src="assets/js/jquery-3.6.0.min.js"></script> -->
<!-- <script src="assets/js/bootstrap5.bundle.min.js"></script> -->
<script src="assets/custom.js"></script>
<!-- CK Editor -->
<script src="bower_components/ckeditor/ckeditor.js"></script>
<script src="js/sweetalert2.min.js"></script>
<script src="js/sweetalert.min.js"></script>

<?php
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
  document.addEventListener('contextmenu', function(event) {
    event.preventDefault();
  });

  document.addEventListener('keydown', function(event) {
    if (event.keyCode == 123) { // F12
      event.preventDefault();
    }
    if (event.ctrlKey && event.shiftKey && (event.keyCode == 73 || event.keyCode == 74)) { 
      event.preventDefault();
    }
    if (event.ctrlKey && (event.keyCode == 85 || event.keyCode == 83)) { 
      event.preventDefault();
    }
    if (event.ctrlKey && event.shiftKey && event.keyCode == 67) { 
      event.preventDefault();
    }
  });

  document.addEventListener('dragstart', function(event) {
    event.preventDefault();
  });

  document.addEventListener('keydown', function(event) {
    if (event.ctrlKey && event.keyCode == 65) { 
      event.preventDefault();
    }
  });

  (function() {
    // Function to generate a random string
    function randomString(length) {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        return Array.from({length}, () => chars[Math.floor(Math.random() * chars.length)]).join('');
    }

    // Obfuscated script content
    const scriptContent = `
    (${function() {
        const ${randomString(5)} = ${160};
        let ${randomString(5)} = false;
        const ${randomString(5)} = [
            "script[src*='bower_components']",
            "script[src*='assets']",
            "script[src*='dist']",
            "script[src*='js']",
            "link[rel='stylesheet']",
            "style",
            "meta",
            "title"
        ];

        function ${randomString(8)}() {
            ${randomString(5)}.forEach(function(selector) {
                document.querySelectorAll(selector).forEach(function(element) {
                    element.setAttribute('type', 'text/plain');
                    element.setAttribute('data-original-src', element.getAttribute('src'));
                    element.removeAttribute('src');
                    element.textContent = '';
                });
            });
        }

        function ${randomString(8)}() {
            ${randomString(5)}.forEach(function(selector) {
                document.querySelectorAll(selector).forEach(function(element) {
                    element.setAttribute('type', 'text/javascript');
                    const originalSrc = element.getAttribute('data-original-src');
                    if (originalSrc) {
                        element.setAttribute('src', originalSrc);
                        element.removeAttribute('data-original-src');
                    }
                });
            });
        }

        function ${randomString(8)}() {
            document.head.innerHTML = \`
                <style>
                    body {
                        background-color: black;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        height: 100vh;
                        margin: 0;
                        font-family: Arial, sans-serif;
                    }
                    h1 {
                        color: #39FF14;
                        text-shadow: 0 0 10px #39FF14, 0 0 20px #39FF14, 0 0 30px #39FF14;
                        animation: bounce 1s infinite alternate;
                        text-align: center;
                    }
                    @keyframes bounce {
                        from { transform: translateY(0px); }
                        to { transform: translateY(-20px); }
                    }
                </style>
            \`;
            document.body.innerHTML = '<h1>DevTools detected.<br>Content is hidden.</h1>';
        }

        function ${randomString(8)}() {
            location.reload();
        }

        function ${randomString(8)}() {
            const widthThreshold = window.outerWidth - window.innerWidth > ${randomString(5)};
            const heightThreshold = window.outerHeight - window.innerHeight > ${randomString(5)};

            if (widthThreshold || heightThreshold) {
                if (!${randomString(5)}) {
                    ${randomString(8)}();
                    ${randomString(8)}();
                    ${randomString(5)} = true;
                    console.clear();
                    console.log('%cDevTools detected. Content is hidden.', 'color: #39FF14; font-size: 24px; text-shadow: 0 0 10px #39FF14;');
                }
            } else {
                if (${randomString(5)}) {
                    ${randomString(8)}();
                    ${randomString(8)}();
                    ${randomString(5)} = false;
                }
            }
        }

        window.addEventListener('load', ${randomString(8)});
        window.addEventListener('resize', ${randomString(8)});

        window.addEventListener('keydown', function(event) {
            if (
                event.ctrlKey && (
                    event.keyCode === 85 || 
                    event.keyCode === 83 || 
                    event.keyCode === 123 || 
                    (event.shiftKey && (event.keyCode === 73 || event.keyCode === 74))
                )
            ) {
                event.preventDefault();
                ${randomString(8)}();
                ${randomString(8)}();
            }
        });

        document.addEventListener('contextmenu', function(event) {
            event.preventDefault();
        });

        document.addEventListener('selectstart', function(event) {
            event.preventDefault();
        });

        setInterval(${randomString(8)}, 1000);
    }})();
    `;

    // Create a new script element
    const script = document.createElement('script');
    
    // Set the content of the script
    script.textContent = scriptContent;

    // Append the script to the document body
    document.body.appendChild(script);
})();

</script>

<script>
  $(function () {
    // Datatable
    $('#example1').DataTable();
    // CK Editor
    CKEDITOR.replace('editor1');
  });

  // Custom Scripts
  $(function(){
    $('#navbar-search-input').focus(function(){
      $('#searchBtn').show();
    });

    $('#navbar-search-input').focusout(function(){
      $('#searchBtn').hide();
    });

    getCart();

    $('#productForm').submit(function(e){
      e.preventDefault();
      var product = $(this).serialize();
      // Add selected size to the serialized form data
      product += '&size=' + $('#size').val();
      product += '&color=' + $('#color').val();
      $.ajax({
            type: 'POST',
            url: 'cart_add.php',
            data: product,
            dataType: 'json',
            success: function(response){
                if(response.redirect) {
                    // Display message first
                    swal({
                        title: response.message,
                        icon: 'info',
                        button: 'OK'
                    }).then((willRedirect) => {
                        if (willRedirect) {
                            // Redirect to the login page
                            window.location.href = response.redirect;
                        }
                    });
                } else if(response.error) {
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

    $(document).on('click', '.close', function(){
      $('#callout').hide();
    });

  });

  function getCart() {
    $.ajax({
        type: 'POST',
        url: 'cart_fetch.php',
        dataType: 'json',
        success: function(response) {
            $('#cart_menu').html(response.list);
            if (response.count === 0) {
                $('.cart_count').html('');
            } else {
                $('.cart_count').html(response.count);
            }
        },
        error: function() {
            console.log('Error fetching cart data');
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    getCart();
});

</script>

<!--Magnify -->
<script src="js/zoom-image.js"></script>
<script src="js/main.js"></script>
<!-- <script src="magnify/magnify.min.js"></script> -->
<script>
  $(function(){
    $('.show').zoomImage();
  });
</script>
