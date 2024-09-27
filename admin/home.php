<?php 
  include 'includes/session.php';
  include 'includes/format.php'; 
?>
<?php 
  $today = date('Y-m-d');
  $todaysmonth = date('m');
  $todaysyear = date('Y');
  $year = date('Y');
  if(isset($_GET['year'])){
    $year = $_GET['year'];
  }

  $conn = $pdo->open();
?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>
  <script src="../js/sweetalert2.min.js"></script>
  <script src="../js/sweetalert.min.js"></script>

  <div class="content-wrapper">
    <section class="content-header">
    <h1>
    <?php if ($admin['type'] == 1): ?>
        <b>Dashboard</b>
        <?php else: ?>
        <b>Vendor Dashboard</b>
        <?php endif; ?>
      </h1>
      <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Dashboard</li>
      </ol>
    </section>

    <section class="content">
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
<!-- For Administrator -->

      <?php if ($admin['type'] == 1): ?>
      <div class="row">
      <?php
    function createSmallBox($title, $icon, $query, $params, $conn, $link) {
      $stmt = $conn->prepare($query);
      $stmt->execute($params);
      $total = 0;
      foreach ($stmt as $row) {
        $subtotal = $row['price'] * $row['quantity'];
        $total += $subtotal + $row['shipping'];
      }
      $formattedTotal = number_format_short($total, 2);
      echo "
        <div class='col-lg-4 col-xs-6'>
          <div class='small-box gradient-box1'>
            <div class='inner'>
              <h3>&#8369; $formattedTotal</h3>
              <p>$title</p>
            </div>
            <div class='icon'>
              <i class='fa $icon'></i>
            </div>
            <a href='$link' class='small-box-footer'>More info <i class='fa fa-arrow-circle-right'></i></a>
          </div>
        </div>
      ";
    }

    $admin_id = $_SESSION['admin'];
    $user_condition = ($admin['type'] == 1) ? "" : "AND p.user_id = :user_id";
    $params = ($admin['type'] == 1) ? [] : ['user_id' => $admin_id];

    createSmallBox("Total Sales", "fa-money", 
      "SELECT d.*, p.price FROM details d 
      LEFT JOIN products p ON p.id = d.product_id 
      WHERE 1=1 $user_condition", 
      $params, $conn, "sales.php");

    createSmallBox("Sales This Year", "fa-money", 
      "SELECT d.*, p.price FROM details d 
      LEFT JOIN sales s ON s.id = d.sales_id 
      LEFT JOIN products p ON p.id = d.product_id 
      WHERE YEAR(s.sales_date) = :todaysyear $user_condition", 
      array_merge($params, ['todaysyear' => $todaysyear]), $conn, "sales.php");

    createSmallBox("Sales This Month", "fa-money", 
      "SELECT d.*, p.price FROM details d 
      LEFT JOIN sales s ON s.id = d.sales_id 
      LEFT JOIN products p ON p.id = d.product_id 
      WHERE MONTH(s.sales_date) = :todaysmonth $user_condition", 
      array_merge($params, ['todaysmonth' => $todaysmonth]), $conn, "sales.php");

  ?>
  </div>
      <div class="row">
  <?php
  // Row 2
  createSmallBox("Sales Today", "fa-money", 
    "SELECT d.*, p.price FROM details d 
    LEFT JOIN sales s ON s.id = d.sales_id 
    LEFT JOIN products p ON p.id = d.product_id 
    WHERE s.sales_date = :today $user_condition", 
    array_merge($params, ['today' => $today]), $conn, "sales.php");
  ?>

  <div class="col-lg-4 col-xs-6">
    <div class="small-box gradient-box1">
      <div class="inner">
        <?php
        if($admin['type'] == 1) {
          $stmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM sales");
          $stmt->execute();
        } else {
          $stmt = $conn->prepare("SELECT COUNT(DISTINCT s.id) AS numrows 
                                  FROM sales s
                                  JOIN details d ON s.id = d.sales_id
                                  JOIN products p ON d.product_id = p.id
                                  WHERE p.user_id = :user_id");
          $stmt->execute(['user_id' => $admin['id']]);
        }
        $urow = $stmt->fetch();
        echo "<h3>".$urow['numrows']."</h3>";
        ?>
        <p>Number of Sales</p>
      </div>
      <div class="icon">
        <i class="fa fa-shopping-cart"></i>
      </div>
      <a href="sales.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
    </div>
  </div>

  <div class="col-lg-4 col-xs-6">
    <div class="small-box gradient-box1">
      <div class="inner">
        <?php
        if($admin['type'] == 1) {
          $stmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM products");
          $stmt->execute();
        } else {
          $stmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM products WHERE user_id = :user_id");
          $stmt->execute(['user_id' => $admin['id']]);
        }
        $prow = $stmt->fetch();
        echo "<h3>".$prow['numrows']."</h3>";
        ?>
        <p>Number of Products</p>
      </div>
      <div class="icon">
        <i class="fa fa-barcode"></i>
      </div>
      <a href="products.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
    </div>
  </div>
</div>
<div class="row">
        <div class="col-lg-4 col-xs-6">
      <div class="small-box gradient-box1">
        <div class="inner">
        <?php
          if($admin['type'] == 1) {
            $stmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM return_products");
            $stmt->execute();
          } else {
            $stmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM return_products 
                                    WHERE user_id = :user_id");
            $stmt->execute(['user_id' => $admin['id']]);
          }
          $prow = $stmt->fetch();
          echo "<h3>".$prow['numrows']."</h3>";
          ?>
          <p>Number of Return Products</p>
        </div>
        <div class="icon">
          <i class="fa fa-truck"></i>
        </div>
        <a href="return_product.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
        <div class="col-lg-4 col-xs-6">
          <div class="small-box gradient-box1">
            <div class="inner">
            <?php
                $stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM users WHERE type=:type");
                $stmt->execute(['type'=>0]);
                $urow = $stmt->fetch();
                echo "<h3>".$urow['numrows']."</h3>";
              ?>
              <p>Number of Customers</p>
            </div>
            <div class="icon">
              <i class="fa fa-users"></i>
            </div>
            <a href="customer.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
    
    
    <div class="col-lg-4 col-xs-6">
      <div class="small-box gradient-box1">
        <div class="inner">
          <?php
          $stmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM users WHERE type=:type");
          $stmt->execute(['type'=>2]);
          $urow = $stmt->fetch();
          echo "<h3>".$urow['numrows']."</h3>";
          ?>
          <p>Number of Vendors</p>
        </div>
        <div class="icon">
          <i class="fa fa-users"></i>
        </div>
        <a href="vendor.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
   
 <!-- for vendor -->
    <?php else: ?>
      <div class="row">
      <?php
    function createSmallBox($title, $icon, $query, $params, $conn, $link) {
      $stmt = $conn->prepare($query);
      $stmt->execute($params);
      $total = 0;
      foreach ($stmt as $row) {
        $subtotal = $row['price'] * $row['quantity'];
        $total += $subtotal + $row['shipping'];
      }
      $formattedTotal = number_format_short($total, 2);
      echo "
        <div class='col-lg-3 col-xs-6'>
          <div class='small-box gradient-box'>
            <div class='inner'>
              <h3>&#8369; $formattedTotal</h3>
              <p>$title</p>
            </div>
            <div class='icon'>
              <i class='fa $icon'></i>
            </div>
            <a href='$link' class='small-box-footer'>More info <i class='fa fa-arrow-circle-right'></i></a>
          </div>
        </div>
      ";
    }

    $admin_id = $_SESSION['admin'];
    $user_condition = ($admin['type'] == 1) ? "" : "AND p.user_id = :user_id";
    $params = ($admin['type'] == 1) ? [] : ['user_id' => $admin_id];

    createSmallBox("Total Sales", "fa-money", 
      "SELECT d.*, p.price FROM details d 
      LEFT JOIN products p ON p.id = d.product_id 
      WHERE 1=1 $user_condition", 
      $params, $conn, "sales.php");

    createSmallBox("Sales This Year", "fa-money", 
      "SELECT d.*, p.price FROM details d 
      LEFT JOIN sales s ON s.id = d.sales_id 
      LEFT JOIN products p ON p.id = d.product_id 
      WHERE YEAR(s.sales_date) = :todaysyear $user_condition", 
      array_merge($params, ['todaysyear' => $todaysyear]), $conn, "sales.php");

    createSmallBox("Sales This Month", "fa-money", 
      "SELECT d.*, p.price FROM details d 
      LEFT JOIN sales s ON s.id = d.sales_id 
      LEFT JOIN products p ON p.id = d.product_id 
      WHERE MONTH(s.sales_date) = :todaysmonth $user_condition", 
      array_merge($params, ['todaysmonth' => $todaysmonth]), $conn, "sales.php");

    createSmallBox("Sales Today", "fa-money", 
      "SELECT d.*, p.price FROM details d 
      LEFT JOIN sales s ON s.id = d.sales_id 
      LEFT JOIN products p ON p.id = d.product_id 
      WHERE s.sales_date = :today $user_condition", 
      array_merge($params, ['today' => $today]), $conn, "sales.php");
      
  ?>
        <div class="col-lg-3 col-xs-6">
          <div class="small-box gradient-box">
            <div class="inner">
            <?php
      if($admin['type'] == 1) {
        $stmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM sales");
        $stmt->execute();
      } else {
        $stmt = $conn->prepare("SELECT COUNT(DISTINCT s.id) AS numrows 
                                FROM sales s
                                JOIN details d ON s.id = d.sales_id
                                JOIN products p ON d.product_id = p.id
                                WHERE p.user_id = :user_id");
        $stmt->execute(['user_id' => $admin['id']]);
      }
      $urow = $stmt->fetch();
      echo "<h3>".$urow['numrows']."</h3>";
      ?>
              <p>Number of Sales</p>
            </div>
            <div class="icon">
              <i class="fa fa-shopping-cart"></i>
            </div>
            <a href="sales.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <div class="col-lg-3 col-xs-6">
          <div class="small-box">
            <div class="inner">
                  <?php
              if($admin['type'] == 1) {
                $stmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM products");
                $stmt->execute();
              } else {
                $stmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM products WHERE user_id = :user_id");
                $stmt->execute(['user_id' => $admin['id']]);
              }
              $prow = $stmt->fetch();
              echo "<h3>".$prow['numrows']."</h3>";
            ?>
              <p>Number of Products</p>
            </div>
            <div class="icon">
              <i class="fa fa-barcode"></i>
            </div>
            <a href="products.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <div class="col-lg-3 col-xs-6">
      <div class="small-box gradient-box">
        <div class="inner">
        <?php
          if($admin['type'] == 1) {
            $stmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM return_products");
            $stmt->execute();
          } else {
            // Assuming return_products table has a user_id column
            $stmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM return_products 
                                    WHERE user_id = :user_id");
            $stmt->execute(['user_id' => $admin['id']]);
          }
          $prow = $stmt->fetch();
          echo "<h3>".$prow['numrows']."</h3>";
          ?>
          <p>Number of Return Products</p>
        </div>
        <div class="icon">
          <i class="fa fa-truck"></i>
        </div>
        <a href="return_product.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
        <div class="col-lg-3 col-xs-6">
          <div class="small-box">
            <div class="inner">
            <?php
                $stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM users WHERE type=:type");
                $stmt->execute(['type'=>0]);
                $urow = $stmt->fetch();
                echo "<h3>".$urow['numrows']."</h3>";
              ?>
              <p>Number of Customers</p>
            </div>
            <div class="icon">
              <i class="fa fa-users"></i>
            </div>
            <a href="customer.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
      </div>
      <?php endif; ?>
    
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Monthly Sales Report</h3>
              <div class="box-tools pull-right">
                <form class="form-inline">
                  <div class="form-group">
                    <label>Select Year: </label>
                    <select class="form-control input-sm" id="select_year">
                      <?php
                        for($i=2021; $i<=2065; $i++){
                          $selected = ($i==$year)?'selected':'';
                          echo "<option value='".$i."' ".$selected.">".$i."</option>";
                        }
                      ?>
                    </select>
                  </div>
                </form>
              </div>
            </div>
            <div class="box-body">
              <div class="chart">
                <br>
                <div id="legend" class="text-center"></div>
                <canvas id="barChart" style="height:320px"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
  <?php include 'includes/footer.php'; ?>
</div>

<style>
  .small-box {
    background: linear-gradient(to right, #FBAED2, #D8BFD8);
    border-radius: 20px;
/*    box-shadow: 0 0 10px rgba(0,0,0,0.1);*/
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3); /* Darker shadow */
    padding: 20px;
  }

 .small-box:hover {
    color: black;
  }
  .gradient-box {
    background: linear-gradient(to right, #FBAED2, #D8BFD8);
  }
  .gradient-box1 {
    background: linear-gradient(to right, #99FFDD, #FBA2CC);
  }
  
  

  .content-wrapper {
    background: white;
  }
</style>

<!-- ./wrapper -->

<!-- Chart Data -->
<?php
  $months = array();
  $sales = array();
  for( $m = 1; $m <= 12; $m++ ) {
    try{
      if($admin['type'] == 1) {
      
        $stmt = $conn->prepare("SELECT SUM(d.quantity * p.price) as total 
                                FROM details d 
                                LEFT JOIN sales s ON s.id=d.sales_id 
                                LEFT JOIN products p ON p.id=d.product_id 
                                WHERE MONTH(s.sales_date)=:month AND YEAR(s.sales_date)=:year");
        $stmt->execute(['month'=>$m, 'year'=>$year]);
      } else {
       
        $stmt = $conn->prepare("SELECT SUM(d.quantity * p.price) as total 
                                FROM details d 
                                LEFT JOIN sales s ON s.id=d.sales_id 
                                LEFT JOIN products p ON p.id=d.product_id 
                                WHERE MONTH(s.sales_date)=:month AND YEAR(s.sales_date)=:year AND p.user_id=:user_id");
        $stmt->execute(['month'=>$m, 'year'=>$year, 'user_id'=>$admin['id']]);
      }
      $total = 0;
      $row = $stmt->fetch();
      $total = $row['total'] ?? 0;
      array_push($sales, round($total, 2));
    }
    catch(PDOException $e){
      echo $e->getMessage();
    }

    $num = str_pad( $m, 2, 0, STR_PAD_LEFT );
    $month =  date('M', mktime(0, 0, 0, $m, 1));
    array_push($months, $month);
  }

  $months = json_encode($months);
  $sales = json_encode($sales);

?>
<!-- End Chart Data -->

<?php $pdo->close(); ?>
<?php include 'includes/scripts.php'; ?>
<script>
$(function(){
  var barChartCanvas = $('#barChart').get(0).getContext('2d')
  var barChart = new Chart(barChartCanvas)
  var barChartData = {
    labels  : <?php echo $months; ?>,
    datasets: [
      {
        label               : 'SALES',
        fillColor           : '#6dd5ed',
        strokeColor         : '#a8e063',
        pointColor          : '#3b8bba',
        pointStrokeColor    : 'rgba(60,141,188,1)',
        pointHighlightFill  : '#fff',
        pointHighlightStroke: 'rgba(60,141,188,1)',
        data                : <?php echo $sales; ?>
      }
    ]
  }
  var barChartOptions                  = {
    // ... (keep your existing options here)
  }

  barChartOptions.datasetFill = false
  var myChart = barChart.Bar(barChartData, barChartOptions)
  document.getElementById('legend').innerHTML = myChart.generateLegend();
});
</script>
<script>
$(function(){
  $('#select_year').change(function(){
    window.location.href = 'home.php?year='+$(this).val();
  });
});
</script>
</body>
</html>
