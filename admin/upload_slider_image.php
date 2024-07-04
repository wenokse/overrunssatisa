<?php
include 'includes/session.php';

if (isset($_POST['upload_slider_images'])) {
    $conn = $pdo->open();
    $target_dir = "image/";

    foreach ($_FILES["slider_images"]["tmp_name"] as $key => $tmp_name) {
        $target_file = $target_dir . basename($_FILES["slider_images"]["name"][$key]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a real image
        $check = getimagesize($tmp_name);
        if ($check === false) {
            $_SESSION['error'] = "File is not an image.";
            $uploadOk = 0;
            break;
        }

        
        // Check file size
        if ($_FILES["slider_images"]["size"][$key] > 100000000) { 
            $_SESSION['error'] = "Sorry, your file is too large. Max size is 500KB.";
            $uploadOk = 0;
            break;
        }

       
        $allowedFormats = ["jpg", "jpeg", "png", "gif"];
        if (!in_array($imageFileType, $allowedFormats)) {
            $_SESSION['error'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
            break;
        }

        
        if ($uploadOk == 0) {
            $_SESSION['error'] .= " Your file was not uploaded.";
        } else {
            if (move_uploaded_file($tmp_name, $target_file)) {
                try {
                    $stmt = $conn->prepare("INSERT INTO slider_images (image_path) VALUES (:image_path)");
                    $stmt->execute(['image_path' => $target_file]);
                    $_SESSION['success'] = "The file " . htmlspecialchars(basename($_FILES["slider_images"]["name"][$key])) . " has been uploaded.";
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Database error: " . $e->getMessage();
                    break; 
                }
            } else {
                $_SESSION['error'] = "Sorry, there was an error uploading your file.";
                break; 
            }
        }
    }

    $pdo->close();
    header('Location: admin_edit_image.php');
    exit();
}
?>
