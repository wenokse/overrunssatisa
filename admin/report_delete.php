<?php
include 'includes/session.php';

$conn = $pdo->open();
$response = array('status' => 'error', 'message' => '');

if(isset($_POST['id'])){
    try{
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM reports WHERE id=:id");
        $stmt->execute(['id'=>$id]);
        
        $response['status'] = 'success';
        $response['message'] = 'Report deleted successfully';
    }
    catch(PDOException $e){
        $response['message'] = $e->getMessage();
    }
}

$pdo->close();
echo json_encode($response);
?>