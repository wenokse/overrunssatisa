<?php include 'includes/session.php'; ?>
<?php
if(!isset($_SESSION['user'])){
    header('location: index');
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
                                <table class="table table-bordered" id="orderHistoryTable" style="border-radius: 10px;">
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
                                        $stmt = $conn->prepare("SELECT * FROM sales WHERE user_id=:user_id AND status IN (0, 1, 2, 3) ORDER BY id DESC");
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
                                                <td><?php echo date('M d, Y', strtotime($row['sales_date']))?></td>
                                                <td><?php echo $row['pay_id']?></td>
                                                <td>&#8369; <?php echo number_format($total, 2)?></td>
                                                <td>
                                                    <?php if ($row['status'] == 0) { ?>
                                                        <p class="btn-sm btn-warning text-center"><i class='fa fa-hourglass-half'></i> Pending</p>
                                                    <?php } ?>
                                                    <?php if ($row['status'] == 1) { ?>
                                                        <p class="btn-sm text-center" style="background-color: #FFB300; color: #FFF;"><i class='fa fa-gift'></i> Packaging</p>
                                                    <?php } ?>
                                                    <?php if ($row['status'] == 2) { ?>
                                                        <p class="btn-sm" style="background-color: #1E88E5; color: #FFF; text-align: center;"><i class='fa fa-arrow-right'></i> Pickup Order</p>
                                                    <?php } ?>
                                                    <?php if ($row['status'] == 3) { ?>
                                                        <p class="btn-sm" style="background-color: #FF5722; color: #FFF; text-align: center;"><i class='fa fa-truck'></i> On Delivery</p>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <center><button class='btn btn-sm btn-flat btn-info transact' style='border-radius: 5px;' data-id="<?php echo $row['id']?>"><i class='fa fa-search'></i> View</button></center>
                                                </td>
                                                <td>
                                                    <?php if ($row['status'] == 0) { ?>
                                                        <button class='btn btn-sm btn-flat btn-danger delete-transaction' style='border-radius: 5px;' data-id="<?php echo $row['id']?>"> Cancel Order</button>
                                                    <?php } ?>
                                                    <?php if ($row['status'] == 1) { ?>
                                                        <button class='btn btn-sm btn-flat btn-danger delete-transaction' style='border-radius: 5px;' data-id="<?php echo $row['id']?>"> Cancel Order</button>
                                                    <?php } ?>
                                                    <?php if ($row['status'] == 3) { ?>
                                                        <button class='btn btn-sm btn-flat btn-danger return-transaction' style='border-radius: 5px;' data-id="<?php echo $row['id']?>"><i class='fa fa-times'></i> Return Order</button>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <?php if ($row['status'] == 3) { ?>
                                                        <button class='btn btn-sm btn-flat btn-info trace' style='border-radius: 5px;' data-id="<?php echo $row['id']?>"><i class='fa fa-truck'></i> Trace Order</button>
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
                        <div class="box box-solid" style="border-radius: 10px;">
                            <div class="box-header with-border">
                                <h4 class="box-title"><i class="fa fa-calendar"></i> <b>Order History</b></h4>
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
                                    </thead>
                                    <tbody>
                                    <?php
                                    $conn = $pdo->open();
                                    try {
                                        $stmt = $conn->prepare("SELECT * FROM sales WHERE user_id=:user_id AND status=4 ORDER BY id DESC");
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
                                                <td><?php echo date('M d, Y', strtotime($row['sales_date']))?></td>
                                                <td><?php echo $row['pay_id']?></td>
                                                <td>&#8369; <?php echo number_format($total, 2)?></td>
                                                <td>
                                                    <p class="btn-sm btn-success text-center"><i class='fa fa-check-circle'></i> Received</p>
                                                </td>
                                                <td>
                                                    <center><button class='btn btn-sm btn-flat btn-info transact' style='border-radius: 5px;' data-id="<?php echo $row['id']?>"><i class='fa fa-search'></i> View</button></center>
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
    <?php include 'includes/trace_modal.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>
<script>
$(document).ready(function() {
    $('#orderHistoryTable').DataTable({
        "responsive": true,
        "order": [[1, 'desc']], // Sort by date column descending
        "pageLength": 10, // Show 10 entries per page
        "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
        "language": {
            "search": "Search transactions:",
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ transactions",
            "emptyTable": "No transactions found",
            "zeroRecords": "No matching transactions found"
        },
        "columnDefs": [
            {
                "targets": [0], // Hidden ID column
                "visible": false,
                "searchable": false
            },
            {
                "targets": [5, 6, 7], // Action and Trace columns
                "orderable": false,
                "searchable": false
            }
        ]
    });
});
</script>
<script>
$(document).on('click', '.transact', function(e){
    e.preventDefault();
    var id = $(this).data('id');
    $.ajax({
        type: 'POST',
        url: 'transaction',
        data: {id: id},
        dataType: 'json',
        success:function(response){
            $('#date').html(response.date);
            $('#transid').html(response.transaction);
            $('#detail').html(response.list);
            $('#total').html(response.total);
            
            // Add recipient details
            $('#recipient').html(response.recipient);
            $('#delivery_address').html(response.delivery_address);
            $('#address2').html(response.address2);
            $('#address3').html(response.address3);
            // rider
            $('#rider_name').html(response.rider_name);
            $('#phone_number').html(response.phone_number);
            $('#rider_address').html(response.rider_address);
           
            $('#transaction').modal('show');
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
});
$(document).on('click', '.cancel-product', function(e){
    e.preventDefault();
    
    var detail_id = $(this).data('id');  // Get detail ID from button

    swal({
        title: "Are you sure?",
        text: "You will not be able to recover this product!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
    .then((willDelete) => {
        if (willDelete) {
            // Send AJAX request to cancel the product
            $.ajax({
                type: 'POST',
                url: 'cancel_product',  // PHP file to handle cancellation
                data: {detail_id: detail_id},
                dataType: 'json',
                success: function(response){
                    if(response.success){
                        swal("Product has been canceled successfully!", {
                            icon: "success",
                        }).then(() => {
                            location.reload();  // Reload the page after cancellation
                        });
                    } else {
                        swal("Cancellation failed!", {
                            icon: "error",
                        });
                    }
                },
                error: function(xhr, status, error){
                    console.log(xhr.responseText);
                }
            });
        }
    });
});

$(document).on('click', '.trace', function(e){
    e.preventDefault();
    var id = $(this).data('id');
    $.ajax({
        type: 'POST',
        url: 'trace_transact',
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
            
            // Update address information
            $('#trace_recipient').html(response.recipient_name);
            $('#trace_phone').html(response.phone);
            $('#trace_address').html(response.address);
            $('#trace_address2').html(response.address2);
            $('#trace_address3').html(response.address3);

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
                url: 'delete_transaction',
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
                url: 'return_transaction',
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
<script>
$(document).ready(function() {
    // Check for unrated products immediately when page loads
    checkUnratedProductsOnLoad();
    
    function checkUnratedProductsOnLoad() {
        $.ajax({
            type: 'POST',
            url: 'check_unrated_products_onload',
            dataType: 'json',
            success: function(response) {
                if(response.unrated_products && response.unrated_products.length > 0) {
                    showRatingModal(response.unrated_products[0]);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }

    function showRatingModal(product) {
        $('#product-id').val(product.product_id);
        $('#detail-id').val(product.detail_id);
        $('#sales-id').val(product.sales_id);
        $('.product-info').html('<h4>' + product.name + '</h4>');
        $('#selected-rating').val(0);
        $('.rating-stars .star').removeClass('selected');
        $('#ratingModal').modal('show');
    }

    // Handle star rating selection
    $('.rating-stars .star').hover(
        function() {
            var rating = $(this).data('rating');
            highlightStars(rating);
        },
        function() {
            var currentRating = $('#selected-rating').val();
            highlightStars(currentRating);
        }
    ).click(function() {
        var rating = $(this).data('rating');
        $('#selected-rating').val(rating);
        highlightStars(rating);
    });

    function highlightStars(rating) {
        $('.rating-stars .star').each(function() {
            if($(this).data('rating') <= rating) {
                $(this).removeClass('fa-star-o').addClass('fa-star selected');
            } else {
                $(this).removeClass('fa-star selected').addClass('fa-star-o');
            }
        });
    }

    $('#submit-rating').click(function() {
    var rating = $('#selected-rating').val();
    var comment = $('#product-comment').val();
    var productId = $('#product-id').val();
    var detailId = $('#detail-id').val();
    var salesId = $('#sales-id').val();

    if(rating == 0) {
        swal("Please select a rating", "", "warning");
        return;
    }

    // Create FormData object to handle file uploads
    var formData = new FormData();
    formData.append('product_id', productId);
    formData.append('rating', rating);
    formData.append('comment', comment);
    formData.append('detail_id', detailId);
    formData.append('sales_id', salesId);

    // Append each file to FormData
    var fileInput = document.getElementById('file-upload');
    var files = fileInput.files;
    for(var i = 0; i < files.length; i++) {
        formData.append('attachments[]', files[i]);
    }

    $.ajax({
        type: 'POST',
        url: 'submit_rating',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                $('#ratingModal').modal('hide');
                swal("Thank you for your rating!", "", "success").then(function() {
                    // Check if there are more unrated products
                    checkUnratedProductsOnLoad();
                });
            } else {
                swal("Error submitting rating", "", "error");
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            swal("Error submitting rating", "Please try again", "error");
        }
    });
});

// Handle file upload preview
$('#file-upload').on('change', function(e) {
    const maxFileSize = 10 * 1024 * 1024; // 10MB
    const maxFiles = 5;
    const files = Array.from(e.target.files);
    const previewContainer = $('#attachment-preview');
    
    // Clear previous previews
    previewContainer.empty();
    
    // Validate number of files
    if(files.length > maxFiles) {
        swal("Too many files", `Maximum ${maxFiles} files allowed`, "warning");
        this.value = '';
        return;
    }
    
    files.forEach(file => {
        // Validate file type
        if(!file.type.match(/^(image\/(jpeg|jpg|png)|video\/.*)/)) {
            swal("Invalid file type", "Please upload only images (JPG, JPEG, PNG) or videos", "warning");
            return;
        }
        
        // Validate file size
        if(file.size > maxFileSize) {
            swal("File too large", `Maximum file size is ${maxFileSize/1024/1024}MB`, "warning");
            return;
        }
        
        // Create preview
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewItem = $('<div>').addClass('attachment-item');
            const removeBtn = $('<span>').addClass('remove-attachment').html('×');
            
            if(file.type.startsWith('image/')) {
                const img = $('<img>').attr({
                    src: e.target.result,
                    alt: 'Preview',
                    style: 'max-width: 200px; max-height: 200px;'
                });
                previewItem.append(img);
            } else if(file.type.startsWith('video/')) {
                const video = $('<video>').attr({
                    src: e.target.result,
                    controls: true,
                    style: 'max-width: 200px; max-height: 200px;'
                });
                previewItem.append(video);
            }
            
            previewItem.append(removeBtn);
            previewContainer.append(previewItem);
        };
        reader.readAsDataURL(file);
    });
});

// Handle removal of previewed files
$(document).on('click', '.remove-attachment', function() {
    $(this).parent().remove();
});

    // Handle skip rating
    $('#skip-rating').click(function() {
        var detailId = $('#detail-id').val();
        var salesId = $('#sales-id').val();
        
        $.ajax({
            type: 'POST',
            url: 'skip_rating',
            data: {
                detail_id: detailId,
                sales_id: salesId
            },
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    $('#ratingModal').modal('hide');
                    // Check if there are more unrated products
                    checkUnratedProductsOnLoad();
                }
            }
        });
    });
});
</script>
<script>
$(document).ready(function(){
    // Image modal functionality
    $(document).on('click', '.view-image', function() {
        var imgSrc = $(this).data('img-src');
        var productName = $(this).data('product-name');
        var productColor = $(this).data('product-color');
        
        $('#modalImage').attr('src', imgSrc);
        $('#productName').text(productName);
        $('#productColor').text(productColor ? 'Color: ' + productColor : '');
        $('#imageModal').modal('show');
    });

    // Handle modal image loading
    $('#modalImage').on('error', function() {
        $(this).attr('src', 'images/no-image.jpg'); // Replace with your default image path
    });

    // Optional: Add keyboard navigation for modal
    $(document).keydown(function(e) {
        if ($('#imageModal').hasClass('in')) {
            if (e.keyCode === 27) { // ESC key
                $('#imageModal').modal('hide');
            }
        }
    });
});
</script>
<style>
   .product-img-container {
    width: 80px;
    height: 80px;
    overflow: hidden;
    border-radius: 5px;
    cursor: pointer;
    transition: transform 0.2s ease;
}

.product-img-container:hover {
    transform: scale(1.05);
}

.product-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Modal styles */
#imageModal .modal-body {
    padding: 20px;
    
}
#imageModal .MODAL-IMG {
    background-color: rgba(0, 0, 0, 0.8);
}


#modalImage {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

#productName {
    font-size: 18px;
    margin-bottom: 5px;
}

#productColor {
    color: #666;
    margin-bottom: 15px;
}
.rating-stars {
    padding: 20px 0;
}
.rating-stars .star {
    color: #ddd;
    cursor: pointer;
    padding: 0 5px;
}
.rating-stars .star.selected {
    color: #FFD700;
}
.attachment-preview {
    margin: 10px 0;
    max-width: 100%;
}
.attachment-preview img,
.attachment-preview video {
    max-width: 200px;
    max-height: 200px;
    margin: 5px;
}
.file-input-wrapper {
    margin: 15px 0;
}
.attachment-list {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin: 10px 0;
}
.attachment-item {
    position: relative;
    display: inline-block;
}
.remove-attachment {
    position: absolute;
    top: -8px;
    right: -8px;
    background: red;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    text-align: center;
    line-height: 20px;
    cursor: pointer;
    font-size: 12px;
}
</style>

<!-- HTML -->
<div class="modal fade" id="ratingModal" tabindex="-1" role="dialog" aria-labelledby="ratingModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="ratingModalLabel">Thank you for purchasing!!</h4>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <h3>Rate Product</h3>
                    <div class="product-info mb-3"></div>
                    <div class="rating-stars">
                        <i class="fa fa-star-o fa-2x star" data-rating="1"></i>
                        <i class="fa fa-star-o fa-2x star" data-rating="2"></i>
                        <i class="fa fa-star-o fa-2x star" data-rating="3"></i>
                        <i class="fa fa-star-o fa-2x star" data-rating="4"></i>
                        <i class="fa fa-star-o fa-2x star" data-rating="5"></i>
                    </div>
                    <input type="hidden" id="selected-rating" value="0">
                    <input type="hidden" id="product-id" value="">
                    <input type="hidden" id="detail-id" value="">
                    <input type="hidden" id="sales-id" value="">
                </div>
                <h3>Comment for Product</h3>
                <div class="form-group">
                    <textarea class="form-control" id="product-comment" rows="4" placeholder="Write your comment here..."></textarea>
                </div>
                <div class="file-input-wrapper">
                    <label for="file-upload" class="btn btn-secondary">
                        <i class="fa fa-paperclip"></i> Add Attachments
                    </label>
                    <input type="file" id="file-upload" multiple accept="image/jpeg,image/png,image/jpg,video/*" style="display: none;">
                    <small class="text-muted d-block">Allowed files: JPG, JPEG, PNG, and video files</small>
                </div>
                <div id="attachment-preview" class="attachment-list"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="submit-rating">Submit Rating</button>
                <button type="button" class="btn btn-warning" id="skip-rating">Skip Rating</button>
            </div>
        </div>
    </div>
</div>
</body>
</html>
