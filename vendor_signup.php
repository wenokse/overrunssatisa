<?php include 'includes/session.php'; ?>
<?php
  if(isset($_SESSION['user'])){
    header('location: cart_view');
  }
?>
<?php include 'includes/header.php'; ?>

<!-- Include SweetAlert -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
<br>
<div class="container">
    <a href="index" style="color: rgb(0, 51, 102);"><i class="fa fa-arrow-left"></i></a>
    <p class="login-box-msg" style="font-size: 30px; color: rgb(0, 51, 102);">
        <b>Create an account for Vendor</b>
    </p>

    <form action="vendor_register" method="POST" enctype="multipart/form-data" onsubmit="return validatePhoneNumber()">
        <div class="row">
            <!-- Left Column -->
            <div class="col-md-6">
                <div class="form-group has-feedback">
                    <input type="text" class="form-control" name="store" placeholder="Name of Store" required>
                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="text" class="form-control" name="firstname" placeholder="First Name" required>
                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="text" class="form-control" name="lastname" placeholder="Last Name" required>
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
           

            <!-- Right Column -->
           
                <div class="form-group has-feedback">
                    <input type="email" class="form-control" name="email" placeholder="Email" required>
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="password" class="form-control" name="password" placeholder="Password" required>
                </div>
                <div class="form-group has-feedback">
                    <input type="password" class="form-control" name="repassword" placeholder="Retype password" required>
                </div>
                </div>
                <div class="col-md-6">
                <div class="form-group">
                    <label>Shop Image <span style="color: red;">*</span></label>
                    <input type="file" id="photo" name="photo" accept="image/png, image/jpeg, image/jpg" required>
                    <img id="photo-preview" src="#" alt="Your Image" style="display:none; border-radius: 50%; width: 100px; height: 100px; margin-top: 10px;">
                </div>
                
                <div class="form-group">
                    <label>BIR Documents <span style="color: red;">*</span></label>
                    <input type="file" name="bir_doc" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                    <small class="text-muted">Upload your BIR Certificate of Registration (Form 2303)</small>
                </div>

                <div class="form-group">
                    <label>DTI Registration <span style="color: red;">*</span></label>
                    <input type="file" name="dti_doc" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                    <small class="text-muted">Upload your DTI Business Registration Certificate</small>
                </div>

                <div class="form-group">
                    <label>Mayor's Permit <span style="color: red;">*</span></label>
                    <input type="file" name="mayor_permit" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                    <small class="text-muted">Upload your valid Mayor's Permit</small>
                </div>

                <div class="form-group">
                    <label>Valid Government ID <span style="color: red;">*</span></label>
                    <input type="file" name="valid_id" class="form-control" accept="image/png, image/jpeg, image/jpg" required>
                    <small class="text-muted">Upload any valid government ID (e.g., Driver's License, Passport, UMID)</small>
                </div>

                <div class="form-group">
                    <label>TIN Number <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" name="tin_number" id="tin_number" placeholder="XXX-XXX-XXX-XXX" pattern="\d{3}-\d{3}-\d{3}-\d{3}" required>
                    <small class="text-muted">Format: XXX-XXX-XXX-XXX</small>
                </div>
            </div>
        </div>

        <!-- Bottom Section -->
        <div class="row">
            <div class="col-md-12 text-center">
                <div class="form-group has-feedback">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">I agree to the <a href="#" id="termsLink">Terms and Conditions</a></label>
                </div>
                <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block" name="signup" id="signupButton" disabled>
                        <i class="fa fa-pencil"></i> Sign Up
                    </button>
                </div>
                <p class="text-center" style="color: rgb(0, 51, 102);">
                    Already have an account? <a href="login">Login</a>
                </p>
            </div>
        </div>
    </form>
</div>
<script src="https://www.google.com/recaptcha/api.js?render=6Lf-VoIqAAAAAIXG5tzEBzI814o8JbZVs61dfiVk"></script>
<script>
grecaptcha.ready(function() {
    grecaptcha.execute('6Lf-VoIqAAAAAIXG5tzEBzI814o8JbZVs61dfiVk', { action: 'vendor_register' })
    .then(function(token) {
        document.getElementById('recaptchaResponse').value = token;
    });
});
</script>
</div>
<div id="termsModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h1>Vendor Terms and Conditions</h1>
        <div class="modal-body">
       
    
    <p>Welcome to Overruns Sa Tisa Online Shop! These Terms and Conditions govern the relationship between Overruns Sa Tisa Online Shop ("we," "us," or "our") and vendors ("you" or "your") who register to sell products on our platform. By signing up as a vendor, you agree to comply with these terms. If you do not agree with any part of these terms, you may not register as a vendor or use our platform.</p>

    <h3>1. Acceptance of Terms</h3>
    <p>By creating a vendor account, you affirm that you have a larravel store and you are at least 18 years old or that you are using the site with parental consent. You agree to abide by all applicable laws and regulations concerning your use of our platform.</p>

    <h3>2. Vendor Registration</h3>
    <p>To become a vendor, you must complete the registration process, providing accurate and complete information. You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account. Notify us immediately of any unauthorized use of your account.</p>

    <h3>3. Product Listings</h3>
    <p>As a vendor, you are responsible for creating and managing your product listings. All product descriptions, images, prices, and availability must be accurate and comply with applicable laws and regulations. You agree not to post any prohibited items as outlined in our prohibited items policy.</p>

    <h3>4. Fees and Payments</h3>
    <p>We may charge a commission or listing fee for the sale of products through our platform. The specific fees will be outlined in your vendor agreement. Payments to vendors will be processed after the customer has completed their order and any return period has expired.</p>

    <h3>5. Order Fulfillment</h3>
    <p>Vendors are responsible for fulfilling orders placed through their listings, including packaging, shipping, and handling customer service inquiries. You agree to ship products promptly and notify customers of tracking information when applicable.</p>

    <h3>6. Returns and Refunds</h3>
    <p>Vendors must adhere to our return policy. You agree to accept returns and provide refunds in accordance with our guidelines. All return requests must be processed through our platform.</p>

    <h3>7. Intellectual Property</h3>
    <p>You grant Overruns Sa Tisa Online Shop a non-exclusive, royalty-free license to use, reproduce, and display your product listings and any associated materials for the purpose of promoting and operating our platform.</p>

    <h3>8. Compliance with Laws</h3>
    <p>You agree to comply with all applicable laws, regulations, and third-party rights in connection with your use of our platform and the sale of your products. This includes, but is not limited to, consumer protection laws, trademark laws, and copyright laws.</p>

    <h3>9. Limitation of Liability</h3>
    <p>To the fullest extent permitted by law, Overruns Sa Tisa Online Shop shall not be liable for any indirect, incidental, or consequential damages arising from your use of our platform or any products sold. Our total liability to you for any claim shall not exceed the fees paid by you to us for the services rendered.</p>

    <h3>10. Termination</h3>
    <p>We reserve the right to suspend or terminate your vendor account at any time for violations of these terms or for any conduct that we deem inappropriate or harmful to the platform or its users.</p>

    <h3>11. Changes to Terms</h3>
    <p>We may update these Terms and Conditions at any time. Any changes will be posted on this page, and your continued use of the platform after any changes constitutes your acceptance of the new terms.</p>

    <h3>12. Contact Information</h3>
    <p>If you have any questions or concerns about these Terms and Conditions, please contact us at:</p>
    <p>
        Email: rowensecuya25@gmail.com<br>
       
    </p>
        </div>
    </div>
</div>
<?php include 'includes/scripts.php' ?>
<script>
    document.getElementById('photo').onchange = function(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var preview = document.getElementById('photo-preview');
            preview.src = reader.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(event.target.files[0]);
    };
</script>
<style>
   body {
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            background-size: cover;
            background-repeat: no-repeat;
        }

        .container {
            width: 850px;
            padding: 20px;
            margin: 0 auto 50px;
            border: 1px solid #ccc;
            border-radius: 20px;
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }

        /* Enhanced form styles */
        .form-group {
            margin-bottom: 15px;
            position: relative;
        }

        .form-control {
            background-color: #eee;
            border: none;
            padding: 7px 15px;
            padding-right: 40px; /* Space for the icon */
            font-size: 13px;
            border-radius: 10px;
            width: 100%;
            outline: none;
            box-sizing: border-box;
        }

        /* Password toggle button styling */
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: none;
            cursor: pointer;
            color: #666;
            padding: 0;
            font-size: 16px;
        }

        .password-toggle:hover {
            color: #333;
        }

        /* File input styling */
        .file-input-wrapper {
            position: relative;
            margin-bottom: 20px;
        }

        .file-input-wrapper input[type="file"] {
            padding: 10px;
            border: 2px dashed #ddd;
            border-radius: 8px;
            width: 100%;
            box-sizing: border-box;
        }

        .file-input-wrapper small {
            display: block;
            margin-top: 5px;
            color: #666;
        }

        /* Enhanced button styling */
        .btn-primary {
            background-color: rgb(0, 51, 102);
            color: #fff;
            padding: 12px 45px;
            border: none;
            border-radius: 25px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-top: 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: rgb(0, 71, 122);
        }

        .btn-primary:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        /* Terms checkbox styling */
        .terms-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin: 20px 0;
        }

        .terms-wrapper input[type="checkbox"] {
            width: 16px;
            height: 16px;
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
    /* General Mobile Styling */
@media only screen and (max-width: 768px) {
    body {
        background: rgb(0, 51, 102);
        font-size: 14px;
        padding: 0;
        margin: 0;
    }

    .container {
        width: 95%;
        padding: 10px;
        margin: 20px auto;
        border-radius: 10px;
        box-shadow: none;
    }

    .form-group {
        margin-bottom: 12px;
    }

    .form-control {
        padding: 10px;
        font-size: 14px;
    }

    /* Adjust button size for mobile */
    .btn-primary {
        padding: 10px 20px;
        font-size: 16px;
        width: 100%;
        border-radius: 20px;
    }

    /* Adjust checkbox and label */
    .terms-wrapper {
        flex-direction: column;
        gap: 5px;
        text-align: center;
    }

    /* Style file upload previews */
    #photo-preview {
        width: 80px;
        height: 80px;
    }

    /* Modal adjustments */
    .modal-content {
        width: 90%;
        max-width: 400px;
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-content h1 {
        font-size: 20px;
    }
}

/* For very small devices (like iPhone SE or similar) */
@media only screen and (max-width: 480px) {
    .btn-primary {
        font-size: 14px;
        padding: 8px 15px;
    }

    .form-control {
        padding: 8px;
        font-size: 13px;
    }

    .container {
        padding: 5px;
        border-radius: 5px;
    }
}

</style>
<script>
// Add this to your existing JavaScript
document.getElementById('tin_number').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 12) {
        value = value.slice(0, 12);
    }
    
    // Format as XXX-XXX-XXX-XXX
    if (value.length >= 3) {
        value = value.slice(0,3) + "-" + value.slice(3);
    }
    if (value.length >= 7) {
        value = value.slice(0,7) + "-" + value.slice(7);
    }
    if (value.length >= 11) {
        value = value.slice(0,11) + "-" + value.slice(11);
    }
    
    e.target.value = value;
});

// Add file size validation
const maxFileSize = 5 * 1024 * 1024; // 5MB
const fileInputs = document.querySelectorAll('input[type="file"]');

fileInputs.forEach(input => {
    input.addEventListener('change', function() {
        if (this.files[0] && this.files[0].size > maxFileSize) {
            swal({
                title: 'File too large',
                text: 'Please upload a file smaller than 5MB',
                icon: 'warning',
                button: 'OK'
            });
            this.value = '';
        }
    });
});
</script>

<script>
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
    document.getElementById('terms').addEventListener('change', function() {
        var signupButton = document.getElementById('signupButton');
        signupButton.disabled = !this.checked; // Enable button only if terms checkbox is checked
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

    document.addEventListener('DOMContentLoaded', function() {
            const passwordFields = document.querySelectorAll('input[type="password"]');
            
            passwordFields.forEach(field => {
                // Create toggle button
                const toggleBtn = document.createElement('button');
                toggleBtn.type = 'button';
                toggleBtn.className = 'password-toggle';
                toggleBtn.innerHTML = '<i class="fas fa-eye"></i>';
                
                // Insert toggle button after password field
                field.parentNode.appendChild(toggleBtn);
                
                // Toggle password visibility
                toggleBtn.addEventListener('click', function() {
                    const type = field.type === 'password' ? 'text' : 'password';
                    field.type = type;
                    this.innerHTML = type === 'password' ? 
                        '<i class="fas fa-eye"></i>' : 
                        '<i class="fas fa-eye-slash"></i>';
                });
            });
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


</script>

</body>

</html>
