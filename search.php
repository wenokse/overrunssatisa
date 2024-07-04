<?php include 'includes/session.php'; ?>
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
	 
<div class="content-wrapper">
    <div class="container">

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-sm-9">
                    <?php
                        $conn = $pdo->open();

                        $stmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM products WHERE name LIKE :keyword");
                        $stmt->execute(['keyword' => '%'.$_POST['keyword'].'%']);
                        $row = $stmt->fetch();
                        if($row['numrows'] < 1){
                            echo '<h1 class="page-header">No results found for <i>'.$_POST['keyword'].'</i></h1>';
                        }
                        else{
                            echo '<h1 class="page-header">Search results for <i>'.$_POST['keyword'].'</i></h1>';
                            try{
                                echo "<div class='product-container'>";
                                $stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE :keyword");
                                $stmt->execute(['keyword' => '%'.$_POST['keyword'].'%']);

                                foreach ($stmt as $row) {
                                    $highlighted = preg_filter('/' . preg_quote($_POST['keyword'], '/') . '/i', '<b>$0</b>', $row['name']);
                                    $image = (!empty($row['photo'])) ? 'images/'.$row['photo'] : 'images/noimage.jpg';
                                    echo "
                                        <div class='col-sm-4 product'>
                                            <div class='box box-solid'>
                                                <div class='box-body prod-body'>
                                                    <img src='".$image."' width='100%' height='230px' class='thumbnail'>
                                                    <h5><a href='product.php?product=".$row['slug']."'>".$highlighted."</a></h5>
                                                </div>
                                                <div class='box-footer'>
                                                    <b>&#8369; ".number_format($row['price'], 2)."</b>
                                                </div>
                                            </div>
                                        </div>
                                    ";
                                }
                                echo "</div>";
                            }
                            catch(PDOException $e){
                                echo "There is some problem in connection: " . $e->getMessage();
                            }
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
    .product-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: center;
        margin: 20px 0;
    }

    .product-container .product {
        flex: 0 1 calc(30% - 20px); /* Adjusted for better spacing */
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
        margin-bottom: 15px;
        font-weight: bold;
    }

    .product-container .product .box-footer {
        background-color: #f9f9f9;
        text-align: center;
        border-top: 1px solid #eee;
        padding: 10px;
    }

    .product-container .product .box-footer b {
        font-size: 14px;
        color: #333;
    }
</style>
</body>
</html>
