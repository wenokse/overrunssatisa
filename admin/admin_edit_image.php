<?php
include 'includes/session.php';
include 'includes/header.php';

// Fetch the current slider status from the database
$slider_enabled = 0; // Default value in case of an error or no value in the database

$conn = $pdo->open();

try {
    $stmt = $conn->prepare("SELECT slider_enabled FROM settings WHERE id=1");
    $stmt->execute();
    $result = $stmt->fetch();
    if ($result) {
        $slider_enabled = $result['slider_enabled'];
    }
} catch (PDOException $e) {
    $_SESSION['error'] = $e->getMessage();
}

$pdo->close();
?>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/menubar.php'; ?>
    <script src="../js/sweetalert.min.js"></script>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Settings
            </h1>
            <ol class="breadcrumb">
                <li><a href="home.php"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Settings</li>
            </ol>
        </section>
        <style>
   .content-wrapper {
      background: white;
   }
</style>
        <div class="container">
            <section class="content">
                <div class="">
                    <div class="col-xs-12">
                        <div class="">
                            <div class="box-header with-border">
                                <table class="table table-bordered">
                                    <thead>
                                        <th class="hidden"></th>
                                        <th>Name</th>
                                        <th>File</th>
                                        <th>Action</th>
                                    </thead>
                                    <hr style="border-top: 2px solid black; margin-top: 10px;">
                                    
                                    <!-- Form for uploading homepage image -->
                                    <form method="POST" action="update_image.php" enctype="multipart/form-data">
                                        <tr>
                                            <td class='hidden'></td>
                                            <td><label for="image">Choose Homepage Image</label></td>
                                            <td><input type="file" id="image" name="image"></td>
                                            <td colspan="2" class="text-center">
                                                <button type="submit" class="btn btn-primary" name="upload">Upload</button>
                                            </td>
                                        </tr>
                                    </form>
                                    

                                    <!-- Form for toggling the slider -->
                                    <form method="POST" action="update_slider.php">
                                        <tr>
                                            <td class='hidden'></td>
                                            <td><label for="slider">Switch to Slider Homepage</label></td>
                                            <td>
                                                <label class="switch">
                                                    <input type="checkbox" id="slider" name="slider" <?php if ($slider_enabled) echo 'checked'; ?>>
                                                    <span class="slider round"></span>
                                                </label>
                                            </td>
                                            <td colspan="2" class="text-center">
                                                <button type="submit" class="btn btn-primary" name="toggle_slider">Save</button>
                                            </td>
                                        </tr>
                                    </form>

                                     <!-- Form for uploading slider images -->
                                     <!-- <form method="POST" action="upload_slider_image.php" enctype="multipart/form-data">
                                        <tr>
                                            <td class='hidden'></td>
                                            <td><label for="slider_image_1">Upload 3 Slider Images</label></td>
                                            <td>
                                                <input type="file" id="slider_image_1" name="slider_images[]" accept="image/*"><br>
                                                <input type="file" id="slider_image_2" name="slider_images[]" accept="image/*"><br>
                                                <input type="file" id="slider_image_3" name="slider_images[]" accept="image/*">
                                            </td>
                                            <td colspan="2" class="text-center">
                                                <button type="submit" class="btn btn-primary" name="upload_slider_images">Upload</button>
                                            </td>
                                        </tr>
                                    </form> -->

                                </table>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</div>
<?php include 'includes/scripts.php'; ?>
</body>
</html>
