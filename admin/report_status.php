<?php
include 'includes/session.php';

$conn = $pdo->open();
$response = array('status' => 'error', 'message' => '');

if(isset($_POST['report_id']) && isset($_POST['status'])){
    try{
        $report_id = $_POST['report_id'];
        $status = $_POST['status'];

        // Validate status
        $valid_statuses = array('pending', 'reviewed', 'resolved', 'dismissed');
        if(!in_array($status, $valid_statuses)){
            $response['message'] = 'Invalid status value';
            echo json_encode($response);
            exit();
        }

        // Check if report exists
        $stmt = $conn->prepare("SELECT id FROM reports WHERE id=:id");
        $stmt->execute(['id' => $report_id]);
        if($stmt->rowCount() == 0){
            $response['message'] = 'Report not found';
            echo json_encode($response);
            exit();
        }

        // Update report status
        $stmt = $conn->prepare("UPDATE reports SET status=:status, updated_at=NOW() WHERE id=:id");
        $stmt->execute([
            'status' => $status,
            'id' => $report_id
        ]);
        
        $response['status'] = 'success';
        $response['message'] = 'Report status updated successfully';

    }
    catch(PDOException $e){
        $response['message'] = $e->getMessage();
    }
}
else {
    $response['message'] = 'Invalid parameters';
}

$pdo->close();
echo json_encode($response);
?>