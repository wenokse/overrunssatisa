<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue layout-top-nav">
<div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v13.0&appId=1346358252525630&autoLogAppEvents=1" nonce="hsdcri7l"></script>
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

        .category-image {
            cursor: pointer;
            display: flex;
            flex-wrap: wrap;
        }
        .category-image {
            flex: 0 1 calc(50% - 10px);
            max-width: calc(50% - 10px);
        }

        @media (max-width: 576px) {
            .category-image {
                flex: 0 1 100%;
                max-width: 100%;
            }
        }

        .product-container .product {
    flex: 0 1 calc(50% - 20px);
}

@media (max-width: 576px) {
    .product-container .product {
        flex: 0 1 100%;
    }
}

@media (max-width: 768px) {
    .content-wrapper {
        padding: 15px;
    }
}
img {
    max-width: 100%;
    height: auto;
}
@media (max-width: 768px) {
    .slider-arrow {
        font-size: 14px;
        padding: 5px;
    }
}

        .product-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
    margin: 20px 0;
}

.product-container .product {
    flex: 0 1 calc(20% - 20px);
    margin: 10px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transition: transform 0.2s ease-in-out;
}

        .product-container .product:hover {
            transform: translateY(-5px);
        }

        .product-container .product .box {
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
            background-color: #fff;
        }

        .product-container .product .box-body {
            padding: 15px;
            text-align: center;
        }

        .product-container .product .box-body img {
             height: 200px; 
    object-fit: cover; 
        }

        .product-container .product .box-body h5 {
            font-size: 16px;
            font-family: justify;  
            margin-bottom: 15px;
            font-weight: bold;
        }

        .product-container .product .box-footer {
            background-color: #f9f9f9;
            text-align: center;
            border-top: 1px solid #eee;
        }

        .product-container .product .box-footer b {
            font-size: 14px;
           
        }

        .product-container .product .box-footer div {
            margin-top: 5px;
        }

/*        Mobile view*/

@media (max-width: 768px) {
    .product-container {
        gap: 10px;
    }

    .product-container .product {
        flex: 0 1 calc(50% - 10px);
        margin: 5px;
    }

    .product-container .product .box-body {
        padding: 10px;
    }

    .product-container .product .box-body h5 {
        font-size: 14px;
        margin-bottom: 10px;
    }

    .product-container .product .box-body img {
        height: 150px;
        object-fit: cover;
    }

    .product-container .product .box-footer b {
        font-size: 12px;
    }

    .product-container .product .box-footer div {
        font-size: 12px;
    }
}

@media (max-width: 480px) {
    .product-container .product {
        flex: 0 1 100%;
    }

    .product-container .product .box-body img {
        height: 200px;
    }
}

/* Improve visibility of the back button on mobile */
@media (max-width: 768px) {
    #backButton {
        position: fixed;
        top: 10px;
        left: 10px;
        z-index: 1000;
        padding: 10px 15px;
        font-size: 16px;
    }
}
    </style>

    <div class="content-wrapper">
        <div class="container2">
            <!-- Main content -->
            <section class="content">
                
                <div class="row">
                    <div class="">
                          <img src="image/home.jpg" class="img1">
                        <!-- CSS for Slider -->
                        
                        

                        <hr style="border-top: 5px solid black;">
                        <h2>Categories</h2>
                        <button id="backButton" class="btn btn-primary" style="display: none;">Back to Categories</button>
                        <?php
                            $conn = $pdo->open();
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

          try {
                            echo "<div class='category-images-container'>";
                            $categories = $conn->query("SELECT * FROM category")->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($categories as $category) {
                                $stmt = $conn->prepare("SELECT products.*, AVG(ratings.rating) AS average_rating FROM products LEFT JOIN ratings ON products.id = ratings.product_id WHERE products.category_id = :category_id GROUP BY products.id");
                                $stmt->execute(['category_id' => $category['id']]);
                                
                                $products = $stmt->fetchAll();
                                if ($products) {
                                    $firstProduct = $products[0];
                                    $image = (!empty($firstProduct['photo'])) ? 'images/'.$firstProduct['photo'] : 'images/noimage.jpg';
                                    echo "
                                    <div class='category-image' data-category='".$category['id']."'>
                                        <img src='".$image."' width='100%' height='230px' class='product'>
                                        <div class='category-name'><h4>".$category['name']."</h4></div>
                                    </div>
                                ";
                                }
                                
                                echo "<div class='product-container hidden' data-category='".$category['id']."'>";
                                foreach ($products as $product) {
                                    $image = (!empty($product['photo'])) ? 'images/'.$product['photo'] : 'images/noimage.jpg';
                                    echo "
                                        <a href='product.php?product=".$product['slug']."'>
                                            <div class='product'>
                                                <div class='box box-solid'>
                                                    <div class='box-body prod-body'>
                                                        <img src='".$image."' width='100%' height='230px' class='thumbnail'>
                                                        <h5><a href='product.php?product=".$product['slug']."'>".$product['name']."</a></h5>
                                                    </div>
                                                    <div class='box-footer'>
                                                        <b>&#8369; ".number_format($product['price'], 2)." each</b>
                                                        <div>Rating: ".getStarRating($product['average_rating'])."</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    ";
                                }
                                echo "</div>";
                            }
                            echo "</div>";
                        } catch(PDOException $e) {
                            echo "There is some problem in connection: " . $e->getMessage();
                        }
                        $pdo->close();
                        ?>
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
document.addEventListener('DOMContentLoaded', () => {
    const categoriesContainer = document.getElementById('categories-container');
    const backButton = document.getElementById('backButton');

    document.querySelectorAll('.category-image').forEach(element => {
        element.addEventListener('click', () => {
            const categoryId = element.getAttribute('data-category');
            
            // Hide all category images
            document.querySelectorAll('.category-image').forEach(img => {
                img.style.display = 'none';
            });

            // Show only the selected category's products
            document.querySelectorAll('.product-container').forEach(container => {
                if (container.getAttribute('data-category') === categoryId) {
                    container.classList.remove('hidden');
                } else {
                    container.classList.add('hidden');
                }
            });

            // Show the back button
            backButton.style.display = 'block';
        });
    });

    backButton.addEventListener('click', () => {
        // Show all category images
        document.querySelectorAll('.category-image').forEach(img => {
            img.style.display = 'block';
        });

        // Hide all product containers
        document.querySelectorAll('.product-container').forEach(container => {
            container.classList.add('hidden');
        });

        // Hide the back button
        backButton.style.display = 'none';
    });
    if (navigator.userAgent.match(/Android/i)) {
        document.addEventListener('backbutton', handleBackButton, false);
    } else {
        window.addEventListener('popstate', handleBackButton);  // Handles browser back button
    }

    function handleBackButton(e) {
        e.preventDefault();  // Prevent default back action
        showExitPopup();
    }

    function showExitPopup() {
        popup.style.display = 'block';
    }

    function closeApp() {
        if (navigator.app && navigator.app.exitApp) {
            navigator.app.exitApp();
        } else {
            window.close();  // Fallback
        }
    }

    // Add event listeners for the popup buttons
    document.getElementById('exitYes').addEventListener('click', (e) => {
        e.preventDefault();
        closeApp();
    });

    document.getElementById('exitNo').addEventListener('click', (e) => {
        e.preventDefault();
        popup.style.display = 'none';
    });

    // Manage browser history
    window.history.pushState({ page: 1 }, "", "");
    window.addEventListener('popstate', function(e) {
        showExitPopup();  // Show the popup when user presses back button in browser
    });
});
</script>

<style>
    #popupCloseRight {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0,0,0,0.3);
        z-index: 1001;
        text-align: center;
    }

    #popupCloseRight .btn {
        display: inline-block;
        margin: 10px;
        padding: 10px 20px;
        text-decoration: none;
        color: white;
        border-radius: 5px;
    }

    #popupCloseRight .btn-primary {
        background-color: #007bff;
    }

    #popupCloseRight .btn-secondary {
        background-color: #6c757d;
    }
    
    h3 {
        font-weight: bolder;
        color: rgb(0, 51, 102);
    }
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f4f4f4;
    }

    .category-images-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 20px;
}

.category-image {
    flex: 0 1 calc(25% - 15px);
    max-width: calc(25% - 15px);
    margin-bottom: 20px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    border-radius: 10px;
    overflow: hidden;
    transition: transform 0.2s ease-in-out;
     position: relative;
    text-align: center;
}

.category-image:hover {
    transform: translateY(-5px);
}

.category-image img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.category-name {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background-color: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 10px 0;
}

.category-name h4 {
    margin: 0;
}
@media (max-width: 1200px) {
    .category-image {
        flex: 0 1 calc(33.333% - 15px);
        max-width: calc(33.333% - 15px);
    }
}

@media (max-width: 992px) {
    .category-image {
        flex: 0 1 calc(50% - 10px);
        max-width: calc(50% - 10px);
    }
}

@media (max-width: 576px) {
    .category-image {
        flex: 0 1 100%;
        max-width: 100%;
    }
}
    .img1 {
        width: 100%;
        height: auto; 
        max-height: 540px; 
        filter: contrast(100%);
        border-radius: 20px;
    }
    .fa-star, .fa-star-half-o, .fa-star-o {
        color: gold;
    }

    .btn-primary:hover {
        background-color: #f0f0f0; 
        color: #333; 
        border-color: #f0f0f0;
    }

    #backButton {
        margin-bottom: 20px;
        display: none;
        position: sticky;
        top: 20px;
        z-index: 1000;
    }

</style>
<script type="text/javascript" src="cordova.js"></script>
<script>
document.addEventListener("deviceready", onDeviceReady, false);

function onDeviceReady() {
    // Your existing DOMContentLoaded code
    const categoriesContainer = document.getElementById('categories-container');
    const backButton = document.getElementById('backButton');

    document.querySelectorAll('.category-image').forEach(element => {
        element.addEventListener('click', () => {
            const categoryId = element.getAttribute('data-category');
            
            // Hide all category images
            document.querySelectorAll('.category-image').forEach(img => {
                img.style.display = 'none';
            });

            // Show only the selected category's products
            document.querySelectorAll('.product-container').forEach(container => {
                if (container.getAttribute('data-category') === categoryId) {
                    container.classList.remove('hidden');
                } else {
                    container.classList.add('hidden');
                }
            });

            // Show the back button
            backButton.style.display = 'block';
        });
    });

    backButton.addEventListener('click', () => {
        // Show all category images
        document.querySelectorAll('.category-image').forEach(img => {
            img.style.display = 'block';
        });

        // Hide all product containers
        document.querySelectorAll('.product-container').forEach(container => {
            container.classList.add('hidden');
        });

        // Hide the back button
        backButton.style.display = 'none';
    });

    // Hide splashscreen after 4 seconds
    setTimeout(function() {
        navigator.splashscreen.hide();
    }, 4000);

    // Register back button event listener
    document.addEventListener("backbutton", onBackKeyDown, false);
}

function onBackKeyDown() {
    if (confirm("Are you sure you want to close this app?")) {
        navigator.app.exitApp(); // Close the app
    }
}

// Preloader script
window.addEventListener('load', function() {
    var preloader = document.getElementById('preloader');
    preloader.style.display = 'none';
});
</script>

</body>
</html>
