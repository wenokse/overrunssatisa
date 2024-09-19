<?php
include 'includes/session.php';

function verifyBusinessPermit($permitNumber, $businessName) {
    // This function would typically make an API call to a government service
    // For this example, we'll simulate the verification process
    
  
    $apiUrl = "https://api.government.com/verify-business-permit";
    $data = [
        'permit_number' => $permitNumber,
        'business_name' => $businessName
    ];

   
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $response = curl_exec($ch);
    curl_close($ch);

    
    $result = json_decode($response, true);

    // Check if the permit is valid
    return isset($result['is_valid']) && $result['is_valid'] === true;
}

if(isset($_POST['signup'])){
    // ... [previous code for gathering form data] ...

    $business_permit = $_FILES['business_permit'];
    $permit_number = $_POST['permit_number']; // You'll need to add this field to your form

    // First, verify the permit number online
    if (!verifyBusinessPermit($permit_number, $name_store)) {
        $_SESSION['error'] = 'Invalid business permit number. Please check and try again.';
        header('location: vendor_signup.php');
        exit();
    }

    // If the permit number is valid, proceed with file upload verification
    $allowed_extensions = array('jpg', 'jpeg', 'png', 'pdf');
    $business_permit_extension = strtolower(pathinfo($business_permit['name'], PATHINFO_EXTENSION));

    if (!in_array($business_permit_extension, $allowed_extensions)) {
        $_SESSION['error'] = 'Invalid business permit file format. Allowed formats: jpg, jpeg, png, pdf';
        header('location: vendor_signup.php');
        exit();
    }

    if ($business_permit['size'] > 5000000) { // 5MB limit
        $_SESSION['error'] = 'Business permit file size must be less than 5MB';
        header('location: vendor_signup.php');
        exit();
    }

    // Generate unique filename for business permit
    $business_permit_filename = uniqid() . '.' . $business_permit_extension;
    $business_permit_destination = 'uploads/business_permits/' . $business_permit_filename;

    if (!move_uploaded_file($business_permit['tmp_name'], $business_permit_destination)) {
        $_SESSION['error'] = 'Failed to upload business permit';
        header('location: vendor_signup.php');
        exit();
    }

    // ... [rest of the signup process] ...
}
?>