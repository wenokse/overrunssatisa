<?php
include 'includes/session.php';

header('Content-Type: application/json');

if (isset($_POST['sales_id'])) {
    try {
        $sales_id = $_POST['sales_id'];
        
        $stmt = $conn->prepare("SELECT 
                                id,
                                sales_id,
                                rider_name,
                                phone_number,
                                rider_address,
                                created_at,
                                updated_at
                              FROM rider 
                              WHERE sales_id = :sales_id");
        
        $stmt->execute(['sales_id' => $sales_id]);
        $rider = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($rider) {
            echo json_encode([
                'status' => 'success',
                'data' => $rider
            ]);
        } else {
            echo json_encode([
                'status' => 'empty',
                'data' => [
                    'rider_name' => '',
                    'phone_number' => '',
                    'rider_address' => ''
                ]
            ]);
        }
        
    } catch (PDOException $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Database error occurred'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Sales ID not provided'
    ]);
}
?>