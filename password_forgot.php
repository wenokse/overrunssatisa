<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<div id="preloader">
        <div class="loader"></div>
    </div>
    <style>
        
        /* Preloader styles */
        #preloader {
            position: fixed;
            left: 0;
            top: 0;
            z-index: 999;
            width: 100%;
            height: 100%;
            overflow: visible;
            background:rgb(0, 51, 102);
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
            animation: spin 1s linear infinite; /* Adjusted duration to 2s */
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
            opacity: 3;
        }
    </style>
<body class="body">

  	
    <br><br><br><br><br>
  	<div class="container2">
    <a href="login.php" style="color: rgb(0, 51, 102); "><i class="fa fa-arrow-left" style="color: rgb(0, 51, 102);"></i></a></p>
    	<h1 class="login-box-msg" style="font-size: 20px; color:  rgb(0, 51, 102);"><b>Enter email associated with account</b></h1>

    	<form action="reset.php" method="POST">
      		<div class="form-group has-feedback">
        		<input type="email" id="email" class="form-control" name="email" placeholder="Email" required>
        		<span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      	
      	<br>
          <button type="submit" class="btn btn-primary btn-block btn-flat" name="login"><i class="fa fa-sign-in"></i> Send</button>
        	
      		
    	</form>
      <br>
  	</div>
</div>

	
<?php include 'includes/scripts.php' ?>
<style>
  .body{
    background: rgb(0, 51, 102);
    background-size: cover;
    background-repeat: no-repeat;
  }
  .container2 { 
    width: 500px;
    height: 260px;
    margin: 0 auto 50px;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 10px;
    background-color: #f9f9f9;
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
    
  }
  .container2 input{
    background-color: #eee;
    border: none;
    margin: 8px 0;
    padding: 10px 15px;
    font-size: 13px;
    border-radius: 8px;
    width: 100%;
    outline: none;
}
.container2 button{
    background-color: #512da8;
    color: #fff;
    font-size: 12px;
    padding: 10px 45px;
    border: 1px solid transparent;
    border-radius: 20px;
    font-weight: 600;
    letter-spacing: 0.5px;
    text-transform: uppercase;
   
    cursor: pointer;
}

  
  </style>
  <script>
    window.addEventListener('load', function() {
        var preloader = document.getElementById('preloader');
        preloader.style.display = 'none';
    });
</script>
</body>
</html>