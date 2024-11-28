<!-- product -->
<?php include 'includes/firewall.php'; ?>
<?php
include 'includes/session.php'; 

$conn = $pdo->open();

if (!isset($_GET['product'])) {
    exit("Product parameter is missing");
}

$slug = $_GET['product'];

try {
    $stmt = $conn->prepare("
    SELECT p.*, p.name AS prodname, c.name AS catname, p.id AS prodid, 
           CASE WHEN p.user_id = 0 THEN 1 ELSE p.user_id END AS vendor_id,
           u.photo AS vendor_photo, 
           CASE WHEN p.user_id = 0 THEN 'Overruns sa Tisa' ELSE u.store END AS vendor_store,
           c.cat_slug
    FROM products p 
    LEFT JOIN category c ON c.id = p.category_id 
    LEFT JOIN users u ON u.id = CASE WHEN p.user_id = 0 THEN 1 ELSE p.user_id END
    WHERE p.slug = :slug
    ");
    $stmt->execute(['slug' => $slug]);
    $product = $stmt->fetch();
    if (!$product) {
        exit("Product not found");
    }

    $stmt = $conn->prepare("SELECT * FROM product_colors WHERE product_id = :product_id");
    $stmt->execute(['product_id' => $product['prodid']]);
    $color_options = $stmt->fetchAll();
    
    $stmt = $conn->prepare("SELECT AVG(rating) as avg_rating FROM ratings WHERE product_id = :product_id");
    $stmt->execute(['product_id' => $product['prodid']]);
    $avg_rating = $stmt->fetch()['avg_rating'];
    $avg_rating = round($avg_rating, 1);

// Add this after the other queries, before closing the try block
$stmt = $conn->prepare("SELECT DISTINCT size FROM product_sizes WHERE product_id = :product_id ORDER BY size");
$stmt->execute(['product_id' => $product['prodid']]);
$sizes = '';
$size_options = $stmt->fetchAll();

if (!empty($size_options)) {
    $sizes .= "<option value=''>Select Size</option>";
    foreach ($size_options as $size) {
        $sizes .= "<option value='" . htmlspecialchars($size['size']) . "'>" . htmlspecialchars($size['size']) . "</option>";
    }
}

// Add this before the category check in your display section
$stmt = $conn->prepare("SELECT c.name as category_name FROM products p 
                       LEFT JOIN category c ON c.id = p.category_id 
                       WHERE p.id = :product_id");
$stmt->execute(['product_id' => $product['prodid']]);
$category_result = $stmt->fetch();
$category_name = $category_result['category_name'];

} catch (PDOException $e) {
    exit("There is some problem in connection: " . $e->getMessage());
}

$now = date('Y-m-d');
if ($product['date_view'] == $now) {
    $stmt = $conn->prepare("UPDATE products SET counter=counter+1 WHERE id=:id");
    $stmt->execute(['id' => $product['prodid']]);
} else {
    $stmt = $conn->prepare("UPDATE products SET counter=1, date_view=:now WHERE id=:id");
    $stmt->execute(['id' => $product['prodid'], 'now' => $now]);
}

$pdo->close();
?>

<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue layout-top-nav">
    <div id="fb-root"></div>
    <!-- <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v13.0&appId=1346358252525630&autoLogAppEvents=1" nonce="hsdcri7l"></script> -->
    <div class="wrapper">
         <!-- Preloader -->
    <div id="preloader">
        <div class="loader"></div>
    </div>
    <?php 
    if (isset($_SESSION['user'])) {
        include 'includes/navbar.php';
    } else {
        include 'includes/home_navbar.php';
    }
    ?>
        <div class="content-wrapper">
            <div class="container">
                <section class="content">
                    <div class="">
                        <div class="">
                            <div class="callout" id="callout" style="display:none">
                                <button type="button" class="close"><span aria-hidden="true">&times;</span></button>
                                <span class="message"></span>
                            </div>
                            <div class="row">
                                <!-- Left column - Product Image and Controls -->
                                <div class="col-sm-4">
                                    <div class="show">      
                                        <img src="<?php echo (!empty($product['photo'])) ? 'images/'.$product['photo'] : 'images/noimage.jpg'; ?>" 
                                            id="product-img" 
                                            class="zoom img1" 
                                            data-magnify-src="<?php echo (!empty($product['photo'])) ? 'images/large-'.$product['photo'] : 'images/noimage.jpg'; ?>">
                                    </div>
                                
                                    <br>
                                
                                    <?php if (!empty($sizes) && !in_array($category_name, ['bags', 'accessories'])) : ?>
                                    <div class="form-group">
                                        <label for="size">Size :</label>
                                        <select class="form-control input-lg" id="size" name="size" required>
                                            <?php echo $sizes; ?>
                                        </select>
                                    </div>
                                    <?php endif; ?>

                                    <br>
                                    <div class="form-group">
                                        <label for="color">Color:</label>
                                        <div id="color-buttons" class="color-buttons-container">
                                            <button type="button" 
                                                class="btn btn-default color-btn active"
                                                data-photo="<?php echo $product['photo']; ?>"
                                                data-color="default"
                                                style="background: linear-gradient(45deg, #f3f3f3 50%, #ddd 50%);
                                                    width: 30px; 
                                                    height: 30px; 
                                                    margin-right: 5px;">
                                            </button>
                                            <?php foreach ($color_options as $color): ?>
                                                <button type="button" 
                                                    class="btn btn-default color-btn" 
                                                    data-color="<?php echo htmlspecialchars($color['color']); ?>"
                                                    data-photo="<?php echo htmlspecialchars($color['photo']); ?>"
                                                    style="background-color: <?php echo htmlspecialchars($color['color']); ?>; 
                                                        width: 30px; 
                                                        height: 30px; 
                                                        margin-right: 5px; 
                                                        border-radius: 50%;">
                                                </button>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>

                                    <br>
                                    <form class="form-inline" id="productForm" method="post" action="cart_add">
                                        <div class="input-group col-sm-6">
                                            <span class="input-group-btn">
                                                <button type="button" id="minus" class="btn btn-default btn-flat btn-lg"><i class="fa fa-minus"></i></button>
                                            </span>
                                            <input type="text" readonly name="quantity" id="quantity" class="form-control input-lg" value="1" data-stock="<?php echo $product['stock'] ?>">
                                            <span class="input-group-btn">
                                                <button type="button" id="add" class="btn btn-default btn-flat btn-lg"><i class="fa fa-plus"></i></button>
                                            </span>
                                            <input type="hidden" value="<?php echo $product['vendor_id']; ?>" name="vendor_id">
                                            <input type="hidden" value="<?php echo $product['prodid']; ?>" name="id">
                                            <input type="hidden" id="selected_size" name="selected_size">
                                            <input type="hidden" id="selected_color" name="selected_color">
                                            <input type="hidden" value="<?php echo $shipping; ?>" id="shipping" name="shipping">
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-lg btn-flat" style="border-radius: 15px;" <?php if($product['stock'] == '0'){echo "disabled";}?>>
                                            <i class="fa fa-shopping-cart"></i> Add to Cart
                                        </button>
                                    </form>
                                </div>

                                <!-- Middle column - Product Details -->
                                <div class="col-sm-5">
                                    <div class="vendor-info">
                                        <a href="shop?id=<?php echo $product['vendor_id']; ?>" style="color: black;">
                                            <img src="<?php echo (!empty($product['vendor_photo'])) ? 'images/'.$product['vendor_photo'] : 'images/noimage.jpg'; ?>" 
                                                style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                                            <strong><?php echo $product['vendor_store']; ?></strong>
                                        </a>
                                    </div>
                                    <h1 class="page-header"><b><?php echo $product['prodname']; ?></b></h1>
                                    <h3><b>&#8369; <span id="price"><?php echo number_format($product['price'], 2); ?> per.</span></b></h3>
                                    
                                    <p><b>Stock:</b> 
                                        <span id="stock">
                                            <?php 
                                            if ($product['stock'] == '0') {
                                                echo '<span style="color: red;">Out of Stock</span>';
                                            } else {
                                                echo $product['stock'];
                                            }
                                            ?>
                                        </span>
                                    </p>

                                    <p><b>Category:</b> <a href="category?category=<?php echo $product['cat_slug']; ?>"><?php echo $product['catname']; ?></a></p>
                                    <p><b>Description:</b></p>
                                    <p1><?php echo $product['description']; ?></p1>

                                    <p><b>Average Rating:</b> <?php echo $avg_rating; ?> / 5</p>
                                    <div class='star-rating' data-product-id='<?php echo $product['prodid']; ?>'>
                                        <?php
                                        $filled_stars = round($avg_rating);
                                        for ($i = 5; $i >= 1; $i--) {
                                            if ($i <= $filled_stars) {
                                                echo "<span class='star' data-rating='$i'>&#9733;</span>"; 
                                            } else {
                                                echo "<span class='star' data-rating='$i'>&#9734;</span>"; 
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>

                                <!-- Right column - Related Products -->
                                <div class="col-sm-3">
                                    <h3 class="page-header">Related Products</h3>
                                    <?php
                                    function getStarRating($rating) {
                                        $fullStars = floor($rating);
                                        $halfStar = $rating - $fullStars >= 0.5 ? 1 : 0;
                                        $emptyStars = 5 - $fullStars - $halfStar;
                                    
                                        $starsHTML = str_repeat('<i class="fa fa-star-o"></i>', $emptyStars);
                                        if ($halfStar) {
                                            $starsHTML .= '<i class="fa fa-star-half-o"></i>';
                                        }
                                        $starsHTML .= str_repeat('<i class="fa fa-star"></i>', $fullStars);
                                    
                                        return $starsHTML;
                                    }
                                    
                                    $stmt1 = $conn->prepare("SELECT * FROM products WHERE category_id = :catid AND id != :prodid LIMIT 2");
                                    $stmt1->execute(['catid' => $product['category_id'], 'prodid' => $product['id']]);
                                    $related_products = $stmt1->fetchAll();

                                    foreach ($related_products as $related_product) {
                                        $stmt2 = $conn->prepare("SELECT products.*, AVG(ratings.rating) AS average_rating FROM products LEFT JOIN ratings ON products.id = ratings.product_id WHERE products.id = :product_id GROUP BY products.id");
                                        $stmt2->execute(['product_id' => $related_product['id']]);
                                        $product_with_rating = $stmt2->fetch();

                                        $image = (!empty($related_product['photo'])) ? 'images/'.$related_product['photo'] : 'images/noimage.jpg';
                                        echo "
                                        <a href='product?product=".$related_product['slug']."'>
                                            <div class='box box-solid'>
                                                <div class='box-body prod-body'>
                                                    <img src='$image' width='100%' height='230px' class='thumbnail'>
                                                    <h5><a href='product?product=".$related_product['slug']."'>".$related_product['name']."</a></h5>
                                                </div>
                                                <div class='box-footer'>
                                                    <b>&#8369; ".number_format($related_product['price'], 2)."</b>
                                                    <div>Rating: ".getStarRating($product_with_rating['average_rating'])."</div>
                                                </div>
                                            </div>
                                        </a>
                                        ";
                                    }
                                    ?>
                                </div>
                            </div>
                           
                            <div id="reviews-section" class="reviews-container">
                                        <h3>Customer Reviews</h3>
                                        <div id="reviews-list"></div>
                                    </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <?php include 'includes/footer.php'; ?>
    </div>
    <?php include 'includes/scripts.php'; ?>
    <script>
$(document).ready(function() {
    // Initialize reviews
    loadReviews();

    // Handle like/dislike buttons
    $(document).on('click', '.like-btn, .dislike-btn', function(e) {
        e.preventDefault();
        
        if(!isLoggedIn()) {
            alert('Please login to like or dislike reviews');
            return;
        }
        
        const commentId = $(this).data('comment-id');
        const action = $(this).hasClass('like-btn') ? 'like' : 'dislike';
        
        handleReviewAction(commentId, action);
    });
});

function isLoggedIn() {
    return <?php echo isset($_SESSION['user']) ? 'true' : 'false'; ?>;
}

function loadReviews() {
    const productId = <?php echo json_encode($product['prodid']); ?>;
    
    $.ajax({
        url: 'fetch_reviews',
        type: 'GET',
        data: { product_id: productId },
        success: function(response) {
            if(response.success) {
                $('#reviews-list').html(response.reviews);
            } else {
                console.error('Error loading reviews:', response.error);
                $('#reviews-list').html('<div class="alert alert-danger">Error loading reviews</div>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Ajax error:', error);
            $('#reviews-list').html('<div class="alert alert-danger">Error loading reviews</div>');
        }
    });
}

function handleReviewAction(commentId, action) {
    $.ajax({
        url: 'like_comment',
        type: 'POST',
        data: {
            review_id: commentId,
            action: action
        },
        success: function(response) {
            if(response.success) {
                loadReviews(); // Reload reviews to show updated counts
            } else {
                alert(response.error || 'An error occurred while processing your request');
            }
        },
        error: function(xhr, status, error) {
            console.error('Ajax error:', error);
            alert('An error occurred while processing your request');
        }
    });
}

function viewAttachment(path) {
    if(path) {
        window.open(path, '_blank');
    }
}
</script>



    <script>
        $(document).ready(function() {
    // Initial validation setup
    const categoryName = '<?php echo $category_name; ?>';
    const hasSizes = <?php echo !empty($sizes) ? 'true' : 'false'; ?>;
    const requireSize = hasSizes && !['bags', 'accessories'].includes(categoryName);
    const hasColors = <?php echo !empty($color_options) ? 'true' : 'false'; ?>;
    const requireColor = hasColors; // Color selection is required if colors are available
    
    // Disable Add to Cart button initially if size or color selection is required
    if (requireSize || requireColor) {
        $('button[type="submit"]').prop('disabled', true);
    }

    // Form submission handling
    $('#productForm').on('submit', function(e) {
        e.preventDefault();
        
        // Get selected values
        const selectedSize = $('#size').val();
        const selectedColor = $('#selected_color').val();
        const stock = $('#quantity').data('stock');
        
        // Build error message if selections are missing
        let errorMessage = '';
        
        if (requireSize && !selectedSize) {
            errorMessage += 'Please select a size\n';
        }
        
        if (requireColor && !selectedColor) {
            errorMessage += errorMessage ? '\n' : '';
            errorMessage += 'Please select a color';
        }
        
        // Show combined error message if any validations failed
        if (errorMessage) {
            swal({
                title: 'Required Selections',
                text: errorMessage,
                icon: 'warning',
                buttons: {
                    confirm: {
                        text: "OK",
                        value: true,
                        visible: true,
                        className: "btn btn-primary",
                        closeModal: true
                    }
                }
            });
            return false;
        }
        
        // Check stock
        if (stock === '0') {
            swal({
                title: 'Out of Stock',
                text: 'Sorry, this product is currently out of stock',
                icon: 'error',
                buttons: {
                    confirm: {
                        text: "OK",
                        value: true,
                        visible: true,
                        className: "btn btn-primary",
                        closeModal: true
                    }
                }
            });
            return false;
        }

        // If all validations pass, confirm the selection
        swal({
            title: 'Confirm Selection',
            text: `Add to cart with:\n${requireSize ? 'Size: ' + selectedSize + '\n' : ''}Color: ${selectedColor}`,
            icon: 'info',
            buttons: {
                cancel: {
                    text: "Cancel",
                    value: null,
                    visible: true,
                    className: "btn btn-default",
                    closeModal: true
                },
                confirm: {
                    text: "Add to Cart",
                    value: true,
                    visible: true,
                    className: "btn btn-primary",
                    closeModal: true
                }
            }
        }).then((willAdd) => {
            if (willAdd) {
                this.submit();
            }
        });
    });

    // Enable/disable submit button based on size selection
    $('#size').change(function() {
        const selectedSize = $(this).val();
        if (requireSize) {
            updateSubmitButton(selectedSize, $('#selected_color').val());
        }
        $('#selected_size').val(selectedSize);
    });

    // Update hidden color input and button state when color is selected
    $('.color-btn').click(function() {
        const selectedColor = $(this).data('color');
        $('#selected_color').val(selectedColor);
        
        // Update button state
        const sizeSelected = requireSize ? $('#size').val() !== '' : true;
        updateSubmitButton(sizeSelected, selectedColor);
    });

    // Helper function to update submit button state
    function updateSubmitButton(sizeSelected, colorSelected) {
        const sizeValid = requireSize ? sizeSelected : true;
        const colorValid = requireColor ? colorSelected : true;
        const stock = $('#quantity').data('stock');
        
        $('button[type="submit"]').prop('disabled', 
            !(sizeValid && colorValid) || stock === '0'
        );
    }
});
$(document).ready(function() {
    // Handle color button clicks
    $('.color-btn').click(function() {
        // Remove active class from all buttons
        $('.color-btn').removeClass('active');
        
        // Add active class to clicked button
        $(this).addClass('active');
        
        // Get color and photo data
        var selectedColor = $(this).data('color');
        var selectedPhoto = $(this).data('photo');
        
        // Update hidden input with selected color
        $('#selected_color').val(selectedColor);
        
        // Fade out current image
        $('#product-img').css('opacity', '0.5');
        
        // Update image source with small delay for transition effect
        setTimeout(function() {
            // Determine correct path based on whether it's default or color photo
            var imagePath = selectedColor === 'default' 
                ? 'images/' + selectedPhoto 
                : 'images/colors/' + selectedPhoto;
                
            // Update main image
            $('#product-img').attr('src', imagePath)
                           .on('load', function() {
                               $(this).css('opacity', '1');
                           });
            
            // Update magnify source if using image zoom
            var largePath = selectedColor === 'default'
                ? 'images/large-' + selectedPhoto
                : 'images/colors/large-' + selectedPhoto;
            $('#product-img').attr('data-magnify-src', largePath);
        }, 200);
    });

    // Initialize magnify/zoom if you're using it
    if(typeof $('.zoom').magnify === 'function') {
        $('.zoom').magnify({
            speed: 200,
            src: $('.zoom').attr('data-magnify-src')
        });
    }
});
</script>

<!-- Add this to your head section or after jQuery is loaded -->
<script>
// Preload images for smooth transitions
$(window).on('load', function() {
    $('.color-btn').each(function() {
        var photo = $(this).data('photo');
        var color = $(this).data('color');
        if(photo) {
            var img = new Image();
            img.src = color === 'default' 
                ? 'images/' + photo 
                : 'images/colors/' + photo;
        }
    });
});
</script>

    <script>
       

        $(function(){
            $('#add').click(function(e){
                e.preventDefault();
                var quantity = $('#quantity').val();
                quantity++;
                const stock = $('#quantity').data('stock');
                if (stock >= quantity) {
                    $('#quantity').val(quantity);
                }
            });
            $('#minus').click(function(e){
                e.preventDefault();
                var quantity = $('#quantity').val();
                if (quantity > 1) {
                    quantity--;
                }
                $('#quantity').val(quantity);
            });

            $('#size').change(function() {
                var selectedSize = $(this).val();
                $('#selected_size').val(selectedSize);
            });

            $('#color').change(function() {
                var selectedColor = $(this).val();
                $('#selected_color').val(selectedColor);
            });

            // Star rating
            const stars = document.querySelectorAll('.star-rating .star');
            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const rating = this.dataset.rating;
                    const productId = this.closest('.star-rating').dataset.productId;
                    fetch('ratess_product', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ rating: rating, product_id: productId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            updateStars(productId, rating);
                            swal('Success', 'Thank you for rating our products', 'success');
                        } else {
                            swal('Error', 'Something went wrong. Please try again.', 'error');
                        }
                    })
                    .catch(error => console.error('Error:', error));
                });
            });
           


            function updateStars(productId, rating) {
                const starRatingDiv = document.querySelector(`.star-rating[data-product-id='${productId}']`);
                const stars = starRatingDiv.querySelectorAll('.star');
                stars.forEach(star => {
                    if (star.dataset.rating <= rating) {
                        star.innerHTML = '&#9733;';
                    } else {
                        star.innerHTML = '&#9734;';
                    }
                });
            }
        });

        $(function() {
    $('#comment_form').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: 'submit_comment',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    swal({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message
                    }).then(function() {
                        $('#comment_form')[0].reset();  // Reset the form after submission
                        loadComments();  // Refresh the comments
                    });
                } else {
                    handleErrorResponse(response);  // Handle different error scenarios
                }
            }
        });
    });

    // Function to load the comments without page refresh
    function loadComments() {
        $.ajax({
            url: 'fetch_comments',
            method: 'GET',
            data: { product_id: <?php echo $product['prodid']; ?> },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#comment_list').html(response.comments);  // Display updated comments
                } else {
                   
                }
            },
            error: function() {
                $('#comment_list').html('<p>An error occurred while loading comments.</p>');
            }
        });
    }

    // Call loadComments when the page is loaded
    loadComments();

    

    $(document).on('click', '.view-replies-link', function(e) {
        e.preventDefault();
        var $this = $(this);
        var commentId = $this.data('comment-id');
        var replyCount = $this.data('reply-count');
        var repliesContainer = $('#replies-' + commentId);

        if (repliesContainer.is(':visible')) {
            repliesContainer.hide();
            $this.text('View ' + replyCount + ' ' + (replyCount === 1 ? 'reply' : 'replies'));
        } else {
            repliesContainer.show();
            $this.text('Hide replies');
        }
    });

    $(document).on('click', '.reply-btn', function() {
        var commentId = $(this).data('comment-id');
        $(this).closest('.comment-container').find('.reply-form').toggle();
    });

    $(document).on('click', '.submit-reply', function() {
        var parentId = $(this).data('parent-id');
        var replyText = $(this).siblings('.reply-text').val();
        
        $.ajax({
            url: 'submit_reply',
            method: 'POST',
            data: {
                parent_id: parentId,
                reply: replyText,
                product_id: <?php echo $product['prodid']; ?>
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    swal({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message
                    });
                    loadComments();  // Refresh comments after reply submission
                } else {
                    swal({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message
                    });
                }
            }
        });
    });

    

    function handleErrorResponse(response) {
        if (response.redirect === true) {
            swal({
                icon: 'error',
                title: 'Invalid Comment',
                text: response.message,
            }).then(() => {
                window.location.reload();  // Reload page on error
            });
        } else if (response.redirect) {
            swal({
                icon: 'error',
                title: 'Login Required',
                text: response.message,
            }).then(() => {
                window.location.href = response.redirect;  // Redirect to login
            });
        } else {
            swal({
                icon: 'error',
                title: 'Error!',
                text: response.message
            });
        }
    }
});

// Preloader script
window.addEventListener('load', function() {
    var preloader = document.getElementById('preloader');
    preloader.style.display = 'none';
});

    </script>
    <style>
.reviews-container {
    max-width: 500px;
    margin: 20px auto;
    padding: 20px;
}

.review-box {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    padding: 20px;
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.user-info {
    display: flex;
    align-items: center;
}

.user-photo {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    margin-right: 15px;
    object-fit: cover;
}

.user-details h4 {
    margin: 0;
    font-size: 16px;
    color: #333;
}

.rating {
    margin: 5px 0;
}

.review-date {
    color: #666;
    font-size: 14px;
}

.review-content {
    margin: 15px 0;
}

.review-text {
    font-size: 15px;
    line-height: 1.6;
    color: #444;
}

.review-attachment {
    margin: 15px 0;
}

.attachment-preview {
    max-width: 400px;
    max-height: 400px;
    border-radius: 4px;
    cursor: pointer;
}

.video-preview {
    max-width: 300%;
    max-height: 400px;
    border-radius: 4px;
}

.review-footer {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.like-btn, .dislike-btn {
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 5px 10px;
    border: 1px solid #ddd;
    background: #f8f9fa;
    color: #666;
}

.like-btn.active, .dislike-btn.active {
    background: #e3f2fd;
    color: #1976d2;
}

.no-reviews {
    text-align: center;
    padding: 20px;
    color: #666;
    font-style: italic;
}

@media (max-width: 576px) {
    .review-box {
        padding: 15px;
    }
    
    .user-photo {
        width: 40px;
        height: 40px;
    }
    
    .user-details h4 {
        font-size: 14px;
    }
    
    .review-text {
        font-size: 14px;
    }
}
</style>
<style>
.color-buttons-container {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 10px;
}

.color-btn {
    border: 2px solid #ddd;
    border-radius: 50%;
    padding: 0;
    transition: all 0.2s ease;
    cursor: pointer;
}

.color-btn.active {
    border: 2px solid #333;
    box-shadow: 0 0 5px rgba(0,0,0,0.3);
    transform: scale(1.1);
}

.color-btn:hover {
    transform: scale(1.1);
}

#product-img {
    width: 100%;
    height: auto;
    object-fit: contain;
    border-radius: 8px;
    transition: opacity 0.3s ease;
}

.zoom {
    transition: transform .2s;
}

.zoom:hover {
    transform: scale(1.05);
}
</style>
    <style>
/* Preloader styles */
#preloader {
            position: fixed;
            left: 0;
            top: 0;
            z-index: 999;
            width: 100%;
            height: 100%;
            overflow: visible;
            background: #ffffff;
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
            transition: opacity 0.5s ease-in-out;
        }

        .visible {
            display: block;
            opacity: 1;
        }

        .color-btn {
    border: 2px solid #ddd;
    border-radius: 50%;
    padding: 0;
}
.color-btn.selected {
    border-color: #333;
    box-shadow: 0 0 5px rgba(0,0,0,0.5);
}
    .vendor-info {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }
    .vendor-info img {
        margin-right: 10px;
    }
</style>
    <style>
        .img1 {
             border-radius: 10px;
        }
        .btn {
            border-radius: 10px;
        }
      .star-rating {
          direction: ltr;
          display: inline-flex;
      }
      .star {
          font-size: 3.5rem;
          cursor: pointer;
          color: gold; 
      }
      .star:hover ~ .star {
          color: red; 
      }
      .fa-star, .fa-star-half-o, .fa-star-o {
          color: gold;
      }
      .comment-section {
    float: right;
    width: calc(50% - 20px); 
    margin-top: -80px; 
}
.comment-section .alert {[
    background: white;
]}
p1 {
    text-align: justify;
}

    </style>
</body>
</html>
