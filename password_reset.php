<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition login-page">
<div class="login-box">
  	<div class="login-logo">
  		<b>Reset Password</b>
  	</div>
  
  	<div class="login-box-body">
    	<p class="login-box-msg">Enter reset code and new password</p>

    	<form action="reset_password.php" method="POST">
      		<div class="form-group has-feedback">
        		<input type="email" class="form-control" name="email" placeholder="Email" required>
        		<span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      		</div>
      		<div class="form-group has-feedback">
        		<input type="text" class="form-control" name="reset_code" placeholder="Reset Code" required>
        		<span class="glyphicon glyphicon-lock form-control-feedback"></span>
      		</div>
      		<div class="form-group has-feedback">
        		<input type="password" class="form-control" name="password" placeholder="New Password" required>
        		<span class="glyphicon glyphicon-lock form-control-feedback"></span>
      		</div>
      		<div class="row">
    			<div class="col-xs-4">
          			<button type="submit" class="btn btn-primary btn-block btn-flat" name="reset"><i class="fa fa-check-square-o"></i> Reset</button>
        		</div>
      		</div>
    	</form>
    	<br>
    	<a href="login.php">I remembered my password</a><br>
    	<a href="index.php"><i class="fa fa-home"></i> Home</a>
  	</div>
</div>
	
<?php include 'includes/scripts.php' ?>
</body>
</html>