<?php
include 'includes/session.php';
?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue layout-top-nav">
<div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v13.0&appId=1346358252525630&autoLogAppEvents=1" nonce="hsdcri7l"></script>
<script src="js/sweetalert.min.js"></script>
<div class="wrapper">
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
                                        <th class="shipping">Shipping</th>
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
                    <td><input type='checkbox' class='product-checkbox' data-id='" . $row['cartid'] . "' data-price='" . $row['price'] . "' data-quantity='" . $row['quantity'] . "' data-shipping='" . $row['shipping'] . "' /></td>
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
                    <td>" . $row['shipping'] . "</td>
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
                            </div>
                        </div>
                        <div class="box box-solid">
                                        <div class="box-header with-border">
                                            <h3 class="box-title"><b>Selected Total: <span id="selected-total">₱ 0.00</span></b></h3>
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
                        <?php
                            if(isset($_SESSION['user'])){
                                $pdo = new Database();
                                $conn = $pdo->open();

                                if (!$conn) {
                                    echo "Connection Failed";
                                } else {
                                    $user_id = $_SESSION['user'];
                                    $query1 = "SELECT * FROM cart WHERE user_id = :user_id";
                                    $stmt = $conn->prepare($query1);
                                    $stmt->execute(['user_id' => $user_id]);

                                    if($stmt->rowCount() > 0) {
                                        echo "
                                            <form method='post' action='sales.php' id='payment_form'>
                                            <input type='hidden' name='selected_products' id='selected-products'>
                                            <button type='submit' class='btn btn-danger btn-lg checkout-btn' disabled>Place Order</button>
                                            </form>
                                        ";
                                    }
                                }
                                $pdo->close();
                            } else {
                                echo "<h4>You need to <a href='login.php'>Login</a> to checkout.</h4>";
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

<?php include 'includes/scripts.php'; ?>

<script>
var total = 0;
$(function(){
    $(document).on('click', '.cart_delete', function(e){
        e.preventDefault();
        var id = $(this).data('id');
        $.ajax({
            type: 'POST',
            url: 'cart_delete.php',
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
            url: 'cart_update.php',
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
                url: 'cart_update.php',
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
        if (settings.url === "cart_details.php") {
            loadCheckedState();
        }
    });
});

$(function() {
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
        $('.product-checkbox:checked').each(function() {
            var price = parseFloat($(this).data('price'));
            var quantity = parseInt($(this).data('quantity'));
            var shipping = parseFloat($(this).data('shipping'));
            selectedTotal += (price * quantity) + shipping;
            selectedProducts.push($(this).data('id'));
        });
        $('#selected-total').text('₱ ' + selectedTotal.toFixed(2));

        // Enable/disable checkout button
        $('.checkout-btn').prop('disabled', selectedProducts.length === 0);

        // Set selected products to hidden input
        $('#selected-products').val(selectedProducts.join(','));
    }

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
    $('#selected-total').text('₱ ' + selectedTotal.toFixed(2));
    
    // Enable/disable checkout button
    $('.checkout-btn').prop('disabled', $('.product-checkbox:checked').length === 0);
}

function getDetails(){
    $.ajax({
        type: 'POST',
        url: 'cart_details.php',
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
        url: 'cart_total.php',
        dataType: 'json',
        success:function(response){
            total = response;
        }
    });
}
function getCart(){
        $.ajax({
            type: 'POST',
            url: 'cart_fetch.php',
            dataType: 'json',
            success: function(response){
                $('#cart_menu').html(response.list);
                $('.cart_count').html(response.count);
            }
        });
    }

    // Call getCart() when the page loads
    getCart();

$('#shipping').change(function() {
    var shipping = $(this).val();
    if (shipping === '90') {
        $('#gcash_shipping').show();
        $('#cod_shipping').hide();
    } else if (shipping === '') {
        $('#gcash_shipping').hide();
        $('#cod_shipping').show();
    } else {
        $('#gcash_shipping').hide();
        $('#cod_shipping').hide();
    }
    getTotal(); 
});

</script>

</body>
</html>
