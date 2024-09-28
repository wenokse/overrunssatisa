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
        width: 500px;
        height: 960px;
        margin: 0 auto 50px;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 20px;
        background-color: #f9f9f9;
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
<a href="signup.php" style="color: rgb(0, 51, 102); "><i class="fa fa-arrow-left" style="color: rgb(0, 51, 102);"></i></a></p><br><br>
<h2>Terms and Conditions</h2>
<p>Welcome to Overruns Sa Tisa Online Shop. By using our website and services, you agree to the following terms and conditions.</p>

<h3>1. Introduction</h3>
<p>This website is operated by Rowen G. Secuya. Throughout the site, the terms "we", "us" and "our" refer to Overruns Sa Tisa. We offer this website, including all information, tools, and services available from this site, to you, the user, conditioned upon your acceptance of all terms, conditions, policies, and notices stated here.</p>

<h3>2. User Accounts</h3>
<p>You may be required to create an account to access certain features of the site. You agree to provide accurate, current, and complete information during registration and update your information as necessary. You are responsible for safeguarding your password and for any activities or actions under your account.</p>

<h3>3. Products and Services</h3>
<p>We strive to provide accurate descriptions of our products, but we do not guarantee that any description is complete, current, or free of errors. Product availability and prices are subject to change without notice.</p>

<h3>4. Orders and Payments</h3>
<p>All prices are in [Currency]. We reserve the right to refuse or cancel any order due to pricing errors, stock issues, or potential fraud. Payment must be completed before the shipment of goods.</p>

<h3>5. Returns and Refunds</h3>
<p>If you are not satisfied with your purchase, you may return the item within [X] days of receiving it, but you still pay the shipping fee for the rider, provided it is in its original condition. [Additional return policy details].</p>

<h3>6. Pricing </h3>
<p>Prices listed on our site are subject to change without notice.</p>

<h3>7. User Conduct</h3>
<p>Users agree to use the site only for lawful purposes and in a manner that does not infringe on the rights of others or restrict the use of the site. Prohibited activities include, but are not limited to, harassment, defamation, and uploading viruses or harmful code.</p>

<h3>8. Intellectual Property</h3>
<p>All content on this site, including text, images, logos, and designs, are owned by or licensed to Overruns Sa Tisa Online Shop and are protected by intellectual property laws. Unauthorized use of this content is prohibited.</p>

<h3>9. Limitation of Liability</h3>
<p>We do not warrant that the use of our service will be uninterrupted, timely, or error-free. In no case shall Overruns Sa Tisa Online Shop, our directors, officers, employees, affiliates, agents, or contractors be liable for any injury, loss, claim, or any direct or indirect damages resulting from your use of our website.</p>

<h3>10. Governing Law</h3>
<p>These Terms and any separate agreements shall be governed by and construed in accordance with the laws of Philippines.</p>

<h3>11. Amendments</h3>
<p>We reserve the right to modify these terms at any time. Changes will be effective immediately upon posting on the website.</p>

<h3>12. Contact Information</h3>
<p>If you have any questions about these Terms and Conditions, please contact us at rowensecuya25@gmail.com.</p>

</div>

<?php include 'includes/scripts.php'; ?>
