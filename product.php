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

} catch (PDOException $e) {
    exit("There is some problem in connection: " . $e->getMessage());
}


$category_name = strtolower($product['catname']);
$sizes = '';
$colors = '';

if ($category_name == 'shoes' || $category_name == 'sandals') {
    $sizes = '
        <option value="36">36</option>
        <option value="37">37</option>
        <option value="38">38</option>
        <option value="39">39</option>
        <option value="40">40</option>
        <option value="41">41</option>
    ';
    $colors = '
        <option value="White">White</option>
        <option value="Black">Black</option>
        <option value="Brown">Brown</option>
        <option value="Gray">Gray</option>
    ';
} elseif ($category_name == 'pants') {
    $sizes = '
        <option value="24">24</option>
        <option value="25">25</option>
        <option value="26">26</option>
        <option value="27">27</option>
        <option value="28">28</option>
        <option value="29">29</option>
    ';
    $colors = '
        <option value="Blue">Blue</option>
        <option value="Black">Black</option>
        <option value="Grey">Grey</option>
    ';

} elseif ($category_name == 't-shirts') {
    $sizes = '
        <option value="Small">Small</option>
        <option value="Medium">Medium</option>
        <option value="Large">Large</option>
        <option value="XLarge">XLarge</option>
    ';
    $colors = '
        <option value="White">White</option>
        <option value="Blue">Blue</option>
        <option value="Black">Black</option>
        <option value="Grey">Grey</option>
    ';
} elseif ($category_name == 'bags' || $category_name == 'accessories') {
    $colors = '
        <option value="Red">Red</option>
        <option value="Green">Green</option>
        <option value="Blue">Blue</option>
    ';
} else {
    $sizes = '
        <option value="Small">Small</option>
        <option value="Medium">Medium</option>
        <option value="Large">Large</option>
        <option value="XLarge">XLarge</option>
    ';
    $colors = '
        <option value="Red">Red</option>
        <option value="Green">Green</option>
        <option value="Yellow">Yellow</option>
    ';
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
                                <div class="col-sm-4">
                                <div class="show" href="<?php echo (!empty($product['photo'])) ? 'images/'.$product['photo'] : 'images/noimage.jpg'; ?>">
                                <img src="<?php echo (!empty($product['photo'])) ? 'images/'.$product['photo'] : 'images/noimage.jpg'; ?>" id="product-img" class="zoom img1" data-magnify-src="images/large-<?php echo $product['photo']; ?>">
                                </div>
                               
                                    <br>
                                   
                                    <?php if (!empty($sizes) && !in_array($category_name, ['bags', 'accessories'])) : ?>
                                    <div class="form-group">
                                        <label for="size">Size :</label>
                                        <select class="form-control input-lg" id="size" name="size">
                                            <?php echo $sizes; ?>
                                        </select>
                                    </div>
                                <?php endif; ?>

                                    <br>
                                    <label for="color">Color :</label>
                                    
                                    <div class="form-group" id="color-buttons">
                                    <?php foreach ($color_options as $color): ?>
                                        <button type="button" class="btn btn-default color-btn" 
                                                style="background-color: <?php echo $color['color']; ?>; width: 30px; height: 30px; margin-right: 5px;"
                                                data-color="<?php echo $color['color']; ?>"
                                                data-photo="<?php echo $color['photo']; ?>">
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                                    <br>
                                    <form class="form-inline" id="productForm" method="post" action="cart_add.php">
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
                                <div class="col-sm-8">
                                <div class="vendor-info">
                                <a href="shop.php?id=<?php echo $product['vendor_id']; ?>" style="color: black;">
                                    <img src="<?php echo (!empty($product['vendor_photo'])) ? 'images/'.$product['vendor_photo'] : 'images/noimage.jpg'; ?>" 
                                        style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                                    <strong><?php echo $product['vendor_store']; ?></strong>
                                </a>
                                    </div>
                                    <h1 class="page-header"><b><?php echo $product['prodname']; ?></b>
                                   </h1>
                                    <span><h3><b>&#8369; <span id="price"><?php echo number_format($product['price'], 2); ?> per.</span></b></h3>
                                    <div class="comment-section pull-right">
                                        <h3>Leave a Comment</h3>
                                        <form id="comment_form" onsubmit="return validateComment()">
                                            <div class="form-group">
                                                <label for="comment">Your Comment:</label>
                                                <textarea class="form-control" id="comment" name="comment" required></textarea>
                                            </div>
                                            <input type="hidden" name="product_id" value="<?php echo $product['prodid']; ?>">
                                            <button type="submit" class="btn btn-primary">Submit Comment</button>
                                        </form>
                                        <br>
                                        <div id="comment_message" class="alert" style="display: none;"></div>
                                        <div id="comment_list"></div>
                                    </div>
                                    </span>
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

                                    <p><b>Category:</b> <a href="category.php?category=<?php echo $product['cat_slug']; ?>"><?php echo $product['catname']; ?></a></p>
                                    <p><b>Description:</b></p>
                                    <p1><?php echo $product['description']; ?></p1>

                                    <!-- Display Average Rating -->
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

                                <!-- <div class="col-sm-8">
                                    
                                    <div class="comment-section pull-right">
                                        <h3>Leave a Comment</h3>
                                        <form id="comment_form">
                                            <div class="form-group">
                                                <label for="comment">Your Comment:</label>
                                                <textarea class="form-control" id="comment" name="comment" required></textarea>
                                            </div>
                                            <input type="hidden" name="product_id" value="<?php echo $product['prodid']; ?>">
                                            
                                            <button type="submit" class="btn btn-primary">Submit Comment</button>
                                        </form>
                                        <br>
                                        <div id="comment_message" class="alert" style="display: none;"></div>
                                        <div id="comment_list"></div>
                                    </div>
                                </div> -->
                            </div>
                            <br>  <br> 
                            <div class="row">
                                <div class="col-sm-12">
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
                                        <a href='product.php?product=".$related_product['slug']."'>
                                            <div class='col-sm-3'>
                                                <div class='box box-solid'>
                                                    <div class='box-body prod-body'>
                                                        <img src='$image' width='100%' height='230px' class='thumbnail'>
                                                        <h5><a href='product.php?product=".$related_product['slug']."'>".$related_product['name']."</a></h5>
                                                    </div>
                                                    <div class='box-footer'>
                                                        <b>&#8369; ".number_format($related_product['price'], 2)."</b>
                                                        <div>Rating: ".getStarRating($product_with_rating['average_rating'])."</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                        ";
                                    }
                                    ?>
                                </div>
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
        function validateComment() {
        const comment = document.getElementById('comment').value.trim();
        const regex = /^[a-zA-Z0-9\s?!.,\-=:]+$/;

        if (comment.length === 0) {
            alert('Comment cannot be empty.');
            return false;
        }

        if (!regex.test(comment)) {
            alert('Invalid comment! Only letters, numbers, spaces, and . , ? ! - = : are allowed.');
            return false;
        }

        return true;
    }

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
                    fetch('rate_product.php', {
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
            $('.color-btn').click(function() {
        var selectedColor = $(this).data('color');
        var selectedPhoto = $(this).data('photo');
        
        // Update hidden input for color
        $('#selected_color').val(selectedColor);
        
        // Update product image
        $('#product-img').attr('src', 'images/colors/' + selectedPhoto);
        
        // Update magnify source if you're using image zoom
        $('#product-img').data('magnify-src', 'images/large-' + selectedPhoto);
        
        // Highlight selected color button
        $('.color-btn').removeClass('selected');
        $(this).addClass('selected');
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
                url: 'submit_comment.php',
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        swal({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            onClose: function () {
                                $('#comment_message').removeClass('alert-danger').addClass('alert-success').text(response.message).show();
                                $('#comment_form')[0].reset();
                                loadComments();
                            }
                        });
                    } else {
                        swal({
                            icon: 'error',
                            title: 'Login First!',
                            text: response.message.redirect,
                            onClose: function () {
                                $('#comment_message').removeClass('alert-success').addClass('alert-danger').text(response.message).show();
                            }
                        }).then((willRedirect) => {
                            if (willRedirect) {
                                // Redirect to the login page
                                window.location.href = response.redirect;
                            }
                        });
                    }
                }
            });
        });
   
$(document).ready(function() {
    $(document).on('click', '.like-btn', function() {
        var commentId = $(this).data('comment-id');
        handleCommentAction(commentId, 'like');
    });

    $(document).on('click', '.dislike-btn', function() {
        var commentId = $(this).data('comment-id');
        handleCommentAction(commentId, 'dislike');
    });

    function handleCommentAction(commentId, action) {
        $.ajax({
            url: 'like_comment.php',
            method: 'POST',
            data: {
                comment_id: commentId,
                action: action
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    loadComments();
                } else {
                    alert('An error occurred.');
                }
            }
        });
    }

    function loadComments() {
            $.ajax({
                url: 'fetch_comments.php',
                method: 'GET',
                data: { product_id: <?php echo $product['prodid']; ?> },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#comment_list').html(response.comments);
                    } else {
                        $('#comment_list').html('<p>No comments yet, Be the first comment.</p>');
                    }
                }
            });
        }

        loadComments();
    });


    
    loadComments();
});

    </script>
    <style>
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
