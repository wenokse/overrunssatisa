<?php include 'includes/session.php'; ?>
<?php
	$slug = $_GET['category'];

	$conn = $pdo->open();

	try {
		$stmt = $conn->prepare("SELECT * FROM category WHERE cat_slug = :slug");
		$stmt->execute(['slug' => $slug]);
		$cat = $stmt->fetch();
		$catid = $cat['id'];
	} catch (PDOException $e) {
		echo "There is some problem in connection: " . $e->getMessage();
	}

	$pdo->close();
?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue layout-top-nav">
<div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v13.0&appId=1346358252525630&autoLogAppEvents=1" nonce="hsdcri7l"></script>
<div class="wrapper">

	<?php 
	if (isset($_SESSION['user'])) {
		include 'includes/navbar.php';
	} else {
		include 'includes/home_navbar.php';
	}
	?>
	<style>
        .container2 {
            margin: 0px 0px 0px 50px; /* top right bottom left */
        }
    </style>
	<div class="content-wrapper">
	    <div class="container2">
	        <!-- Main content -->
	        <section class="content">
	            <div class="">
	                <div class="">
	                    <center><h1 class="page-header"><?php echo $cat['name']; ?></h1></center>
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
							$inc = 0;
							echo "<div class='product-container'>";     
							$stmt = $conn->prepare("SELECT products.*, IFNULL(AVG(ratings.rating), 0) AS average_rating FROM products LEFT JOIN ratings ON products.id = ratings.product_id WHERE products.category_id = :catid GROUP BY products.id");
							$stmt->execute(['catid' => $catid]);
							foreach ($stmt as $row) {
								$image = (!empty($row['photo'])) ? 'images/'.$row['photo'] : 'images/noimage.jpg';
								if ($inc % 5 == 0) echo "<div class='row'>";
								echo "
								<a href='product?product=".$row['slug']."'>
									<div class='col-sm-2 product'>
										<div class='box box-solid'>
											<div class='box-body prod-body'>
												<img src='".$image."' width='100%' height='230px' class='thumbnail'>
												<h5><a href='product?product=".$row['slug']."'>".$row['name']."</a></h5>
											</div>
											<div class='box-footer'>
												<b>&#8369; ".number_format($row['price'], 2)." each</b>
												<div>Rating: ".getStarRating($row['average_rating'])."</div>
											</div>
										</div>
									</div>
								</a>
								";
								$inc++;
								if ($inc % 5 == 0) echo "</div>";
							}
							if ($inc % 5 != 0) echo "</div>"; // close the last row if not complete
						} catch (PDOException $e) {
							echo "There is some problem in connection: " . $e->getMessage();
						}

						$pdo->close();
						?>

	                </div>
	                
	            </div>
	        </section>
	    </div>
	</div>

	<?php include 'includes/footer.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>
<style>
	.fa-star, .fa-star-half-o, .fa-star-o {
		color: gold;
	}

	.btn-primary :hover {
		background-color: #f0f0f0; /* Change this color to your desired minimalistic color */
		color: #333; /* Change this color to your desired text color */
		border-color: #f0f0f0; /* Change this color to match the background color */
	}

.product-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
    margin: 20px 0;
}

.product-container .row {
    width: 100%;
    /* display: flex; */
    flex-wrap: wrap;
    justify-content: space-around;
}

.product-container .product {
    flex: 0 1 calc(20% - 20px);
    margin: 10px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transition: transform 0.2s ease-in-out;
}
.product-container .product2 {
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
    border-radius: 5px;
    object-fit: cover;
}

.product-container .product .box-body h5 {
    font-size: 16px;
    font-family: justify;  
    margin-bottom: 15px;
    font-weight: bold;
}

.product-container .product .box-footer {
    /* padding: 10px 15px; */
    background-color: #f9f9f9;
    text-align: center;
    border-top: 1px solid #eee;
}

.product-container .product .box-footer b {
    font-size: 14px;
    color: #333;
}

.product-container .product .box-footer div {
    margin-top: 5px;
}
.page-header {
	font-family: Arial;
}
</style>
</body>
</html>
