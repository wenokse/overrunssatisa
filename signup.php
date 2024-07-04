<?php include 'includes/session.php'; ?>
<?php
  if(isset($_SESSION['user'])){
    header('location: cart_view.php');
  }
?>
<?php include 'includes/header.php'; ?>

<!-- Include SweetAlert -->
<script src="js/sweetalert.min.js"></script>
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
            background: rgb(0, 51, 102);
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
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
</style>
<script>
    window.addEventListener('load', function() {
        var preloader = document.getElementById('preloader');
        preloader.style.display = 'none';
    });
</script>
<body>
  
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
<br><br>
<div class="container">
    <p class="login-box-msg" style="font-size: 30px; color: rgb(0, 51, 102);">
        <b>Create an account</b></p>

    <form action="register.php" method="POST" onsubmit="return validatePhoneNumber()">
        <div class="form-group has-feedback">
            <input type="text" class="form-control" name="firstname" placeholder="Firstname" required>
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
            <input type="text" class="form-control" name="lastname" placeholder="Lastname" required>
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
            <input type="text" class="form-control" name="contact_info" id="contact_info" placeholder="Phone Number" maxlength="11" required>
            <span class="glyphicon glyphicon-phone form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
            <label for="municipality">Municipality</label>
            <select id="municipality" class="form-control" name="municipality" required>
                <option value="">Select Municipality</option>
                <option value="Bantayan">Bantayan</option>
                <option value="Madridejos">Madridejos</option>
                <option value="Santa Fe">Santa Fe</option>
            </select>
        </div>
        <div class="form-group has-feedback">
            <label for="barangay">Barangay</label>
            <select id="barangay" class="form-control" name="barangay" required>
                <option value="">Select Barangay</option>
            </select>
        </div>
        <div class="form-group has-feedback">
            <input type="text" class="form-control" name="address" id="address" placeholder="Address" required readonly>
            <span class="fa fa-address-book form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
            <input type="text" class="form-control" name="address2" placeholder="Purok" required>
            <span class="glyphicon glyphicon-book form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
            <input type="email" class="form-control" name="email" placeholder="Email" required>
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
            <input type="password" class="form-control" name="password" placeholder="Password" required>
            <input type="checkbox" id="togglePassword" class="form-control-feedback1">
        </div>
        <div class="form-group has-feedback">
            <input type="password" class="form-control" name="repassword" placeholder="Retype password" required>
            <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
        </div>

      
        <div class="form-group has-feedback">
            <button type="submit" class="btn btn-primary btn-block" name="signup"><i class="fa fa-pencil"></i> Sign Up</button>
        </div>

    </form>
    <br>
    <p class="text-center" style="color: rgb(0, 51, 102); ">Already have an account? <a href="login.php">Login</a></p>
</div>

</div>

<?php include 'includes/scripts.php' ?>
<style>
    body {
        background: rgb(0, 51, 102);
        background-size: cover;
        background-repeat: no-repeat;
    }

    .larg {
        font-size: 1.2em;
    }

    .form-control-feedback1 {
        position: absolute;
        right: -215px;
        top: 45%;
        transform: translateY(-100%);
        line-height: 46px;
    }

    .container {
        width: 500px;
        height: 770px;
        margin: 0 auto 50px;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 20px;
        background-color: #f9f9f9;
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
    }

    .container input {
        background-color: #eee;
        border: none;
        margin: 8px 0;
        padding: 10px 15px;
        font-size: 13px;
        border-radius: 10px;
        width: 100%;
        outline: none;
    }

    .container button {
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

<script>
    var input = document.getElementById('contact_info');
    input.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    function validatePhoneNumber() {
        var phoneNumber = document.getElementById('contact_info').value;
        if (phoneNumber.length !== 11) {
            swal({
                title: 'Phone number must be exactly 11 digits long.',
                icon: 'warning',
                button: 'OK'
            });
            return false;
        }
        return true;
    }

    const togglePassword = document.querySelector('#togglePassword');
    const passwordField = document.querySelector('input[name="password"]');

    togglePassword.addEventListener('click', function(e) {
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);
    });

    const barangays = {
        'Bantayan': ['Atop-atop', 'Baigad', 'Bantigue', 'Baod', 'Binaobao', 'Guiwanon', 'Hilotongan', 'Kabac', 'Kabangbang', 'Kampingganon', 'Kangkaibe', 'Lipayran', 'Luyongbaybay', 'Mojon', 'Obo-ob', 'Patao', 'Putian', 'Sillon', 'Suba', 'Sulangan', 'Sungko', 'Tamiao', 'Ticad'],
        'Madridejos': ['Bunakan', 'Kangwayan', 'Kaongkod', 'Kodia', 'Maalat', 'Malbago', 'Mancilang', 'Pili', 'Poblacion', 'San Agustin', 'Talangnan', 'Tarong', 'Tugas', 'Tabagak'],
        'Santa Fe': ['Balidbid', 'Hagdan', 'Hilantagaan', 'Kinatarkan', 'Langub', 'Maricaban', 'Okoy', 'Poblacion', 'Pooc', 'Talisay']
    };

    const municipalitySelect = document.getElementById('municipality');
    const barangaySelect = document.getElementById('barangay');
    const addressInput = document.getElementById('address');

    municipalitySelect.addEventListener('change', function() {
        const selectedMunicipality = this.value;
        barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
        
        if (barangays[selectedMunicipality]) {
            barangays[selectedMunicipality].forEach(function(barangay) {
                const option = document.createElement('option');
                option.value = barangay;
                option.textContent = barangay;
                barangaySelect.appendChild(option);
            });
        }
    });

    barangaySelect.addEventListener('change', function() {
        const selectedMunicipality = municipalitySelect.value;
        const selectedBarangay = this.value;
        addressInput.value = selectedMunicipality + ', ' + selectedBarangay;
    });
</script>

</body>

</html>
