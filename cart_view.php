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
                            <div class="box-body">
                                <table class="table table-bordered">
                                    <thead>
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
                                    </tbody>
                                </table>
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
                            $conn = mysqli_connect("127.0.0.1;port=3306", "u510162695_root", "1RootEcomm", "u510162695_ecomm");

                            if (!$conn) {
                                echo "Connection Failed";
                            }
                            $user_id = $_SESSION['user'];
                            $query1 = "SELECT * FROM cart WHERE user_id = '$user_id' ";
                            $query_run1 = mysqli_query($conn, $query1);

                            if(mysqli_num_rows($query_run1) > 0) {
                                echo "
                                    <form method='post' action='sales.php' id='payment_form'>
                                      <a class='btn btn-danger btn-lg' href='sales.php?pay=".uniqid()."'>Place Order</a>
                                    </form>
                                ";
                            }
                        }
                        else{
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

});

function getDetails(){
    $.ajax({
        type: 'POST',
        url: 'cart_details.php',
        dataType: 'json',
        success: function(response){
            $('#tbody').html(response);
            getCart();
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
