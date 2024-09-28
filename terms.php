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

    body {
        background: rgb(0, 51, 102);
        background-size: cover;
        background-repeat: no-repeat;
    }

    .container {
        width: 80%;
        margin: 0 auto 50px;
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 10px;
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
    }

    h1, p {
        color: #333;
    }
</style>

<script>
    window.addEventListener('load', function() {
        var preloader = document.getElementById('preloader');
        preloader.style.display = 'none';
    });
</script>

<div class="container">
    <h1>Terms and Conditions</h1>

    <p><b>Effective Date:</b> [Insert Date]</p>

    <p>Welcome to <b>overrunssatisa.com</b> ("the Website"). By accessing or using this Website, you agree to comply with and be bound by the following terms and conditions of use. If you do not agree to these terms, please do not use this Website.</p>

    <h2>1. Acceptance of Terms</h2>
    <p>By accessing or using <b>overrunssatisa.com</b>, you acknowledge that you have read, understood, and agree to be bound by these Terms and Conditions of Use.</p>

    <h2>2. Use of the Website</h2>
    <p>You may use the Website for lawful purposes only. You agree not to:</p>
    <ul>
        <li>Violate any laws, regulations, or international treaties.</li>
        <li>Engage in any activity that could harm the Website's functionality or security.</li>
        <li>Use automated systems without prior written permission.</li>
    </ul>

    <h2>3. Intellectual Property</h2>
    <p>All content on this Website is the property of <b>overrunssatisa.com</b> and its licensors and is protected by copyright laws. Unauthorized use of any materials is prohibited.</p>

    <h2>4. Limitation of Liability</h2>
    <p><b>overrunssatisa.com</b> will not be liable for any damages arising from your use of the Website.</p>

    <h2>5. Governing Law</h2>
    <p>These terms are governed by the laws of [Insert Location].</p>

    <h2>6. Contact Us</h2>
    <p>If you have any questions about these Terms and Conditions, please contact us at:</p>
    <p>Email: rowensecuya25@gmail.com<br></p>
</div>

<?php include 'includes/scripts.php'; ?>
