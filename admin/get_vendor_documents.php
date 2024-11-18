<?php
// Create a new file named get_vendor_documents.php

include 'includes/session.php';

if(isset($_POST['id'])){
    $id = $_POST['id'];
    
    $conn = $pdo->open();
    
    try{
        $stmt = $conn->prepare("SELECT business_permit, bir_doc, dti_doc, mayor_permit, valid_id FROM users WHERE id=:id");
        $stmt->execute(['id'=>$id]);
        $row = $stmt->fetch();
        
        $response = array(
            'business_permit' => $row['business_permit'],
            'bir_doc' => $row['bir_doc'],
            'dti_doc' => $row['dti_doc'],
            'mayor_permit' => $row['mayor_permit'],
            'valid_id' => $row['valid_id']
        );
        
        echo json_encode($response);
    }
    catch(PDOException $e){
        echo json_encode(['error' => $e->getMessage()]);
    }

    $pdo->close();
}
else{
    echo json_encode(['error' => 'No ID provided']);
}
?>