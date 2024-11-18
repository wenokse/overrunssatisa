<?php
include 'includes/session.php';

$id = $_POST['id'];
$conn = $pdo->open();
$output = array('list'=>'');

$stmt = $conn->prepare("SELECT *, details.quantity AS detail_quantity 
                       FROM details 
                       LEFT JOIN products ON products.id=details.product_id 
                       LEFT JOIN sales ON sales.id=details.sales_id 
                       LEFT JOIN home_address ON home_address.sales_id=sales.id 
                        LEFT JOIN rider ON rider.sales_id=sales.id 
                       WHERE details.sales_id=:id");
$stmt->execute(['id'=>$id]);

$total = 0;
foreach($stmt as $row){
    $output['transaction'] = $row['pay_id'];
    $output['date'] = date('M d, Y', strtotime($row['sales_date']));
    $output['rider_name'] = $row['rider_name'] ?? 'Not Assigned';
    $output['phone_number'] = $row['phone_number'] ?? 'Not Available';
    $output['rider_address'] = $row['rider_address'] ?? 'Not Available';
    $subtotal = $row['price']*$row['detail_quantity'];
    $total += $subtotal;
    $output['list'] .= "
        <tr class='prepend_items'>
            <td>".$row['name']."</td>
            <td>&#8369; ".number_format($row['price'], 2)."</td>
            <td>".$row['size']."</td>
            <td>".$row['color']."</td>
            <td>".$row['detail_quantity']."</td>
            <td>&#8369; ".number_format($subtotal, 2)."</td>
        </tr>
    ";
}

$output['total'] = '<b>&#8369; '.number_format($total + 100, 2).'<b>';
$pdo->close();
echo json_encode($output);
?>