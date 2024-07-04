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
					<div class="box box-solid">
						<div class="box-body">
							<div class="box-header with-border">
								<i class="fa fa-map-marker"></i>
								<h3 class="box-title">P.Lozada St Binaobao, Bantayan, Cebu Philippines</h3>
							</div>
							<div class="box-header with-border">
								<i class="fa fa-phone"></i>
								<h3 class="box-title">+639565565090</h3>
							</div>
							<div class="box-header with-border">
								<i class="fa fa-envelope"></i>
								<h3 class="box-title"><a href="mailto:manilynalmohallas.miranda@gmail.com">manilynalmohallas.miranda@gmail.com</a></h3>
							</div>
						</div>
					</div>
					<!-- <div class="box box-solid"> -->
						<!-- <div class="box-body"> -->
						<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3935.187907224754!2d123.7156801!3d11.166587!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33a88903adb82363%3A0x6133fa0a7224a6!2sOverruns%20SA%20Tisa%20and%20Fashion%20Shop!5e0!3m2!1sen!2sph!4v1615687637855!5m2!1sen!2sph" 
								width="800" 
								height="500" 
								style="border:0;" 
								allowfullscreen="" 
								loading="lazy">
						</iframe>


						<!-- </div> -->
					<!-- </div> -->
	        	</div>
	        	<div class="col-sm-3">
	        		<?php include 'includes/sidebar.php'; ?>
	        	</div>
	        </div>
	      </section>
	     
	    </div>
	  </div>
  
  	<?php include 'includes/footer.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>
</body>
</html>