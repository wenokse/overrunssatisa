<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<script src="js/sweetalert.min.js"></script>
<body class="body">
<div class="login-box">
<?php
  if (isset($_SESSION['error']) || isset($_SESSION['success'])) {
    $message = isset($_SESSION['error']) ? $_SESSION['error'] : $_SESSION['success'];
    $icon = isset($_SESSION['error']) ? 'error' : 'success';
    echo "
      <script src='js/sweetalert.min.js'></script>
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
    <br><br><br><br><br>
  	<div class="login-box-body">
    	<h1 class="login-box-msg" style="font-size: 20px; color: yellow;"><b>Enter your new password</b></h1>

    	<form action="" method="POST">
      		<div class="form-group has-feedback">
        		<input type="password" id="password" class="form-control" name="password" placeholder="New Password" required>
        		<span class="glyphicon glyphicon-lock form-control-feedback"></span><br>
                <div class="form-group has-feedback">
            <input type="password" class="form-control" name="repassword" placeholder="Retype password"  required>
            <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
          </div>
        </div>
          		
    	</form>
     <br>
      <a href="login.php">I remembered my password</a><br>
      <a href="index.php"><i class="fa fa-home"></i> Home</a>
    </div>
    </div>
  	</div>
</div>
	
<?php include 'includes/scripts.php' ?>
<style>
  .body{
    background: rgb(0, 51, 102);
    background-size: cover;
    background-repeat: no-repeat;
  }
  </style>
</body>
</html>