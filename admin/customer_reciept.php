<?php
// customer_receipt.php
include 'includes/session.php';

if(isset($_GET['sale_id'])) {
    $sale_id = $_GET['sale_id'];
    $conn = $pdo->open();
    $output = array('list' => '', 'total' => '');

    try {
        // Get sale details
        $stmt = $conn->prepare("SELECT * FROM details LEFT JOIN products ON products.id=details.product_id WHERE details.sales_id=:id");
        $stmt->execute(['id' => $sale_id]);
        $total = 0;

        foreach ($stmt as $details) {
            $subtotal = $details['price'] * $details['quantity'];
            $total += $subtotal;

            $output['list'] .= '
            <tr>
                <td>'.$details['name'].'</td>
                <td>'.number_format($details['price'], 2).'</td>
                <td>'.$details['size'].'</td>
                <td>'.$details['color'].'</td>
                <td>'.$details['quantity'].'</td>
                <td>'.number_format($subtotal, 2).'</td>
            </tr>
            ';
        }

        $output['total'] = '<b>'.number_format($total + 100, 2).'</b>';

        $stmt_sale = $conn->prepare("SELECT s.*, h.recipient_name as h_name, h.address as h_address, 
                    h.address2 as h_address2, h.address3 as h_address3, h.phone as h_phone,
                    v.firstname as vendor_firstname, v.lastname as vendor_lastname, v.address as vendor_address, 
                    v.address2 as vendor_address2, v.contact_info as vendor_contact, v.photo as vendor_photo
                    FROM sales s 
                    LEFT JOIN home_address h ON h.sales_id = s.id
                    LEFT JOIN users u ON u.id = s.user_id 
                    LEFT JOIN users v ON v.id = s.admin_id 
                    WHERE s.id = :id");
            $stmt_sale->execute(['id' => $sale_id]);
        $sale_info = $stmt_sale->fetch();
        
        $pdo->close();
    } catch(PDOException $e) {
        echo $e->getMessage();
    }

    // Prepare vendor photo path
    $vendor_photo = !empty($sale_info['vendor_photo']) ? '../images/'.$sale_info['vendor_photo'] : '../images/profile.jpg';
   
    echo '<html>
    <head>
        <title>Customer Receipt</title>
        <style>
            body { font-family: Arial, sans-serif; }
            table { border-collapse: collapse; width: 100%; margin: 20px 0; }
            th, td { border: 1px solid black; padding: 8px; text-align: center; }
            .center { text-align: center; }
            .header-section {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 20px;
            }
            .header-item {
                width: 30%;
            }
            .header-item.center {
                text-align: center;
            }
            .header-item.right {
                text-align: right;
            }
            .vendor-info {
                display: flex;
                align-items: center;
                margin-bottom: 15px;
            }
            .vendor-photo {
                width: 50px;
                height: 50px;
                border-radius: 10%;
                margin-right: 10px;
            }
        </style>
    </head>
    <body>
        <div class="vendor-info">
            <img src="'.$vendor_photo.'" class="vendor-photo" alt="Vendor Photo"/>
            <p><strong>Vendor:</strong> '.$sale_info['vendor_firstname'].' '.$sale_info['vendor_lastname'].'</p>
        </div>
        <div class="header-section">
            <div class="header-item left">
                <p><strong>Sender:</strong><br> '.$sale_info['vendor_firstname'].' '.$sale_info['vendor_lastname'].'<br> '.$sale_info['vendor_address'].' '.$sale_info['vendor_address2'].'<br>'.$sale_info['vendor_contact'].'</p>
            </div>
            <div class="header-item center">
                <p><strong>Receiver:</strong> '.$sale_info['h_name'].'<br>'.$sale_info['h_address'].''.$sale_info['h_address2'].''.$sale_info['h_address3'].'<br>'.$sale_info['h_phone'].'</p>
            </div>
            <div class="header-item right">
                <p><strong>RECEIPT#:</strong> '.$sale_info['pay_id'].'<br><strong>DATE:</strong> '.date('M d, Y').'</p>
            </div>
        </div>
        <table>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Size</th>
                <th>Color</th>
                <th>Quantity</th>
                <th>Subtotal</th>
            </tr>
            '.$output['list'].'
       
         <tbody id="detail">
            <td colspan="6" class="text-right"><strong>Total:</strong> '.$output['total'].'</td>
            </tbody>
             </table>
              <h4>Payment Method: COD</h4>
               <h4 style="color: red;">Shipping: 100</h4>
    </body>
    </html>';
} else {
    echo "Sale ID is not provided.";
}


?>
