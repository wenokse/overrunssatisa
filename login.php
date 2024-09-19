<?php include 'includes/session.php'; ?>
<?php
  if(isset($_SESSION['user'])){
    header('location: profile.php');
  }
?>
<?php include 'includes/header.php'; ?>
<script src="js/sweetalert.min.js"></script>
<body class="body">
<div class="">
<?php
  if(isset($_SESSION['error']) || isset($_SESSION['success'])){
    if(isset($_SESSION['error'])){
      $message = $_SESSION['error'];
      $icon = 'error';
    } else {
      $message = $_SESSION['success'];
      $icon = 'success';
    }
    echo "
      <script>
        document.addEventListener('DOMContentLoaded', function() {
          swal({
            title: '" . $message . "',
            icon: '" . $icon . "',
            button: 'OK'
          });
        });
      </script>
    ";
    unset($_SESSION['error']);
    unset($_SESSION['success']);
  }
?>

    <style>
      @keyframes slideInLeft {
    from {
        transform: translateX(-100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.animate__slideInLeft {
    animation-name: slideInLeft;
    animation-duration: 1s;
    animation-fill-mode: both;
    font-size: 40px;
    color: rgb(0, 51, 102);
}



.glitch {
  position: relative;
  font-size: 205px;
  font-weight: 700;
  line-height: 1.2;
  color: #fff;
  letter-spacing: 5px;
  z-index: 1;
  animation: shift 1s ease-in-out infinite alternate;
}

.glitch:before,
.glitch:after {
  display: block;
  content: attr(data-glitch);
  position: absolute;
  top: 0;
  left: 0;
  opacity: 0.8;
}

.glitch:before {
  animation: glitch 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94) both infinite;
  color: #8b00ff;
  z-index: -1;
}

.glitch:after {
  animation: glitch 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94) reverse both infinite;
  color: #00e571;
  z-index: -2;
    
}
.container2 { 
    width: 500px;
    height: 410px;
    margin: 0 auto 50px;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 20px;
    background-color: #f9f9f9;
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
}

/* Add these new styles for mobile responsiveness */
@media (max-width: 768px) {
    .container2 {
        width: 90%;
        height: auto;
        margin: 20px auto;
        padding: 15px;
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
    }

    .animate__slideInLeft {
        font-size: 28px;
    }

    .container2 input {
        font-size: 14px;
        padding: 8px 12px;
    }

    .container2 button {
        font-size: 14px;
        padding: 8px 30px;
    }

    .checkbox-wrapper-46 .cbx span:last-child {
        font-size: 14px;
    }

    .glitch {
        font-size: 100px;
    }

    body {
        background-size: cover;
        background-position: center;
    }
}

@media (max-width: 480px) {
    .container2 {
        width: 95%;
        padding: 10px;
    }

    .animate__slideInLeft {
        font-size: 24px;
    }

    .container2 input,
    .container2 button {
        font-size: 12px;
    }

    .checkbox-wrapper-46 .cbx span:last-child {
        font-size: 12px;
    }

    .glitch {
        font-size: 80px;
    }
}
  }
  .container2 input{
    background-color: #eee;
    border: none;
    margin: 8px 0;
    padding: 10px 15px;
    font-size: 13px;
    border-radius: 10px;
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
    margin-top: 10px;
    cursor: pointer;
}


  


    </style>
    <br><br><br><br><br><br>
  	<div class="container2">
    <a href="index.php" style="color: rgb(0, 51, 102); "><i class="fa fa-arrow-left" style="color: rgb(0, 51, 102);"></i></a></p>
    	<center><h2 class="animate__animated animate__slideInLeft"> Sign in to start your session </h2></center>

    	<form action="verify.php" method="POST">
      	<div class="form-group has-feedback">
        	<input type="email" class="form-control" name="email" placeholder="Email" required>
        	<span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      	</div>
        <div class="form-group has-feedback">
        <input type="password" class="form-control" name="password" id="passwordField" placeholder="Password" required>
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <div class="checkbox-wrapper-46">
            <input type="checkbox" id="cbx-46" class="inp-cbx" onclick="togglePassword()">
            <label for="cbx-46" class="cbx">
                <span>
                    <svg viewBox="0 0 12 10" height="10px" width="12px">
                        <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                    </svg>
                </span>
                <span style="color: rgb(0, 51, 102);">Show Password</span>
            </label>
        </div>
    </div>

    <script>
        function togglePassword() {
            var checkbox = document.getElementById("cbx-46");
            var passwordField = document.getElementById("passwordField");

           
            if (checkbox.checked) {
                passwordField.type = "text";
            } else {
               
                passwordField.type = "password";
            }
        }
    </script>
      <div class="form-group has-feedback">
    <button type="submit" class="btn btn-primary btn-block" name="login">
        <i class="fa fa-sign-in"></i> Sign In
    </button>
</div>
</form><br>
<a href="password_forgot.php">Forgot Password?</a><br>
<p style="color: rgb(0, 51, 102); ">Don't have an account? 
<a href="signup.php" class="text-center">Register</a><br>
</div>

</div>
	
<?php include 'includes/scripts.php' ?>
<style>
  body{
   
    background: rgb(0, 51, 102);
/* background-image: url('image/sliders1.jpg'); */
background-size: cover;
background-repeat: no-repeat;
}
  .checkbox-wrapper-46 input[type="checkbox"] {
  display: none;
  visibility: hidden;
}

.checkbox-wrapper-46 .cbx {
  margin: auto;
  -webkit-user-select: none;
  user-select: none;
  cursor: pointer;
}
.checkbox-wrapper-46 .cbx span {
  display: inline-block;
  vertical-align: middle;
  transform: translate3d(0, 0, 0);
}
.checkbox-wrapper-46 .cbx span:first-child {
  position: relative;
  width: 18px;
  height: 18px;
  border-radius: 3px;
  transform: scale(1);
  vertical-align: middle;
  border: 1px solid #9098a9;
  transition: all 0.2s ease;
}
.checkbox-wrapper-46 .cbx span:first-child svg {
  position: absolute;
  top: 3px;
  left: 2px;
  fill: none;
  stroke: #ffffff;
  stroke-width: 2;
  stroke-linecap: round;
  stroke-linejoin: round;
  stroke-dasharray: 16px;
  stroke-dashoffset: 16px;
  transition: all 0.3s ease;
  transition-delay: 0.1s;
  transform: translate3d(0, 0, 0);
}
.checkbox-wrapper-46 .cbx span:first-child:before {
  content: "";
  width: 100%;
  height: 100%;
  background: #506eec;
  display: block;
  transform: scale(0);
  opacity: 1;
  border-radius: 50%;
}
.checkbox-wrapper-46 .cbx span:last-child {
  padding-left: 8px;
}
.checkbox-wrapper-46 .cbx:hover span:first-child {
  border-color: #506eec;
}

.checkbox-wrapper-46 .inp-cbx:checked + .cbx span:first-child {
  background: #506eec;
  border-color: #506eec;
  animation: wave-46 0.4s ease;
}
.checkbox-wrapper-46 .inp-cbx:checked + .cbx span:first-child svg {
  stroke-dashoffset: 0;
}
.checkbox-wrapper-46 .inp-cbx:checked + .cbx span:first-child:before {
  transform: scale(3.5);
  opacity: 0;
  transition: all 0.6s ease;
}

@keyframes wave-46 {
  50% {
    transform: scale(0.9);
  }
}

p {
  text-align: justify;
}
  </style>
</body>
</html>