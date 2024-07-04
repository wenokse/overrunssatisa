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
  <script src="../js/sweetalert.min.js"></script>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        <b>Dashboard</b>
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
      <div class="row">
        <?php
          function createSmallBox($title, $icon, $query, $conn, $link, $condition = true) {
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $total = 0;
            foreach ($stmt as $row) {
              $subtotal = $row['price'] * $row['quantity'];
              $total += $subtotal;
            }
            $formattedTotal = number_format_short($total, 2);
            $footerLink = $condition ? "<a href='$link' class='small-box-footer'>More info <i class='fa fa-arrow-circle-right'></i></a>" : "<a class='small-box-footer'>More info <i class='fa fa-arrow-circle-right'></i></a>";

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
                  $footerLink
                </div>
              </div>
            ";
          }

          createSmallBox("Total Sales", "fa-money", "SELECT * FROM details LEFT JOIN products ON products.id=details.product_id", $conn, "sales.php", $admin['type'] <= 1);
          createSmallBox("Sales Today's Year", "fa-money", "SELECT * FROM details LEFT JOIN sales ON sales.id=details.sales_id LEFT JOIN products ON products.id=details.product_id WHERE YEAR(sales_date) = '$todaysyear'", $conn, "sales.php", $admin['type'] <= 1);
          createSmallBox("Sales Today's Month", "fa-money", "SELECT * FROM details LEFT JOIN sales ON sales.id=details.sales_id LEFT JOIN products ON products.id=details.product_id WHERE MONTH(sales_date) = '$todaysmonth'", $conn, "sales.php", $admin['type'] <= 1);
          createSmallBox("Sales Today", "fa-money", "SELECT * FROM details LEFT JOIN sales ON sales.id=details.sales_id LEFT JOIN products ON products.id=details.product_id WHERE sales_date='$today'", $conn, "sales.php", $admin['type'] <= 1);
        ?>
        <div class="col-lg-3 col-xs-6">
          <div class="small-box gradient-box1">
            <div class="inner">
              <?php
                $stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM sales");
                $stmt->execute();
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
                $stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM products");
                $stmt->execute();
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
          <div class="small-box gradient-box1">
            <div class="inner">
              <?php
                $stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM return_products");
                $stmt->execute();
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
      $stmt = $conn->prepare("SELECT * FROM details LEFT JOIN sales ON sales.id=details.sales_id LEFT JOIN products ON products.id=details.product_id WHERE MONTH(sales_date)=:month AND YEAR(sales_date)=:year");
      $stmt->execute(['month'=>$m, 'year'=>$year]);
      $total = 0;
      foreach($stmt as $srow){
        $subtotal = $srow['price']*$srow['quantity'];
        $total += $subtotal;    
      }
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
        fillColor           : 'rgba(60,141,188,0.9)',
        strokeColor         : 'rgba(60,141,188,0.8)',
        pointColor          : '#3b8bba',
        pointStrokeColor    : 'rgba(60,141,188,1)',
        pointHighlightFill  : '#fff',
        pointHighlightStroke: 'rgba(60,141,188,1)',
        data                : <?php echo $sales; ?>
      }
    ]
  }
  //barChartData.datasets[1].fillColor   = '#00a65a'
  //barChartData.datasets[1].strokeColor = '#00a65a'
  //barChartData.datasets[1].pointColor  = '#00a65a'
  var barChartOptions                  = {
    //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
    scaleBeginAtZero        : true,
    //Boolean - Whether grid lines are shown across the chart
    scaleShowGridLines      : true,
    //String - Colour of the grid lines
    scaleGridLineColor      : 'rgba(0,0,0,.05)',
    //Number - Width of the grid lines
    scaleGridLineWidth      : 1,
    //Boolean - Whether to show horizontal lines (except X axis)
    scaleShowHorizontalLines: true,
    //Boolean - Whether to show vertical lines (except Y axis)
    scaleShowVerticalLines  : true,
    //Boolean - If there is a stroke on each bar
    barShowStroke           : true,
    //Number - Pixel width of the bar stroke
    barStrokeWidth          : 2,
    //Number - Spacing between each of the X value sets
    barValueSpacing         : 5,
    //Number - Spacing between data sets within X values
    barDatasetSpacing       : 1,
    //String - A legend template
    legendTemplate          : '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<datasets.length; i++){%><li><span style="background-color:<%=datasets[i].fillColor%>"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>',
    //Boolean - whether to make the chart responsive
    responsive              : true,
    maintainAspectRatio     : true
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
