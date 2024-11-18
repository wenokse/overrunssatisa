<?php
include 'includes/session.php';

if (isset($_POST['from']) && isset($_POST['to'])) {
    $from = $_POST['from'];
    $to = $_POST['to'];

    $conn = $pdo->open();
    $user_id = $_SESSION['admin']; 

    if ($user_id == 195) {  // Admin view                         
        $stmt = $conn->prepare("SELECT s.id AS salesid, 
                                     s.sales_date,
                                     s.pay_id,
                                     h.recipient_name AS recipient_name,
                                     h.phone AS recipient_phone,
                                     v.firstname AS vendor_firstname,
                                     v.lastname AS vendor_lastname,
                                     v.photo AS vendor_photo
                              FROM sales s 
                              LEFT JOIN home_address h ON h.sales_id = s.id
                              LEFT JOIN users v ON v.id = s.admin_id 
                              WHERE s.sales_date BETWEEN :from AND :to 
                              ORDER BY s.sales_date ASC");
        $stmt->execute(['from' => $from, 'to' => $to]);
    } else {  // Vendor view
        $stmt = $conn->prepare("SELECT s.id AS salesid, 
                                     s.sales_date,
                                     s.pay_id,
                                     h.recipient_name AS recipient_name,
                                     h.phone AS recipient_phone,
                                     v.firstname AS vendor_firstname,
                                     v.lastname AS vendor_lastname,
                                     v.photo AS vendor_photo
                              FROM sales s 
                              LEFT JOIN home_address h ON h.sales_id = s.id
                              LEFT JOIN users v ON v.id = s.admin_id 
                              WHERE s.admin_id = :admin_id 
                              AND s.sales_date BETWEEN :from AND :to 
                              ORDER BY s.sales_date ASC");
        $stmt->execute(['admin_id' => $user_id, 'from' => $from, 'to' => $to]);
    }

    $row = $stmt->fetch();
    // Prepare vendor photo path for the header
    $vendor_photo = !empty($row['vendor_photo']) ? '../images/'.$row['vendor_photo'] : '../images/profile.jpg';
    
    // Start building the HTML output with the new header layout
    $output = '
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div style="display: flex; align-items: center; flex: 1;">
            <img src="'.$vendor_photo.'" style="width: 100px; height: 100px; border-radius: 10px; margin-right: 10px; filter: none;" />
            <div>
                <h3 style="margin: 0;">'.$row['vendor_firstname'].' '.$row['vendor_lastname'].'</h3>
                <p style="margin: 5px 0;">Vendor</p>
            </div>
        </div>
    <div style="text-align: center; margin-bottom: 20px;">
        <h2>SALES REPORT</h2>
        <h4>'.date('M d, Y', strtotime($from)).' - '.date('M d, Y', strtotime($to)).'</h4>
    </div>
        <div style="flex: 1; text-align: right;">
            <img src="../images/logo1.png" style="width: 200px; height: 100px;" alt="Logo not found" />
        </div>

    </div>
   
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <th style="border: 1px solid #000; padding: 8px;">Date</th>
            <th style="border: 1px solid #000; padding: 8px;">Recipient Name</th>
            <th style="border: 1px solid #000; padding: 8px;">Phone</th>
            <th style="border: 1px solid #000; padding: 8px;">Transaction #</th>
            <th style="border: 1px solid #000; padding: 8px;">Amount</th>
        </tr>';

    // Reset the statement pointer
    $stmt->execute($user_id == 195 ? ['from' => $from, 'to' => $to] : ['admin_id' => $user_id, 'from' => $from, 'to' => $to]);
    
    $total = 0;
    foreach ($stmt as $row) {
        $details_stmt = $conn->prepare("SELECT admin_sales.*, products.name 
                FROM admin_sales 
                LEFT JOIN products ON products.id = admin_sales.product_id 
                WHERE admin_sales.sales_id = :id");
            $details_stmt->execute(['id' => $row['salesid']]);
            
            $amount = 0;
            foreach ($details_stmt as $details) {
                $subtotal = floatval($details['admin_price']);
                $amount += $subtotal;
            }
            $total += $amount;

            $output .= '
            <tr>
                <td style="border: 1px solid #000; padding: 8px;">'.date('M d, Y', strtotime($row['sales_date'])).'</td>
                <td style="border: 1px solid #000; padding: 8px;">'.$row['recipient_name'].'</td>
                <td style="border: 1px solid #000; padding: 8px;">'.$row['recipient_phone'].'</td>
                <td style="border: 1px solid #000; padding: 8px;">'.$row['pay_id'].'</td>
                <td style="border: 1px solid #000; padding: 8px; text-align: right;">PHP '.number_format($amount, 2).'</td>
            </tr>';
        }


    $output .= '
        <tr>
            <td colspan="4" style="border: 1px solid #000; padding: 8px; text-align: right;"><b>Total</b></td>
            <td style="border: 1px solid #000; padding: 8px; text-align: right;"><b>PHP '.number_format($total, 2).'</b></td>
        </tr>
    </table>';

    echo $output;

    $pdo->close();
}
?>
