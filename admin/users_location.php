<?php
include 'includes/session.php';

$output = array('error' => true);

if(isset($_POST['id'])){
    $conn = $pdo->open();
    
    try{
        $stmt = $conn->prepare("SELECT latitude, longitude FROM user_locations WHERE user_id = :id");
        $stmt->execute(['id' => $_POST['id']]);
        $location = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($location){
            $output = array(
                'error' => false,
                'latitude' => $location['latitude'],
                'longitude' => $location['longitude']
            );
        } else {
            $output = array(
                'error' => false,
                'latitude' => null,
                'longitude' => null
            );
        }
    }
    catch(PDOException $e){
        $output['message'] = $e->getMessage();
    }
    
    $pdo->close();
}

echo json_encode($output);
?>