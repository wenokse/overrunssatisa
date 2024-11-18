<?php
include 'includes/session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $sales_id = $_POST['sales_id'];
        $rider_name = $_POST['rider_name'];
        $rider_phone = $_POST['rider_phone'];
        $rider_address = $_POST['rider_address'];

        // First check if a rider exists for this sales_id
        $check = $conn->prepare("SELECT id FROM rider WHERE sales_id = :sales_id");
        $check->execute(['sales_id' => $sales_id]);
        $exists = $check->fetch();

        if ($exists) {
            // Update existing rider
            $stmt = $conn->prepare("UPDATE rider 
                                  SET rider_name = :rider_name,
                                      phone_number = :rider_phone,
                                      rider_address = :rider_address,
                                      updated_at = NOW()
                                  WHERE sales_id = :sales_id");
            
            $stmt->execute([
                'rider_name' => $rider_name,
                'rider_phone' => $rider_phone,
                'rider_address' => $rider_address,
                'sales_id' => $sales_id
            ]);
            
            $_SESSION['success'] = 'Rider information updated successfully.';
        } else {
            // Insert new rider
            $stmt = $conn->prepare("INSERT INTO rider (sales_id, rider_name, phone_number, rider_address, created_at) 
                                  VALUES (:sales_id, :rider_name, :rider_phone, :rider_address, NOW())");
            
            $stmt->execute([
                'sales_id' => $sales_id,
                'rider_name' => $rider_name,
                'rider_phone' => $rider_phone,
                'rider_address' => $rider_address
            ]);
            
            $_SESSION['success'] = 'Rider assigned successfully.';
        }
        
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Error: ' . $e->getMessage();
    }
    
} else {
    $_SESSION['error'] = 'Invalid request method.';
}

header('Location: sales');
exit();
?>