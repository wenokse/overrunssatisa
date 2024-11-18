<?php
include 'includes/session.php';

$id = $_POST['id'];
$conn = $pdo->open();
$output = array('list'=>'', 'address'=>'', 'rider'=>'');

// Get transaction details
$stmt = $conn->prepare("SELECT * FROM details 
                       LEFT JOIN products ON products.id=details.product_id 
                       LEFT JOIN sales ON sales.id=details.sales_id 
                       WHERE details.sales_id=:id");
$stmt->execute(['id'=>$id]);

$total = 0;
foreach($stmt as $row){
    $output['transaction'] = $row['pay_id'];
    $output['date'] = date('M d, Y', strtotime($row['sales_date']));
    $subtotal = $row['price']*$row['quantity']+$row['shipping'];
    $total += $subtotal;
    $output['list'] .= "
        <tr class='prepend_items'>
            <td>".$row['name']."</td>
            <td>&#8369; ".number_format($row['price'], 2)."</td>
            <td>".$row['size']."</td>
            <td>".$row['color']."</td>
            <td class='text-center'>".$row['quantity']."</td>
            <td>".$row['shipping']."</td>
            <td>&#8369; ".number_format($subtotal, 2)."</td>
        </tr>
    ";
}

// Get home address information
$stmt = $conn->prepare("SELECT * FROM home_address WHERE sales_id=:sales_id");
$stmt->execute(['sales_id'=>$id]);
$address = $stmt->fetch();

if($address){
    $output['address'] = "
        <div class='address-details'>
            <h4>Delivery Address</h4>
            <p><strong>Recipient:</strong> ".$address['recipient_name']."</p>
            <p><strong>Phone:</strong> ".$address['phone']."</p>
            <p><strong>Address:</strong> ".$address['address']."</p>
            <p><strong>Purok/Street:</strong> ".$address['address2']."</p>
            <p><strong>Landmark:</strong> ".$address['address3']."</p>
        </div>
    ";
}

// Get rider information
$stmt = $conn->prepare("SELECT * FROM rider WHERE sales_id=:sales_id");
$stmt->execute(['sales_id'=>$id]);
$rider = $stmt->fetch();

if($rider){
    $output['rider'] = "
        <div class='rider-details'>
            <h4>Rider Information</h4>
            <p><strong>Name:</strong> ".$rider['rider_name']."</p>
            <p><strong>Phone:</strong> ".$rider['phone_number']."</p>
            <p><strong>Address:</strong> ".$rider['rider_address']."</p>
        </div>
    ";
}

$output['total'] = '<b>&#8369; '.number_format($total, 2).'<b>';
$pdo->close();
echo json_encode($output);
?>