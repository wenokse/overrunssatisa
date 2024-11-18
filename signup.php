<?php include 'includes/session.php'; ?>
<?php
  if(isset($_SESSION['user'])){
    header('location: cart_view');
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
            background: linear-gradient(135deg, #6e8efb, #a777e3);
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

    <form action="register" method="POST" onsubmit="return validatePhoneNumber()">
        <div class="form-group has-feedback">
            <input type="text" class="form-control" name="firstname" placeholder="Firstname" required>
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
            <input type="text" class="form-control" name="lastname" placeholder="Lastname" required>
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
            <input type="text" class="form-control" name="contact_info" id="contact_info" placeholder="Phone Number" value="09" maxlength="11" required>
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
            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
            <div id="password-strength" style="margin-top: 5px; height: 3px; width: 100%; background-color: red;"></div>
            <input type="checkbox" id="togglePassword" class="form-control-feedback1">
        </div>

        <div class="form-group has-feedback">
            <input type="password" class="form-control" name="repassword" placeholder="Retype password" required>
            <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
            <input type="checkbox" id="terms" name="terms" class="form-control-feedback1" required>
            <label for="terms">I agree to the <a href="#" id="termsLink">Terms and Conditions</a></label>
        </div>
       
        <div class="form-group has-feedback">
        <input type="hidden" id="recaptchaToken" name="g-recaptcha-response">
            <button type="submit" class="btn btn-primary btn-block" name="signup" id="signupButton" disabled><i class="fa fa-pencil"></i> Sign Up</button>
        </div>
      
    </form>
    <br>
    
    <p class="text-center" style="color: rgb(0, 51, 102); ">Already have an account? <a href="login">Login</a></p>
    <p class="text-center" style="color: rgb(0, 51, 102); ">You have a Store? <a href="vendor_signup">Signup as Vendor</a></p>
</div>

</div>

<div id="termsModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Terms and Conditions</h2>
        <div class="modal-body">
            <h3>1. Introduction</h3>
            <p>This website is operated by Rowen G. Secuya & Teams. Throughout the site, the terms "we", "us" and "our" refer to Overruns Sa Tisa (Main Land). We offer this website, including all information, tools, and services available from this site, to you, the user, conditioned upon your acceptance of all terms, conditions, policies, and notices stated here.</p>
            <h3>2. User Accounts</h3>
            <p>You may be required to create an account to access certain features of the site. You agree to provide accurate, current, and complete information during registration and update your information as necessary. You are responsible for safeguarding your password and for any activities or actions under your account.</p>

            <h3>3. Products and Services</h3>
            <p>We strive to provide accurate descriptions of our products, but we do not guarantee that any description is complete, current, or free of errors. Product availability and prices are subject to change without notice.</p>

            <h3>4. Orders and Payments</h3>
            <p>All prices are in [Currency]. We reserve the right to refuse or cancel any order due to pricing errors, stock issues, or potential fraud. Payment must be completed before the shipment of goods.</p>

            <h3>5. Returns and Refunds</h3>
                <p>If the item you receive does not match the description or specifications of the ordered item, you may return it within 3 to 4 days of receipt. However, please ensure the following:</p>
                <ul>
                    <li>The product is in its original condition and packaging.</li>
                    <li>You provide a video recording as proof, clearly showing the item as it was received, including the unboxing, so we can verify any discrepancies.</li>
                    <li>Opened items or items that show signs of use may not be eligible for return or refund.</li>
                </ul>
                <p>Please note that shipping fees will be covered by the customer and are non-refundable. If the return request meets these criteria, we will process the return and issue a refund or replacement as appropriate.</p>

            <h3>6. Pricing </h3>
            <p>Prices listed on our site are subject to change without notice.</p>

            <h3>7. User Conduct</h3>
            <p>Users agree to use the site only for lawful purposes and in a manner that does not infringe on the rights of others or restrict the use of the site. Prohibited activities include, but are not limited to, harassment, defamation, and uploading viruses or harmful code.</p>

            <h3>8. Intellectual Property</h3>
            <p>All content on this site, including text, images, logos, and designs, are owned by or licensed to Overruns Sa Tisa Online Shop and are protected by intellectual property laws. Unauthorized use of this content is prohibited.</p>

            <h3>9. Limitation of Liability</h3>
            <p>We do not warrant that the use of our service will be uninterrupted, timely, or error-free. In no case shall Overruns Sa Tisa Online Shop shall be liable for any injury, loss, claim, or any direct or indirect damages resulting from your use of our website.</p>

            <h3>10. Governing Law</h3>
            <p>These Terms and any separate agreements shall be governed by and construed in accordance with the laws of Philippines.</p>

            <h3>11. Amendments</h3>
            <p>We reserve the right to modify these terms at any time. Changes will be effective immediately upon posting on the website.</p>

            <h3>12. Contact Information</h3>
            <p>If you have any questions about these Terms and Conditions, please contact us at rowensecuya25@gmail.com.</p>
        </div>
    </div>
</div>

<script src="https://www.google.com/recaptcha/api.js?render=6Lf-VoIqAAAAALGiTwK15qjAKTRD6Kv8al322Apf"></script>
<script>
    // Render reCAPTCHA v3 and get the token
    grecaptcha.ready(function() {
        grecaptcha.execute('6Lf-VoIqAAAAALGiTwK15qjAKTRD6Kv8al322Apf', { action: 'register' }).then(function(token) {
            // Attach token to a hidden input in your form
            document.getElementById('recaptchaToken').value = token;
        });
    });
</script>
<script>
    document.getElementById('terms').addEventListener('change', function() {
        var signupButton = document.getElementById('signupButton');
        signupButton.disabled = !this.checked; // Enable button only if terms checkbox is checked
    });
</script>
<?php include 'includes/scripts.php' ?>

<script>
   function containsSpecialCharacters(str) {
        var regex = /[<>:\/\$\;\,\?\!]/;
        return regex.test(str);
    }

    function isFieldEmptyOrWhitespace(fieldValue) {
        return !fieldValue.trim(); 
    }

    function validatePassword(password) {
        var regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/;
        return regex.test(password); 
    }

    function validateForm() {
        var firstname = document.querySelector('input[name="firstname"]').value;
        var lastname = document.querySelector('input[name="lastname"]').value;
        var email = document.querySelector('input[name="email"]').value;
        var password = document.querySelector('input[name="password"]').value;

        
        if (isFieldEmptyOrWhitespace(firstname) || isFieldEmptyOrWhitespace(lastname) || isFieldEmptyOrWhitespace(email) || isFieldEmptyOrWhitespace(password)) {
            swal({
                title: 'All fields must be filled out correctly.',
                icon: 'warning',
                button: 'OK'
            });
            return false;
        }

       
        if (!validatePhoneNumber()) {
            return false;
        }

        
        if (!validatePassword(password)) {
            swal({
                title: 'Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, and one number.',
                icon: 'warning',
                button: 'OK'
            });
            return false;
        }

        
        if (containsSpecialCharacters(firstname) || containsSpecialCharacters(lastname) || containsSpecialCharacters(email)) {
            swal({
                title: 'Special characters are not allowed.',
                icon: 'warning',
                button: 'OK'
            });
            return false;
        }

        return true;
    }

   
    document.querySelector('form').addEventListener('submit', function(event) {
        if (!validateForm()) {
            event.preventDefault();
        }
    });


    document.getElementById('contact_info').addEventListener('input', function() {
    // Ensure that input starts with '09'
    if (!this.value.startsWith('09')) {
        this.value = '09';
    }

    // Allow only numbers and limit to 11 characters
    this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11);
});

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

    // Password strength indicator
const passwordField = document.querySelector('input[name="password"]');
const passwordStrengthBar = document.getElementById('password-strength');

passwordField.addEventListener('input', function() {
    const password = passwordField.value;
    let strength = 0;

    // Check for different conditions
    if (/[a-z]/.test(password)) strength++; // Lowercase letter
    if (/[A-Z]/.test(password)) strength++; // Uppercase letter
    if (/[0-9]/.test(password)) strength++; // Number
    if (password.length >= 8) strength++;   // Length

    // Update the strength bar color based on the strength
    switch(strength) {
        case 0:
        case 1:
            passwordStrengthBar.style.backgroundColor = 'red'; // Weak
            break;
        case 2:
            passwordStrengthBar.style.backgroundColor = 'orange'; // Medium
            break;
        case 3:
            passwordStrengthBar.style.backgroundColor = 'yellow'; // Stronger
            break;
        case 4:
            passwordStrengthBar.style.backgroundColor = '#39FF14'; // Strong (neon green)
            break;
    }
});

// Toggle password visibility
const togglePassword = document.querySelector('#togglePassword');
togglePassword.addEventListener('click', function() {
    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordField.setAttribute('type', type);
});


    const barangays = {
        'Bantayan': ['Atop-atop', 'Baigad', 'Bantigue', 'Baod', 'Binaobao', 'Guiwanon', 'Kabac', 'Kabangbang', 'Kampingganon', 'Kangkaibe', 'Mojon', 'Obo-ob', 'Patao', 'Putian', 'Sillon', 'Suba', 'Sulangan', 'Sungko', 'Tamiao', 'Ticad'],
        'Madridejos': ['Bunakan', 'Kangwayan', 'Kaongkod', 'Kodia', 'Maalat', 'Malbago', 'Mancilang', 'Pili', 'Poblacion', 'San Agustin', 'Talangnan', 'Tarong', 'Tugas', 'Tabagak'],
        'Santa Fe': ['Balidbid', 'Hagdan', 'Langub', 'Maricaban', 'Okoy', 'Poblacion', 'Talisay']
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
    document.querySelector('form').addEventListener('submit', function(event) {
    var email = document.querySelector('input[name="email"]').value;
    
    // Check if the email is empty
    if (email === '') {
        swal({
            title: 'Email is required',
            icon: 'warning',
            button: 'OK'
        });
        event.preventDefault(); // Prevent form submission
        return false;
    }

    // Check if email ends with @gmail.com
    if (!email.endsWith('@gmail.com')) {
        swal({
            title: 'Email must be a @gmail.com address',
            icon: 'warning',
            button: 'OK'
        });
        event.preventDefault(); // Prevent form submission
        return false;
    }

    
});
 // Modal script
 var modal = document.getElementById("termsModal");
    var link = document.getElementById("termsLink");
    var span = document.getElementsByClassName("close")[0];

    link.onclick = function(e) {
        e.preventDefault();
        modal.style.display = "block";
    }

    span.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

</script>
<style>
      body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #6e8efb, #a777e3);
        background-size: cover;
        background-repeat: no-repeat;
    }

    
    .container {
        width: 500px;
        height: 1000px;
        margin: 0 auto 50px;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 20px;
        background: linear-gradient(135deg, #6e8efb, #a777e3);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
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

    body {
        background: linear-gradient(135deg, #6e8efb, #a777e3);
        background-size: cover;
        background-repeat: no-repeat;
    }

    .container {
        width: 500px;
        height: 940px;
        margin: 0 auto 50px;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 20px;
        background-color: #f9f9f9;
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
    }

  

    /* Mobile view styles */
    @media (max-width: 768px) {
        .container {
            width: 90%;
            height: auto;
            margin: 20px auto;
            padding: 15px;
        }

        .login-box-msg {
            font-size: 24px;
        }

        .form-control {
            font-size: 14px;
            padding: 8px 12px;
        }

        .btn {
            font-size: 14px;
            padding: 8px 30px;
        }

        .form-control-feedback1 {
           position: absolute;
        right: -142px;
        top: 45%;
        transform: translateY(-100%);
        line-height: 46px;
        }
    }

    @media (max-width: 480px) {
        .container {
            width: 95%;
            padding: 10px;
        }

        .login-box-msg {
            font-size: 20px;
        }

        .form-control,
        .btn {
            font-size: 12px;
        }

        .form-group {
            margin-bottom: 10px;
        }
    }

    /* Adjustments for the preloader */
    @media (max-width: 768px) {
        .loader {
            width: 80px;
            height: 80px;
            border-width: 12px;
        }
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
    /* Modal styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.4);
    }

    .modal-content {
        background-color: #fefefe;
        margin: 5% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 700px;
        max-height: 80vh;
        overflow-y: auto;
        border-radius: 25px;
    }

    .close {
        color: black;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
    .modal-content h2 {
        margin-top: 0;
        padding-bottom: 10px;
        border-bottom: 1px solid #ddd;
    }

    .modal-body {
        margin-top: 20px;
    }
    
</style>

</body>

</html>
