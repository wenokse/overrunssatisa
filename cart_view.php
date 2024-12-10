<?php include 'includes/firewall.php'; ?>
<?php
include 'includes/session.php';
?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue layout-top-nav">
<div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v13.0&appId=1346358252525630&autoLogAppEvents=1" nonce="hsdcri7l"></script>
<script src="js/sweetalert.min.js"></script>
<div class="wrapper">
    <!-- Preloader -->
    <div id="preloader">
        <div class="loader"></div>
    </div>
    <style>
        .container2 {
            margin: 0px 50px 0px 50px; /* top right bottom left */
        }
        /* Preloader styles */
        #preloader {
            position: fixed;
            left: 0;
            top: 0;
            z-index: 999;
            width: 100%;
            height: 100%;
            overflow: visible;
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .loader {
            border: 16px solid #f3f3f3; /* Light grey */
            border-top: 16px solid #3498db; /* Blue */
            border-radius: 50%;
            width: 120px;
            height: 120px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .hidden {
            display: none;
            opacity: 0;
            transition: opacity 5s ease-in-out;
        }

        .visible {
            display: block;
            opacity: 1;
        }
        /* Mobile Cart Styles */
@media screen and (max-width: 768px) {
    .container2 {
        margin: 0;
        padding: 10px;
    }

    /* Make table responsive */
    .table-responsive {
        border: none;
    }

    /* Hide table headers on mobile */
    .table thead {
        display: none;
    }

    /* Convert table rows to cards */
    .table tbody tr {
        display: block;
        background: #fff;
        margin-bottom: 1rem;
        padding: 1rem;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    /* Style table cells */
    .table tbody td {
        display: flex;
        padding: 0.5rem 0;
        border: none;
        align-items: center;
    }

    /* Checkbox and delete button container */
    .table tbody td:nth-child(1),
    .table tbody td:nth-child(2) {
        display: inline-block;
        width: auto;
        padding-right: 1rem;
    }

    /* Product image styling */
    .table tbody td img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        margin-right: 1rem;
    }

    /* Product info layout */
    .table tbody td:nth-child(4) {
        font-weight: bold;
        font-size: 1.1rem;
    }

    /* Size and Color info */
    .table tbody td:nth-child(5),
    .table tbody td:nth-child(6) {
        display: inline-block;
        width: auto;
        padding-right: 1rem;
        color: #666;
        font-size: 0.9rem;
    }

    /* Price styling */
    .table tbody td:nth-child(7) {
        color: #ff4d4f;
        font-weight: bold;
        font-size: 1.1rem;
    }

    /* Quantity input group */
    .table tbody td.input-group {
        width: 120px;
        margin: 0.5rem 0;
    }

    /* Subtotal styling */
    .table tbody td:last-child {
        font-weight: bold;
        color: #ff4d4f;
        font-size: 1.2rem;
    }

    /* Sticky bottom bar for total and checkout */
    .box.box-solid:last-of-type {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: white;
        padding: 1rem;
        box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
        z-index: 1000;
    }

    /* Adjust main content padding to account for sticky bottom bar */
    .content-wrapper {
        padding-bottom: 140px;
    }

    /* Style the delivery address box */
    .delivery-address-display {
        background: #fff;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .address-details {
        margin: 1rem 0;
    }

    .address-details p {
        margin: 0.5rem 0;
        font-size: 0.95rem;
    }

    /* Style buttons */
    .btn {
        border-radius: 4px;
        padding: 0.5rem 1rem;
    }

    .checkout-btn {
        width: 100%;
        margin-top: 1rem;
        padding: 0.8rem;
        font-size: 1.1rem;
    }

    /* Shipping info styling */
    #cod_shipping {
        background: #fff8f8;
        padding: 0.8rem;
        border-radius: 4px;
        margin: 1rem 0;
    }

    /* Terms checkbox styling */
    .form-group.has-feedback {
        margin: 1rem 0;
        padding: 0.5rem;
        background: #f8f8f8;
        border-radius: 4px;
    }
}

/* Loading spinner adjustments for mobile */
@media screen and (max-width: 768px) {
    .loader {
        width: 60px;
        height: 60px;
        border-width: 8px;
    }
}
    </style>
    <script>
        window.addEventListener('load', function() {
            var preloader = document.getElementById('preloader');
            setTimeout(function() {
                preloader.style.display = 'none'; 
            }, 100);  // 3000 milliseconds = 3 seconds
        });
    </script>
</div>

<?php
  if (isset($_SESSION['error']) || isset($_SESSION['success'])) {
    $message = isset($_SESSION['error']) ? $_SESSION['error'] : $_SESSION['success'];
    $icon = isset($_SESSION['error']) ? 'error' : 'success';
    echo "
      <script src='js/sweetalert.min.js'></script>
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

<?php 
	
	if (isset($_SESSION['user'])) {
		
		include 'includes/navbar.php';
	} else {
		
		include 'includes/home_navbar.php';
	}
	
	?>
    <div class="content-wrapper">
        <div class="container">
            <!-- Main content -->
            
            <section class="content">
                <div class="row">
                    <div class="">
                    <?php
                        if(isset($_SESSION['error'])){
                            echo "
                            <script>
                              swal({
                                title: 'Error!',
                                text: '". $_SESSION['error'] ."',
                                icon: 'error',
                                button: 'OK'
                              });
                            </script>";
                            unset($_SESSION['error']);
                        }
                        if(isset($_SESSION['success'])){
                           
                            $stmt_product = $conn->prepare("SELECT COUNT(*) AS numrows FROM cart WHERE user_id=:user_id AND product_id=:product_id"); 
                            $stmt_product->execute(['user_id'=>$user['id'], 'product_id'=>$id]); 
                            $row_product = $stmt_product->fetch();
                            if($row_product['numrows'] == 1){
                               
                                $stmt_update_shipping = $conn->prepare("UPDATE cart SET shipping = 100 WHERE user_id=:user_id AND product_id=:product_id"); 
                                $stmt_update_shipping->execute(['user_id'=>$user['id'], 'product_id'=>$id]); 
                            }

                            echo "
                            <script>
                              swal({
                                title: 'Success!',
                                text: '". $_SESSION['success'] ."',
                                icon: 'success',
                                button: 'OK'
                              });
                            </script>";
                            unset($_SESSION['success']);
                        }
                        ?>
                        
                        <div class="box box-solid">
                            <div class='box-header with-border'>
                                <h3 class='box-title'>YOUR CART</h3>
                            </div>
                            
                            <div class="box-body table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                    <th><input type="checkbox" id="select-all"></th>
                                        <th></th>
                                        <th>Photo</th>
                                        <th>Name</th>
                                        <th>Size</th>
                                        <th>Color</th>
                                        <th>Price</th>
                                        <th width="18%" class="text-center">Quantity</th>
                                        
                                        <th>Subtotal</th>
                                    </thead>
                                    
                                    <tbody id="tbody">
                                    <?php
                                        $conn = $pdo->open();
                                        try {
                                            $stmt = $conn->prepare("SELECT *, cart.id AS cartid FROM cart LEFT JOIN products ON products.id=cart.product_id WHERE cart.user_id=:user_id");
                                            $stmt->execute(['user_id' => $user['id']]);
                                            foreach ($stmt as $row) {
                                                $image = (!empty($row['photo'])) ? 'images/' . $row['photo'] : 'images/noimage.jpg';
                                                echo "
                                                <tr>
                                                    <td><input type='checkbox' class='product-checkbox' data-id='" . $row['cartid'] . "' data-price='" . $row['price'] . "' data-quantity='" . $row['quantity'] ."' data-shipping='' /></td>
                                                    <td>
                                                        <button class='btn btn-danger btn-sm cart_delete' data-id='" . $row['cartid'] . "'><i class='fa fa-trash'></i></button>
                                                    </td>
                                                    <td><img src='" . $image . "' width='30px' height='30px'></td>
                                                    <td>" . $row['name'] . "</td>
                                                    <td>" . $row['size'] . "</td>
                                                    <td>" . $row['color'] . "</td>
                                                    <td>" . number_format($row['price'], 2) . "</td>
                                                    <td class='input-group'>
                                                        <span class='input-group-btn'>
                                                            <button type='button' id='minus' class='btn btn-default btn-sm btn-flat minus' data-id='" . $row['cartid'] . "'><i class='fa fa-minus'></i></button>
                                                        </span>
                                                        <input type='text' class='form-control input-sm' value='" . $row['quantity'] . "' id='qty_" . $row['cartid'] . "'>
                                                        <span class='input-group-btn'>
                                                            <button type='button' id='add' class='btn btn-default btn-sm btn-flat add' data-id='" . $row['cartid'] . "'><i class='fa fa-plus'></i>
                                                            </button>
                                                        </span>
                                                    </td>
                                                   
                                                    <td>" . number_format($row['price'] * $row['quantity'], 2) . "</td>
                                                </tr>
                                                ";
                                            }
                                        } catch (PDOException $e) {
                                            echo "There is some problem in connection: " . $e->getMessage();
                                        }

                                        $pdo->close();
                                    ?>
                                </tbody>


                                </table>
                                <div id="cod_shipping" style="">
                            <p style="color: red;">Shipping: 100</p>
                        </div>
                            </div>
                        </div>
                        <div class="box box-solid">
                                        <div class="box-header with-border">
                                            <h3 class="box-title"><b>Selected Total: <span id="selected-total">₱ 0.00</span></b></h3>
                                        </div>
                                    </div>
                                    <div class="box box-solid">
                                    <div class="box-header with-border">
                                        <h3 class="box-title"><b>Delivery Address</b></h3>
                                    </div>
                                    <div class="box-body">
                                    <?php
                                    $conn = $pdo->open();
                                    try {
                                        $stmt = $conn->prepare("SELECT * FROM delivery_address WHERE user_id=:user_id");
                                        $stmt->execute(['user_id' => $user['id']]);
                                        $address = $stmt->fetch();

                                        if($address){
                                            echo "
                                                <div class='delivery-address-display'>
                                                    <div class='address-checkbox-container'>
                                                        <input type='checkbox' id='confirm_address' class='address-checkbox'>
                                                        <label for='confirm_address'>I confirm this is my correct delivery address:</label>
                                                    </div>
                                                    <div class='address-details'>
                                                        <p><strong>Name:</strong> ".$address['recipient_name']."</p>
                                                        <p><strong>Phone:</strong> ".$address['phone']."</p>
                                                        <p><strong>Address:</strong> ".$address['address']."</p>
                                                        <p><strong>Purok:</strong> ".$address['address2']."</p>
                                                        <p><strong>Address2:</strong> ".$address['address3']."</p>
                                                        <button type='button' class='btn btn-primary btn-sm edit-address'>Edit Address</button>
                                                        <button type='button' class='btn btn-danger btn-sm delete-address'>Delete Address</button>
                                                    </div>
                                                </div>
                                            ";
                                        } else {
                                            echo "
                                                <div class='no-address-message'>
                                                    <p>No delivery address found. Please add your delivery address.</p>
                                                    <button type='button' class='btn btn-primary btn-sm' data-toggle='modal' data-target='#addAddressModal'>
                                                        Add Delivery Address
                                                    </button>
                                                </div>
                                            ";
                                        }
                                    } catch(PDOException $e) {
                                        echo "Connection error: " . $e->getMessage();
                                    }
                                    $pdo->close();
                                    ?>
                                    </div>
                                </div>

                        <div class="box box-solid">
                        <div class="box-header with-border">
                        <div id="cod_shipping" style="">
                            <p style="color: red;">Shipping: 100</p>
                        </div>
                        <div id="gcash_shipping" style="display: none;">
                            <img src="image/GCash.jpg" alt="GCash QR Code">
                            <p>Overruns Shipping</p>
                            <p>GCash Number: 09073711101</p>
                            <p style="color: red;">Shipping: 90</p>
                        </div>
                        <div class='form-group'>
                        <h3 class="box-title"><b>Payment Method: COD only </b></h3><br><br></div>
                        </div>
                        </div>
                        <div class="form-group has-feedback">
                            <input type="checkbox" id="terms" name="terms" class="form-control-feedback1" required>
                            <label for="terms">I agree to the <a href="#" id="termsLink">Terms and Conditions</a></label>
                        </div>
                        <?php
                            if(isset($_SESSION['user'])){
                                $pdo = new Database();
                                $conn = $pdo->open();

                                if (!$conn) {
                                    echo "Connection Failed";
                                } else {
                                    $user_id = $_SESSION['user'];
                                    
                                    // Check for delivery address first
                                    $addr_stmt = $conn->prepare("SELECT COUNT(*) as addr_count FROM delivery_address WHERE user_id = :user_id");
                                    $addr_stmt->execute(['user_id' => $user_id]);
                                    $addr_result = $addr_stmt->fetch();
                                    
                                    // Check cart items
                                    $cart_stmt = $conn->prepare("SELECT COUNT(*) as cart_count FROM cart WHERE user_id = :user_id");
                                    $cart_stmt->execute(['user_id' => $user_id]);
                                    $cart_result = $cart_stmt->fetch();

                                    if($addr_result['addr_count'] == 0) {
                                        echo "
                                            <div class='alert alert-warning'>
                                                <h4>Please add a delivery address before placing your order.</h4>
                                                <button type='button' class='btn btn-primary btn-sm' data-toggle='modal' data-target='#addAddressModal'>
                                                    Add Delivery Address
                                                </button>
                                            </div>
                                        ";
                                    } 
                                    else if($cart_result['cart_count'] > 0) {
                                        echo "
                                            <form method='post' action='sales' id='payment_form'>
                                                <input type='hidden' name='selected_products' id='selected-products'>
                                                <button type='submit' class='btn btn-danger btn-lg checkout-btn' disabled>Place Order</button>
                                            </form>
                                        ";
                                    }
                                }
                                $pdo->close();
                            } else {
                                echo "<h4>You need to <a href='login'>Login</a> to checkout.</h4>";
                            }
                            ?>
                    </div>
                    <div class="col-sm-3">
                        <?php include 'includes/sidebar.php'; ?>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>
</div>
<?php include 'includes/address_modal.php'; ?>
<?php include 'includes/scripts.php'; ?>
<script>
    // Modal script
 var modal = document.getElementById("termsModal");
    var link = document.getElementById("termsLink");
    var span = document.getElementsByClassName("close1")[0];

    link.onclick = function(e) {
        e.preventDefault();
        modal.style.display = "block";
    }

    span.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
    
   
$(function() {
    // Function to check if delivery address exists
    function hasDeliveryAddress() {
        return $('.delivery-address-display').length > 0;
    }

    // Function to check if order can be placed
    function canPlaceOrder() {
        var hasSelectedProducts = $('.product-checkbox:checked').length > 0;
        var addressExists = hasDeliveryAddress();
        var hasConfirmedAddress = $('#confirm_address').is(':checked');
        var hasAcceptedTerms = $('#terms').is(':checked');
        
        // Additional check to ensure address exists
        if (!addressExists) {
            $('.checkout-btn').attr('title', 'Please add a delivery address first');
            return false;
        }

        // Check for address confirmation
        if (!hasConfirmedAddress) {
            $('.checkout-btn').attr('title', 'Please confirm your delivery address');
            return false;
        }
        if (!hasAcceptedTerms) {
            $('.checkout-btn').attr('title', 'Please accept the terms and conditions');
            return false;
        }

        // Check for product selection
        if (!hasSelectedProducts) {
            $('.checkout-btn').attr('title', 'Please select at least one product');
            return false;
        }

        return true;
    }

    // Update button state
    function updateOrderButton() {
        var canOrder = canPlaceOrder();
        $('.checkout-btn').prop('disabled', !canOrder);

        // Show appropriate message if button is disabled
        if (!canOrder) {
            var message = '';
            if (!hasDeliveryAddress()) {
                message = 'Please add a delivery address first. ';
            } else if (!$('#confirm_address').is(':checked')) {
                message = 'Please confirm your delivery address. ';
            } else if ($('.product-checkbox:checked').length === 0) {
                message = 'Please select at least one product. ';
            }else if (!$('#terms').is(':checked')) {
                message = 'Please accept the terms and conditions. ';
            }
            
            $('.checkout-btn').attr('title', message.trim());
        } else {
            $('.checkout-btn').attr('title', '');
        }
    }

    // Update total and button state when checkboxes change
    $('#confirm_address, #terms, .product-checkbox').change(function() {
        updateTotal();
        updateOrderButton();
    });

    // Select all products checkbox handler
    $('#select-all').change(function() {
        $('.product-checkbox').prop('checked', $(this).prop('checked'));
        updateTotal();
        saveCheckedState();
        updateOrderButton();
    });

    // Update total amount and save state
    

    // Save checked state to localStorage
    function saveCheckedState() {
        var checkedProducts = [];
        $('.product-checkbox:checked').each(function() {
            checkedProducts.push($(this).data('id'));
        });
        localStorage.setItem('checkedProducts', JSON.stringify(checkedProducts));
    }

    // Load checked state from localStorage
    function loadCheckedState() {
        var checkedProducts = JSON.parse(localStorage.getItem('checkedProducts')) || [];
        checkedProducts.forEach(function(id) {
            $('.product-checkbox[data-id="' + id + '"]').prop('checked', true);
        });
        updateTotal();
        updateOrderButton();
    }

    // Modify the form submission handler
    $('#payment_form').submit(function(e) {
        e.preventDefault();
        
        // Check if address exists before submitting
        if (!hasDeliveryAddress()) {
            swal({
                title: 'Error!',
                text: 'Please add a delivery address before placing your order.',
                icon: 'error',
                button: 'OK'
            });
            return false;
        }

        // Check if address is confirmed
        if (!$('#confirm_address').is(':checked')) {
            swal({
                title: 'Error!',
                text: 'Please confirm your delivery address before placing your order.',
                icon: 'error',
                button: 'OK'
            });
            return false;
        }
        if (!$('#terms').is(':checked')) {
            swal({
                title: 'warning!',
                text: 'Please accept the terms and conditions before placing your order.',
                icon: 'warning',
                button: 'OK'
            });
            return false;
        }

        // If all checks pass, submit the form
        this.submit();
    });

    // Initialize the page
    loadCheckedState();
    updateOrderButton();

    // Show OTP Modal after address submission
$('#addressForm').on('submit', function(e) {
    e.preventDefault();
    var formData = $(this).serialize();
    
    $.ajax({
        type: 'POST',
        url: 'address_add',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (!response.error) {
                $('#addAddressModal').modal('hide');
                swal({
                    icon: 'success',
                    title: 'OTP Sent!',
                    text: 'An OTP has been sent to your phone. Please verify.',
                    confirmButtonText: 'OK'
                }).then(() => {
                    $('#otpModal').modal('show');
                });
            } else {
                swal({
                    icon: 'error',
                    title: 'Error',
                    text: response.message,
                    confirmButtonText: 'Try Again'
                });
            }
        },
        error: function(xhr) {
            swal({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while submitting your address. Please try again.',
                confirmButtonText: 'OK'
            });
        }
    });
});

// Handle OTP verification
$('#otpForm').on('submit', function(e) {
    e.preventDefault();
    var otp = $('#otp').val().trim();

    if (otp.length !== 6 || isNaN(otp)) {
        swal({
            icon: 'warning',
            title: 'Invalid OTP',
            text: 'Please enter a valid 6-digit OTP.',
            confirmButtonText: 'OK'
        });
        return;
    }

    $.ajax({
        type: 'POST',
        url: 'verify_otp',
        data: { otp: otp },
        dataType: 'json',
        success: function(response) {
            if (!response.error) {
                swal({
                    icon: 'success',
                    title: 'OTP Verified!',
                    text: response.message,
                    confirmButtonText: 'OK'
                }).then(() => {
                    location.reload();
                });
            } else {
                swal({
                    icon: 'error',
                    title: 'Error',
                    text: response.message,
                    confirmButtonText: 'Try Again'
                });
            }
        },
        error: function(xhr) {
            swal({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while verifying the OTP. Please try again.',
                confirmButtonText: 'OK'
            });
        }
    });
});
// Handle Resend OTP
$('#resendOtpBtn').on('click', function(e) {
    e.preventDefault();

    swal({
        title: 'Resend OTP?',
        text: 'Are you sure you want to request a new OTP?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, resend it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                url: 'resend_otp',
                dataType: 'json',
                success: function(response) {
                    if (!response.error) {
                        swal({
                            icon: 'success',
                            title: 'OTP Resent!',
                            text: response.message,
                            confirmButtonText: 'OK'
                        });
                    } else {
                        swal({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr) {
                    swal({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while resending the OTP. Please try again.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    });
});


    // Handle address deletion
    $('.delete-address').click(function() {
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this address!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    type: 'POST',
                    url: 'address_delete',
                    dataType: 'json',
                    success: function(response) {
                        if(!response.error) {
                            swal("Success!", "Address has been deleted!", "success")
                            .then(function() {
                                location.reload();
                            });
                        } else {
                            swal("Error!", response.message, "error");
                        }
                    }
                });
            }
        });
    });

    // Handle address editing
    $('.edit-address').click(function() {
        $('#addAddressModal').modal('show');
        $.ajax({
            type: 'POST',
            url: 'get_address',
            dataType: 'json',
            success: function(response) {
                if (!response.error) {
                    $('#recipient_name').val(response.recipient_name);
                    $('#phone').val(response.phone);
                    $('#address').val(response.address);
                    $('#address2').val(response.address2);
                    $('#address3').val(response.address3);
                }
            }
        });
    });
});
</script>
<style>
.address-checkbox-container {
    margin-bottom: 15px;
    padding: 10px;
    background-color: #f8f9fa;
    border-radius: 4px;
}

.address-checkbox {
    margin-right: 10px;
}

.address-details {
    margin-left: 25px;
    padding: 10px;
    border-left: 3px solid #007bff;
}

.address-checkbox-container label {
    font-weight: 600;
    color: #495057;
}
</style>
<script>
var total = 0;
$(function(){
    $(document).on('click', '.cart_delete', function(e){
        e.preventDefault();
        var id = $(this).data('id');
        $.ajax({
            type: 'POST',
            url: 'cart_delete',
            data: {id:id},
            dataType: 'json',
            success: function(response){
                if(!response.error){
                    swal({
                        title: 'Deleted!',
                        text: response.message,
                        icon: 'success',
                        button: 'OK'
                    });
                    getDetails();
                    getCart();
                    getTotal();
                } else {
                    swal({
                        title: 'Error!',
                        text: response.message,
                        icon: 'error',
                        button: 'OK'
                    });
                }
            }
        });
    });



    $(document).on('click', '.minus', function(e){
        e.preventDefault();
        var id = $(this).data('id');
        var qty = $('#qty_'+id).val();
        if(qty > 1){
            qty--;
        }
        $('#qty_'+id).val(qty);
        $.ajax({
            type: 'POST',
            url: 'cart_update',
            data: {
                id: id,
                qty: qty,
            },
            dataType: 'json',
            success: function(response){
                if(!response.error){
                   
                    getDetails();
                    getCart();
                    getTotal();
                } 
            }
        });
    });

    $(document).on('click', '.add', function(e){
        e.preventDefault();
        var id = $(this).data('id');
        var qty = $('#qty_'+id).val();
        qty++;

        const stock = $('#qty_'+id).data('stock');
        if(stock >= qty){
            $('#qty_'+id).val(qty);
            $.ajax({
                type: 'POST',
                url: 'cart_update',
                data: {
                    id: id,
                    qty: qty,
                },
                dataType: 'json',
                success: function(response){
                    if(!response.error){
                        
                        getDetails();
                        getCart();
                        getTotal();
                    } 
                }
            });
        }
    });
    

    getDetails();
    getTotal();
    

    $(document).ajaxComplete(function(event, xhr, settings) {
        if (settings.url === "cart_details") {
            loadCheckedState();
        }
    });
});

$(function() {
    
    function canPlaceOrder() {
        return $('.product-checkbox:checked').length > 0 && $('#confirm_address').is(':checked');
    }

    // Update button state when address checkbox changes
    $('#confirm_address').change(function() {
        $('.checkout-btn').prop('disabled', !canPlaceOrder());
    });

    $(document).on('change', '.product-checkbox', function() {
        updateTotal();
        saveCheckedState();
    });

    $('#select-all').change(function() {
        $('.product-checkbox').prop('checked', $(this).prop('checked'));
        updateTotal();
        saveCheckedState();
    });

    function updateTotal() {
    var selectedTotal = 0;
    var selectedProducts = [];
    var hasSelectedProducts = false;

    $('.product-checkbox:checked').each(function() {
        hasSelectedProducts = true;
        var price = parseFloat($(this).data('price'));
        var quantity = parseInt($(this).data('quantity'));
        var shipping = parseFloat($(this).data('shipping') || 100);
        
        // Add product price * quantity + shipping
        selectedTotal += (price * quantity) + shipping;
        selectedProducts.push($(this).data('id'));
    });

    // Format the total with 2 decimal places and display
    $('#selected-total').text('₱ ' + selectedTotal.toFixed(2));
    
    // Update hidden input with selected products
    $('#selected-products').val(selectedProducts.join(','));
    
    // Update checkout button state
    $('.checkout-btn').prop('disabled', !hasSelectedProducts || !$('#confirm_address').is(':checked'));
}

// Update when checkboxes change
$(document).on('change', '.product-checkbox', function() {
    updateTotal();
    saveCheckedState();
});

// Update when "Select All" changes
$('#select-all').change(function() {
    $('.product-checkbox').prop('checked', $(this).prop('checked'));
    updateTotal();
    saveCheckedState();
});

function saveCheckedState() {
    var checkedProducts = [];
    $('.product-checkbox:checked').each(function() {
        checkedProducts.push($(this).data('id'));
    });
    localStorage.setItem('checkedProducts', JSON.stringify(checkedProducts));
}

function loadCheckedState() {
    var checkedProducts = JSON.parse(localStorage.getItem('checkedProducts')) || [];
    checkedProducts.forEach(function(id) {
        $('.product-checkbox[data-id="' + id + '"]').prop('checked', true);
    });
    updateTotal();
}

    loadCheckedState();
});


function saveCheckedState() {
    var checkedProducts = [];
    $('.product-checkbox:checked').each(function() {
        checkedProducts.push($(this).data('id'));
    });
    localStorage.setItem('checkedProducts', JSON.stringify(checkedProducts));
}


function loadCheckedState() {
    var checkedProducts = JSON.parse(localStorage.getItem('checkedProducts')) || [];
    checkedProducts.forEach(function(id) {
        $('.product-checkbox[data-id="' + id + '"]').prop('checked', true);
    });
    updateTotal();
}
$(document).on('change', '.product-checkbox', function() {
    var id = $(this).data('id');
    $('input[name="selected_products[]"][value="' + id + '"]').prop('checked', $(this).prop('checked'));
    updateTotal();
    saveCheckedState();
});

$('#select-all').change(function() {
    $('.product-checkbox').prop('checked', $(this).prop('checked'));
    $('input[name="selected_products[]"]').prop('checked', $(this).prop('checked'));
    updateTotal();
    saveCheckedState();
});

// Individual checkboxes
$(document).on('change', '.product-checkbox', function() {
    updateTotal();
    saveCheckedState();
});

function updateTotal() {
    var selectedTotal = 0;
    $('.product-checkbox:checked').each(function() {
        var price = parseFloat($(this).data('price'));
        var quantity = parseInt($(this).data('quantity'));
        var shipping = parseFloat($(this).data('shipping'));
        selectedTotal += (price * quantity) + shipping;
    });
    
    // Enable/disable checkout button
    $('.checkout-btn').prop('disabled', $('.product-checkbox:checked').length === 0);
}

function getDetails(){
    $.ajax({
        type: 'POST',
        url: 'cart_details',
        dataType: 'json',
        success: function(response){
            $('#tbody').html(response);
            getCart();
            loadCheckedState(); // Load checked state after details are loaded
        }
    });
}

function getTotal(){
    $.ajax({
        type: 'POST',
        url: 'cart_total',
        dataType: 'json',
        success:function(response){
            total = response;
        }
    });
}
function getCart(){
        $.ajax({
            type: 'POST',
            url: 'cart_fetch',
            dataType: 'json',
            success: function(response){
                $('#cart_menu').html(response.list);
                $('.cart_count').html(response.count);
            }
        });
    }

    // Call getCart() when the page loads
    getCart();



</script>

</body>
</html>
