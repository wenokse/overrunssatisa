<?php
include 'includes/session.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Initialize variables
    $image_url = isset($_POST['image_url']) ? $_POST['image_url'] : '';
    $image = isset($_FILES['image']) ? $_FILES['image'] : null;

    if (!empty($image['name'])) {
        $target_dir = "image/";
        $target_file = $target_dir . basename($image['name']);

        // Check if directory exists and is writable
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        if (!is_writable($target_dir)) {
            $_SESSION['error'] = "Upload directory is not writable.";
            header('Location: admin_edit_image.php');
            exit();
        }

        // Move uploaded file
        if (move_uploaded_file($image['tmp_name'], $target_file)) {
            $image_url = $target_file;
        } else {
            // Get detailed error information
            $error_code = $image['error'];
            $error_messages = [
                UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
                UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
                UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
                UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
                UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.'
            ];
            $_SESSION['error'] = "Failed to upload image. Error: " . $error_messages[$error_code] ?? 'Unknown error';
            header('Location: admin_edit_image.php');
            exit();
        }
    }

    if (!empty($image_url)) {
        // Update the image URL in the database
        $conn = $pdo->open();
        try {
            $stmt = $conn->prepare("UPDATE settings SET value=:value WHERE name='home_image'");
            $stmt->execute(['value' => $image_url]);
            $_SESSION['success'] = "Image updated successfully.";
        } catch (PDOException $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        $pdo->close();
    } else {
        $_SESSION['error'] = "Please provide an image or URL.";
    }

    header('Location: admin_edit_image.php');
    exit();
}
?>
