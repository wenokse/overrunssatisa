<?php
include 'includes/session.php';

$conn = $pdo->open();

$response = array('status' => 'error', 'message' => '');

if(isset($_POST['action']) && $_POST['action'] == 'report') {
    try {
        // Check if user is logged in
        if(!isset($_SESSION['user'])){
            $response['message'] = 'Please login first to submit a report';
            echo json_encode($response);
            exit();
        }

        $reporter_id = $_SESSION['user'];
        $shop_id = $_POST['shop_id'];
        $reason = $_POST['reason'];
        $description = $_POST['description'];

        // Validate inputs
        if(empty($reason) || empty($description)) {
            $response['message'] = 'All fields are required';
            echo json_encode($response);
            exit();
        }

        // Check if user has already reported this shop recently
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM reports WHERE reporter_id = :reporter_id AND shop_id = :shop_id AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)");
        $stmt->execute([
            'reporter_id' => $reporter_id,
            'shop_id' => $shop_id
        ]);
        $result = $stmt->fetch();

        if($result['count'] > 0) {
            $response['message'] = 'You have already reported this shop in the last 24 hours';
            echo json_encode($response);
            exit();
        }

        // Insert report
        $stmt = $conn->prepare("INSERT INTO reports (reporter_id, shop_id, reason, description) VALUES (:reporter_id, :shop_id, :reason, :description)");
        $stmt->execute([
            'reporter_id' => $reporter_id,
            'shop_id' => $shop_id,
            'reason' => $reason,
            'description' => $description
        ]);

        $response['status'] = 'success';
        $response['message'] = 'Thank you for your report. Our team will review it shortly.';

    } catch(PDOException $e) {
        $response['message'] = 'Database Error: ' . $e->getMessage();
    }
}

$pdo->close();
echo json_encode($response);
?>