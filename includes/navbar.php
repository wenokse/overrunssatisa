<header class="main-header">
  <nav class="navbar navbar-static-top">
    <div class="container">
      <div class="navbar-header">
        <a href="index.php" class="navbar-brand navbar-width"><b>Overruns Sa Tisa Online Shop System</b></a>
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
          <i class="fa fa-bars"></i>
        </button>
      </div>

      <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
        <ul class="nav navbar-nav">
          <li><a href="index.php">HOME</a></li>
          <li><a href="about_us.php">ABOUT US</a></li>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">CATEGORY <span class="caret"></span></a>
            <ul class="dropdown-menu" role="menu">
              <?php
                $conn = $pdo->open();
                try{
                  $stmt = $conn->prepare("SELECT * FROM category");
                  $stmt->execute();
                  foreach($stmt as $row){
                    echo "<li><a href='category.php?category=".$row['cat_slug']."'>".$row['name']."</a></li>";                  
                  }
                }
                catch(PDOException $e){
                  echo "There is some problem in connection: " . $e->getMessage();
                }
                $pdo->close();
              ?>
            </ul>
          </li>
        </ul>
        <form method="POST" class="navbar-form navbar-left" action="search.php">
          <div class="input-group">
              <input type="text" class=" form-control" id="navbar-search-input" style="border-radius: 20px;" name="keyword" placeholder="Search for Product" required>
          </div>
        </form>
      </div>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <li class="dropdown messages-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-shopping-cart"></i>
              <span class="label label-danger cart_count"></span>
            </a>
            <ul class="dropdown-menu">
              <li class="header">You have <span class="cart_count"></span> item(s) in cart</li>
              <li>
                <ul class="menu" id="cart_menu">
                </ul>
              </li>
              <li class="footer"><a href="cart_view.php">Go to Cart</a></li>
            </ul>
          </li>
          <li class="dropdown notifications-menu">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <i class="fa fa-bell"></i>
        <?php
            $conn = $pdo->open();
            $stmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM sales WHERE user_id=:user_id AND status=2");
            $stmt->execute(['user_id'=>$_SESSION['user']]);
            $row = $stmt->fetch();
            if($row['numrows'] > 0){
                echo "
                    <span class='label label-warning'>".$row['numrows']."</span>
                ";
            }
            $pdo->close();
        ?>
    </a>
    <ul class="dropdown-menu">
        <li class="header">You have <?php echo $row['numrows']; ?> delivery notification(s)</li>
        <li>
            <ul class="menu">
                <?php
                    $conn = $pdo->open();
                    $stmt = $conn->prepare("
                        SELECT sales.id, sales.pay_id, products.name 
                        FROM sales 
                        LEFT JOIN details ON sales.id = details.sales_id 
                        LEFT JOIN products ON details.product_id = products.id 
                        WHERE sales.user_id = :user_id AND sales.status = 2
                    ");
                    $stmt->execute(['user_id'=>$_SESSION['user']]);
                    foreach($stmt as $row){
                        echo "
                            <li>
                                <a href='profile.php?order=".$row['id']."'>
                                    <i class='fa fa-truck text-aqua'></i> Product: ".$row['name']." is on delivery
                                </a>
                            </li>
                        ";
                    }
                    $pdo->close();
                ?>
            </ul>
        </li>
    </ul>
</li>

          <?php
            if(isset($_SESSION['user'])){
              $image = (!empty($user['photo'])) ? 'images/'.$user['photo'] : 'images/profile.jpg';
              echo '
                <li class="dropdown user user-menu">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <img src="'.$image.'" class="user-image" alt="User Image">
                    <span class="hidden-xs">'.$user['firstname'].' '.$user['lastname'].'</span>
                  </a>
                  <ul class="dropdown-menu">
                    <li class="user-header">
                      <img src="'.$image.'" class="img-circle" alt="User Image">
                      <p>
                        '.$user['firstname'].' '.$user['lastname'].'
                        <small>Member since '.date('M. Y', strtotime($user['created_on'])).'</small>
                      </p>
                    </li>
                    <li class="user-footer">
                      <div class="pull-left">
                        <a href="profile.php" class="btn btn-default btn-flat" style="border-radius: 15px;">Profile</a>
                      </div>
                      <div class="pull-right">
                        <a href="logout.php" class="btn btn-default btn-flat" style="border-radius: 15px;">Log out</a>
                      </div>
                    </li>
                  </ul>
                </li>
              ';
            }
            else{
              echo "
                <li><a href='login.php'>LOGIN</a></li>
                <li><a href='signup.php'>SIGNUP</a></li>
              ";
            }
          ?>
        </ul>
      </div>
    </div>
  </nav>
</header>
