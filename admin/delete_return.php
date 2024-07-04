<?php
include 'includes/session.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['id'])) {
        $id = $_POST['id'];
        $response = array(); // Initialize response array
        
        try {
            $conn = $pdo->open();

            $stmt = $conn->prepare("DELETE FROM return_products WHERE id = :id");
            $stmt->execute(['id' => $id]);

           
            $response['success'] = true;
            
        } catch (PDOException $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }

        $pdo->close();
       
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    } else {
        $response['success'] = false;
        $response['message'] = 'Transaction ID not provided';
    }
} else {
    $response['success'] = false;
    $response['message'] = 'Invalid request';
}


header('Content-Type: application/json');
echo json_encode($response);
exit();
?>
