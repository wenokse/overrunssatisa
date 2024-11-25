<?php
include 'includes/session.php';

$output = array('error' => true);

if(isset($_POST['id'])){
    $conn = $pdo->open();
    
    try {
        // Modified query to get the most recent login location
        $stmt = $conn->prepare("
            SELECT 
                latitude, 
                longitude,
                last_login,
                first_login
            FROM user_locations 
            WHERE user_id = :id 
            ORDER BY last_login DESC 
            LIMIT 1
        ");
        
        $stmt->execute(['id' => $_POST['id']]);
        $location = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($location){
            $output = array(
                'error' => false,
                'latitude' => $location['latitude'],
                'longitude' => $location['longitude'],
                'last_login' => date('M d, Y h:i A', strtotime($location['last_login'])),
                'first_login' => date('M d, Y h:i A', strtotime($location['first_login']))
            );
        } else {
            $output = array(
                'error' => false,
                'latitude' => null,
                'longitude' => null,
                'last_login' => null,
                'first_login' => null
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