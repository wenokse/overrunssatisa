<?php
include 'includes/session.php';

$id = $_POST['id'];  // Transaction ID

$conn = $pdo->open();

$output = array('list'=>'');

// Get the number of items in the transaction
$stmt_count = $conn->prepare("SELECT COUNT(*) as item_count FROM details WHERE sales_id=:id");
$stmt_count->execute(['id'=>$id]);
$row_count = $stmt_count->fetch();
$item_count = $row_count['item_count'];  // Number of items in the transaction

// First get the sales record to get the user_id
$stmt_sale = $conn->prepare("SELECT * FROM sales WHERE id=:id");
$stmt_sale->execute(['id'=>$id]);
$sale = $stmt_sale->fetch();

$stmt_address = $conn->prepare("SELECT * FROM home_address WHERE sales_id=:sales_id");
$stmt_address->execute(['sales_id' => $id]);
$address = $stmt_address->fetch();

// Add recipient details to output
if ($address) {
    $output['recipient'] = $address['recipient_name'];
    $output['delivery_address'] = $address['address'];
    $output['address2'] = $address['address2'];
    $output['address3'] = $address['address3'];
} else {
    $output['recipient'] = 'N/A';
    $output['delivery_address'] = 'N/A';
    $output['address2'] = 'N/A';
    $output['address3'] = 'N/A';
}

$stmt_address = $conn->prepare("SELECT * FROM rider WHERE sales_id=:sales_id");
$stmt_address->execute(['sales_id' => $id]);
$address = $stmt_address->fetch();
if ($address) {
    $output['rider_name'] = $address['rider_name'];
    $output['phone_number'] = $address['phone_number'];
    $output['rider_address'] = $address['rider_address'];
} else {
    $output['rider_name'] = 'N/A';
    $output['phone_number'] = 'N/A';
    $output['rider_address'] = 'N/A';
}

$stmt = $conn->prepare("SELECT details.id as detail_id, details.*, products.*, sales.* 
                    FROM details 
                    LEFT JOIN products ON products.id=details.product_id 
                    LEFT JOIN sales ON sales.id=details.sales_id 
                    WHERE details.sales_id=:id");
$stmt->execute(['id'=>$id]);

$total = 0;
foreach($stmt as $row){
    $output['transaction'] = $row['pay_id'];
    $output['date'] = date('M d, Y', strtotime($row['sales_date']));
    $subtotal = $row['price'] * $row['quantity'];
    $total += $subtotal;
    
    // Get the color-specific photo
    $color_photo = '';
    if(!empty($row['color'])) {
        $stmt_color = $conn->prepare("SELECT photo FROM product_colors 
                                    WHERE product_id=:product_id AND color=:color");
        $stmt_color->execute([
            'product_id' => $row['product_id'],
            'color' => $row['color']
        ]);
        $color_row = $stmt_color->fetch();
        if($color_row && !empty($color_row['photo'])) {
            $color_photo = 'images/colors/' . $color_row['photo'];
        }
    }
    
    // If no color photo found, use default product photo
    if(empty($color_photo)) {
        $color_photo = 'images/' . $row['photo'];
    }
    
    // Only show the Cancel button if the status is not 'On Delivery' (status != 2) and if there are more than 1 items in the transaction
    $cancelButton = '';
    if ($row['status'] != 2 && $row['status'] != 3 && $row['status'] != 4 && $item_count > 1) {
        $cancelButton = "<button class='btn btn-danger btn-sm cancel-product' data-id='".$row['detail_id']."'>Cancel</button>";
    }
    
    $output['list'] .= "
        <tr class='prepend_items'>
            <td>
                <div class='product-img-container'>
                    <img src='".$color_photo."' 
                         class='product-img view-image' 
                         style='width: 80px; height: 80px; object-fit: cover; border-radius: 5px; cursor: pointer;'
                         data-toggle='modal' 
                         data-target='#imageModal' 
                         data-img-src='".$color_photo."'
                         data-product-name='".$row['name']."'
                         data-product-color='".$row['color']."'>
                </div>
            </td>
            <td>".$row['name']."</td>
            <td>&#8369; ".number_format($row['price'], 2)."</td>
            <td>".(!empty($row['size']) ? $row['size'] : 'N/A')."</td>
            <td>".(!empty($row['color']) ? $row['color'] : 'N/A')."</td>
            <td class='text-center'>".$row['quantity']."</td>
            <td>&#8369; ".number_format($subtotal, 2)."</td>
            <td>".$cancelButton."</td>
        </tr>
    ";
}

$output['total'] = '<b>&#8369; '.number_format($total + 100, 2).'</b>';
$pdo->close();
echo json_encode($output);
?>