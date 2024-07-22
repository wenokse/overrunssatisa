<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition login-page">
<div class="login-box">
  	<div class="login-logo">
  		<b>Forgot Password</b>
  	</div>
  
  	<div class="login-box-body">
    	<p class="login-box-msg">Enter email associated with account</p>

    	<form action="forgot_password.php" method="POST">
      		<div class="form-group has-feedback">
        		<input type="email" class="form-control" name="email" placeholder="Email" required>
        		<span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      		</div>
      		<div class="row">
    			<div class="col-xs-4">
          			<button type="submit" class="btn btn-primary btn-block btn-flat" name="forgot"><i class="fa fa-mail-forward"></i> Send</button>
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