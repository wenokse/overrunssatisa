<?php include 'includes/session.php'; ?>
<?php
  if(isset($_SESSION['user'])){
    header('location: profile');
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
    backdrop-filter: blur(10px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
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


.form-control {
    width: 100%;
    border: none;
    background-color: #f0f0f0;
    border-radius: 8px;
    font-size: 16px;
    transition: all 0.3s ease;
}
  


    </style>
    <br><br><br><br><br><br>
  	<div class="container2">
    <a href="index" style="color: rgb(0, 51, 102);"><i class="fa fa-arrow-left" style="color: rgb(0, 51, 102);"></i></a>
    <center><h2 class="animate__animated animate__slideInLeft">Welcome Back</h2></center><br><br>

    <form action="verify" method="POST">
        <div class="form-group has-feedback">
            <input type="email" class="form-control" name="email" placeholder="Email" required>
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback password-container">
            <input type="password" class="form-control" name="password" id="passwordField" placeholder="Password" required>
            <span class="password-toggle" onclick="togglePassword()">
                <i class="fa fa-eye" id="toggleIcon"></i>
            </span>
        </div>
        <div class="terms-checkbox">
        <input type="checkbox" id="termsCheck" name="terms" required>
                <label for="termsCheck">I agree to the <span class="terms-link" onclick="openTerms()">Terms and Conditions</span></label>
                <div class="error-message" id="termsError">Please accept the terms and conditions to continue</div>
            </div>
            <input type="hidden" id="recaptchaToken" name="g-recaptcha-response">
        <div class="form-group has-feedback">
        
            <button type="submit" class="btn btn-primary btn-block" name="login">
                <i class="fa fa-sign-in"></i> Sign In
            </button>
        </div>
    </form>
    <a href="password_forgot">Forgot Password?</a><br>
    <p style="color: rgb(0, 51, 102);">Don't have an account? 
        <a href="signup" class="text-center">Register</a>
    </p>
</div>
<!-- Terms and Conditions Modal -->
<div id="termsModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeTerms()">&times;</span>
            <h2>Terms and Conditions</h2>
            <div class="terms-content">
                <h3>1. Introduction</h3>
                <p>Welcome to our e-commerce platform. By accessing or using our website, you agree to these terms and conditions.</p>

                <h3>2. Account Security</h3>
                <p>You are responsible for maintaining the confidentiality of your account credentials and for all activities under your account.</p>

                <h3>3. Privacy Policy</h3>
                <p>Your use of our platform is also governed by our Privacy Policy. By using our services, you consent to our collection and use of your data as described therein.</p>

                <h3>4. Prohibited Activities</h3>
                <p>You agree not to:</p>
                <ul>
                    <li>Use the platform for any illegal purposes</li>
                    <li>Attempt to gain unauthorized access to other user accounts</li>
                    <li>Upload malicious content or viruses</li>
                    <li>Engage in fraudulent activities</li>
                </ul>

                <h3>5. Termination</h3>
                <p>We reserve the right to terminate or suspend your account for violations of these terms.</p>
            </div>
        </div>
    </div>
    <script src="https://www.google.com/recaptcha/api.js?render=6Lf-VoIqAAAAALGiTwK15qjAKTRD6Kv8al322Apf"></script>
<script>
    function loadRecaptcha() {
        grecaptcha.ready(function() {
            // Generate a token and assign it to the hidden field
            grecaptcha.execute('6Lf-VoIqAAAAALGiTwK15qjAKTRD6Kv8al322Apf', { action: 'login' }).then(function(token) {
                document.getElementById('recaptchaToken').value = token;
            });
        });
    }

    // Refresh token every 2 minutes
    setInterval(loadRecaptcha, 120000);

    // Initial token generation
    loadRecaptcha();
</script>

<style>
 .password-container {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: rgb(0, 51, 102);
        }

        .password-toggle:hover {
            color: #004080;
        }

        /* Terms Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            border-radius: 5px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: black;
        }

        .terms-checkbox {
            margin: 15px 0;
        }

        .terms-link {
            color: rgb(0, 51, 102);
            text-decoration: underline;
            cursor: pointer;
        }

        .error-message {
            color: red;
            display: none;
            margin-top: 5px;
            font-size: 0.9em;
        }
</style>

<script>
  function openTerms() {
            document.getElementById("termsModal").style.display = "block";
        }

        function closeTerms() {
            document.getElementById("termsModal").style.display = "none";
        }

        function validateForm() {
            const termsCheck = document.getElementById("termsCheck");
            const termsError = document.getElementById("termsError");

            if (!termsCheck.checked) {
                termsError.style.display = "block";
                return false;
            }
            termsError.style.display = "none";
            return true;
        }

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById("termsModal");
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
function togglePassword() {
    const passwordField = document.getElementById("passwordField");
    const toggleIcon = document.getElementById("toggleIcon");
    
    if (passwordField.type === "password") {
        passwordField.type = "text";
        toggleIcon.classList.remove("fa-eye");
        toggleIcon.classList.add("fa-eye-slash");
    } else {
        passwordField.type = "password";
        toggleIcon.classList.remove("fa-eye-slash");
        toggleIcon.classList.add("fa-eye");
    }
}
</script>
	
<?php include 'includes/scripts.php' ?>
<style>
  body{
   
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #6e8efb, #a777e3);
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