<?php include 'includes/session.php'; ?>
<?php
if(!isset($_SESSION['user'])){
    header('location: index.php');
    exit();
}
?>
<?php include 'includes/header.php'; ?>
<script src="js/sweetalert.min.js"></script>
<body class="hold-transition skin-blue layout-top-nav">
<div class="wrapper">

    <?php include 'includes/navbar.php'; ?>

    <div class="content-wrapper">
        <div class="container">

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
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
                       <div class="box box-solid" style="border-radius: 10px;">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-xs-12 col-sm-2 text-center">
                                        <img src="<?php echo (!empty($user['photo'])) ? 'images/'.$user['photo'] : 'images/profile.jpg'; ?>" class="img-responsive center-block" style="max-width: 100%; margin-bottom: 15px;">
                                    </div>
                                    <div class="col-xs-12 col-sm-10">
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-3">
                                                <h4>Name:</h4>
                                                <h4>Email:</h4>
                                                <h4>Contact No:</h4>
                                                <h4>Address:</h4>
                                                <h4>Purok:</h4>
                                                <h4>Member Since:</h4>
                                            </div>
                                            <div class="col-xs-12 col-sm-9">
                                                <h4><?php echo $user['firstname'].' '.$user['lastname']; ?>
                                                    <span class="pull-right">
                                                        <a href="#edit" class="btn btn-success btn-flat btn-sm" style="border-radius: 8px;" data-toggle="modal"><i class="fa fa-edit"></i> Edit</a>
                                                    </span>
                                                </h4>
                                                <h4><?php echo $user['email']; ?></h4>
                                                <h4><?php echo (!empty($user['contact_info'])) ? $user['contact_info'] : 'N/A'; ?></h4>
                                                <h4><?php echo (!empty($user['address'])) ? $user['address'] : 'N/A'; ?></h4>
                                                <h4><?php echo (!empty($user['address2'])) ? $user['address2'] : 'N/A'; ?></h4>
                                                <h4><?php echo date('M d, Y', strtotime($user['created_on'])); ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box box-solid" style="border-radius: 10px;">
                            <div class="box-header with-border">
                                <h4 class="box-title"><i class="fa fa-calendar"></i> <b>Transaction History</b></h4>
                            </div>
                            <div class="box-body table-responsive">
                                <table class="table table-bordered" id="example1" style="border-radius: 10px;">
                                    <thead>
                                        <th class="hidden"></th>
                                        <th>Date</th>
                                        <th>Transaction #</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Full Details</th>
                                        <th>Action</th>
                                        <th>Trace</th>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $conn = $pdo->open();
                                    try {
                                        $stmt = $conn->prepare("SELECT * FROM sales WHERE user_id=:user_id ORDER BY id DESC");
                                        $stmt->execute(['user_id' => $user['id']]);
                                        foreach ($stmt as $row) {
                                            $stmt2 = $conn->prepare("SELECT * FROM details LEFT JOIN products ON products.id=details.product_id WHERE sales_id=:id");
                                            $stmt2->execute(['id' => $row['id']]);
                                            $total = 0;
                                            foreach ($stmt2 as $row2) {
                                                $subtotal = ($row2['price'] * $row2['quantity']) + $row2['shipping'];
                                                $total += $subtotal;
                                            }
                                            ?>
                                            <tr>
                                                <td class='hidden'></td>
                                                <td><?php echo date('M d, Y', strtotime($row['sales_date']))?> </td>
                                                <td><?php echo $row['pay_id']?></td>
                                                <td>&#8369; <?php echo number_format($total, 2)?></td>
                                                <td>
                                                    <?php if ($row['status'] == 0) { ?>
                                                        <p class="btn-sm btn-warning text-center"><i class='fa fa-spinner'></i> Pending</p>
                                                    <?php } ?>
                                                    <?php if ($row['status'] == 1) { ?>
                                                        <p class="btn-sm text-center" style="background-color: #39FF14; color: #000;"><i class='fa fa-flag'></i> Accepted</p>
                                                    <?php } ?>
                                                    <?php if ($row['status'] == 2) { ?>
                                                        <p class="btn-sm btn-primary text-center"><i class='fa fa-bicycle'></i> On Delivery</p>
                                                    <?php } ?>
                                                    <?php if ($row['status'] == 3) { ?>
                                                        <p class="btn-sm btn-success text-center"><i class='fa fa-thumbs-up'></i> Received</p>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <center><button class='btn btn-sm btn-flat btn-info transact' style='border-radius: 5px;' data-id="<?php echo $row['id']?>"><i class='fa fa-search'></i> View</button></center>
                                                </td>
                                                <td>
                                                    <?php if ($row['status'] == 0) { ?>
                                                        <button class='btn btn-sm btn-flat btn-danger delete-transaction' style='border-radius: 5px;' data-id="<?php echo $row['id']?>"> Cancel Order</button>
                                                    <?php } ?>
                                                    <?php if ($row['status'] == 2) { ?>
                                                        <button class='btn btn-sm btn-flat btn-danger return-transaction' style='border-radius: 5px;' data-id="<?php echo $row['id']?>"><i class='fa fa-times'></i> Return Order</button>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <?php if ($row['status'] == 2) { ?>
                                                    <button class='btn btn-sm btn-flat btn-info trace' style='border-radius: 5px;'  data-id="<?php echo $row['id']?>"><i class='fa fa-truck'></i> Trace Order</button></center>
                                                    <?php } ?>       
                                                </td>
                                            </tr>
                                            <?php
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
                    </div>
                </div>
            </section>

        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <?php include 'includes/profile_modal.php'; ?>
    <?php include 'includes/trace_modal.php'; // Ensure this file contains the modal definition ?>
</div>

<?php include 'includes/scripts.php'; ?>
<script>
$(document).on('click', '.transact', function(e){
    e.preventDefault();
    var id = $(this).data('id');
    $.ajax({
        type: 'POST',
        url: 'transaction.php',
        data: {id: id},
        dataType: 'json',
        success:function(response){
            $('#date').html(response.date);
            $('#transid').html(response.transaction);
            $('#detail').html(response.list); 
            $('#total').html(response.total);
            $('#transaction').modal('show');
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText); 
        }
    });
});

$(document).on('click', '.trace', function(e){
    e.preventDefault();
    var id = $(this).data('id');
    $.ajax({
        type: 'POST',
        url: 'trace_transact.php',
        data: {id: id},
        dataType: 'json',
        success: function(response){
            $('#trace_date').html(response.date);
            $('#trace_transid').html(response.transaction);
            $('#trace_detail').html(response.list);
            $('#trace_total').html(response.total);
            $('#trace_status').html(response.status);
            $('#trace_distance').html(response.distance + ' km');
            $('#trace_time').html(response.time + ' hours');
            $('#trace_address').html(response.address);

            var imageContainer = $('#trace_images');
            imageContainer.empty();
            response.images.forEach(function(imageUrl) {
                imageContainer.append('<img src="' + imageUrl + '" alt="Product Image" style="width: 100px; height: 100px; margin: 5px;">');
            });
            
            $('#traceModal').modal('show');
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
});

$(document).on('click', '.delete-transaction', function(e){
    e.preventDefault();
    var id = $(this).data('id');
    swal({
        title: "Are you sure you want to delete this transaction?",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
    .then((willDelete) => {
        if (willDelete) {
            $.ajax({
                type: 'POST',
                url: 'delete_transaction.php',
                data: {id: id},
                dataType: 'json',
                success: function(response){
                    if(response.success){
                        swal("Transaction deleted successfully!", {
                            icon: "success",
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        swal("Error deleting transaction!", {
                            icon: "error",
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }
    });
});

$(document).on('click', '.return-transaction', function(e){
    e.preventDefault();
    var id = $(this).data('id');
    swal({
        title: "Are you sure you want to return this item? But You Will Pay The Shipping",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
    .then((willReturn) => {
        if (willReturn) {
            $.ajax({
                type: 'POST',
                url: 'return_transaction.php',
                data: {id: id},
                dataType: 'json',
                success: function(response){
                    if(response.success){
                        swal("Transaction returned successfully!", {
                            icon: "success",
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        swal("Error returning transaction!", {
                            icon: "error",
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }
    });
});
</script>
</body>
</html>
