<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue layout-top-nav">
<div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v13.0&appId=1346358252525630&autoLogAppEvents=1" nonce="hsdcri7l"></script>
<div class="wrapper">

	<?php 
	if (isset($_SESSION['user'])) {
		include 'includes/navbar.php';
	} else {
		include 'includes/home_navbar.php';
	}
	?>
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
	<!-- About Us Section -->
	<div class="content-wrapper">
    <div class="about-us-container">
        <div class="row">
            <div class="col-md-8">
                <div class="about-us-box animated fadeInUp">
                    <h2 class="about-us-title">About Us</h2>
                    <div class="about-us-content">
                        <p>Welcome to <b>Overruns Sa Tisa</b>! We are your go-to e-commerce website for online shopping, exclusively serving Bantayan Island. Explore a wide selection of high-quality overrun items at unbeatable prices, from trendy fashion to essential goods. We offer fast, reliable, and island-specific delivery, making shopping easy and convenient for the people of Bantayan. Shop local, save more, and enjoy the convenience of doorstep delivery with Overruns Sa Tisa!</p>
                    </div>
                </div>
                
                <div class="contact-info animated fadeInUp">
                    <h3>Contact Us</h3>
                    <div class="contact-item">
                        <i class="fa fa-map-marker"></i>
                        <span>P.Lozada St Binaobao, Bantayan, Cebu Philippines</span>
                    </div>
                    <div class="contact-item">
                        <i class="fa fa-phone"></i>
                        <span>+639565565090</span>
                    </div>
                    <div class="contact-item">
                        <i class="fa fa-envelope"></i>
                        <a href="mailto:manilynalmohallas.miranda@gmail.com">manilynalmohallas.miranda@gmail.com</a>
                    </div>
                </div>

                <div class="map-container animated fadeInUp">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3935.187907224754!2d123.7156801!3d11.166587!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33a88903adb82363%3A0x6133fa0a7224a6!2sOverruns%20SA%20Tisa%20and%20Fashion%20Shop!5e0!3m2!1sen!2sph!4v1615687637855!5m2!1sen!2sph" 
                        allowfullscreen="" 
                        loading="lazy">
                    </iframe>
                </div>
            </div>
            <div class="col-md-4">
                <?php include 'includes/sidebar.php'; ?>
            </div>
        </div>
    </div>
</div>

	<?php include 'includes/footer.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>
<style>
/* Aesthetic About Us Page Styles */
body {
    font-family: 'Poppins', sans-serif;
    background-color: #f4f7f6;
    color: #333;
}

.content-wrapper {
    background: linear-gradient(135deg, #6e8efb, #a777e3);
}

.about-us-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 50px 20px;
}

.about-us-box {
    background-color: #ffffff;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    padding: 40px;
    margin-bottom: 30px;
    transition: transform 0.3s ease;
}

.about-us-box:hover {
    transform: translateY(-5px);
}

.about-us-title {
    font-size: 2.5em;
    color: #333;
    margin-bottom: 20px;
    position: relative;
    padding-bottom: 15px;
}

.about-us-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 50px;
    height: 3px;
    background-color: #6e8efb;
}

.about-us-content {
    font-size: 1.1em;
    line-height: 1.6;
    color: #555;
}

.contact-info {
    background-color: #6e8efb;
    color: #ffffff;
    border-radius: 15px;
    padding: 30px;
    margin-top: 30px;
}

.contact-info h3 {
    font-size: 1.5em;
    margin-bottom: 20px;
}

.contact-item {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}

.contact-item i {
    font-size: 1.2em;
    margin-right: 15px;
    color: #ffffff;
}

.contact-item a {
    color: #ffffff;
    text-decoration: none;
    transition: opacity 0.3s ease;
}

.contact-item a:hover {
    opacity: 0.8;
}

.map-container {
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    margin-top: 30px;
}

.map-container iframe {
    width: 100%;
    height: 400px;
    border: none;
}

@media (max-width: 768px) {
    .about-us-title {
        font-size: 2em;
    }
    
    .about-us-content {
        font-size: 1em;
    }
    
    .contact-info {
        padding: 20px;
    }
}

/* Animation */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animated {
    animation-duration: 1s;
    animation-fill-mode: both;
}

.fadeInUp {
    animation-name: fadeInUp;
}

</style>
</body>
</html>
