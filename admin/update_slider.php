<?php
include 'includes/session.php';

if (isset($_POST['toggle_slider'])) {
    // Retrieve the current slider status from the database
    $slider_enabled = isset($_POST['slider']) ? 1 : 0;

    // Update the slider status in the database
    $conn = $pdo->open();

    try {
        $stmt = $conn->prepare("UPDATE settings SET slider_enabled=:slider_enabled WHERE id=1");
        $stmt->execute(['slider_enabled' => $slider_enabled]);
        $_SESSION['success'] = 'Slider status updated successfully';
    } catch (PDOException $e) {
        $_SESSION['error'] = $e->getMessage();
    }

    $pdo->close();

    header('location: admin_edit_image.php');
}
?>
