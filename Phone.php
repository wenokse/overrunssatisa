<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="body">
    <div class="login-box">
        <br><br><br><br><br>
        <div class="login-box-body">
            <p class="login-box-msg" style="font-size: 20px; color: yellow;"><b>Enter your phone number:</b></p>
            
            <form action="very" method="POST">
                <div class="form-group has-feedback">
                    <input type="text" class="form-control" name="contact_info" id="contact_info" placeholder="Phone Number" maxlength="11" required>
                    <span class="glyphicon glyphicon-phone form-control-feedback"></span><br>
                    <button type="submit" class="btn btn-primary btn-block btn-flat" name="login"><i class="fa fa-sign-in"></i> Continue</button>
                </div>
            </form>
            <a href="login">I remembered my password</a><br>
            <a href="index"><i class="fa fa-home"></i> Home</a>
        </div>
    </div>
    <?php include 'includes/scripts.php' ?>
    <style>
        .body{
            background-image: url('image/sliders1.jpg');
            background-size: cover;
            background-repeat: no-repeat;
        }
    </style>
    <script>
        var input = document.getElementById('contact_info');
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, ''); 
        });
    </script>
</body>
</html>
