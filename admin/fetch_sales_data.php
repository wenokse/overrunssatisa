<?php
include 'includes/session.php';

if (isset($_POST['from']) && isset($_POST['to'])) {
    $from = $_POST['from'];
    $to = $_POST['to'];

    $conn = $pdo->open();
    $user_id = $_SESSION['admin']; 

    if ($user_id == 195) {                           
    $stmt = $conn->prepare("SELECT *, sales.id AS salesid FROM sales LEFT JOIN users ON users.id=sales.user_id WHERE sales_date BETWEEN :from AND :to ORDER BY sales_date ASC");
		$stmt->execute(['from' => $from, 'to' => $to]);
    }else{
    $stmt = $conn->prepare("SELECT sales.id AS salesid, sales.sales_date, users.firstname, users.lastname, sales.pay_id 
                             FROM sales 
                             LEFT JOIN users ON users.id = sales.user_id 
                             WHERE sales.admin_id = :admin_id 
                             AND sales.sales_date BETWEEN :from AND :to 
                             ORDER BY sales.sales_date ASC");
    $stmt->execute(['admin_id' => $user_id, 'from' => $from, 'to' => $to]);
    }
    $total = 0;
    $output = '';

    // Loop through each sale
    foreach ($stmt as $row) {
        $details_stmt = $conn->prepare("SELECT * FROM details 
                                        LEFT JOIN products ON products.id = details.product_id 
                                        WHERE details.sales_id = :id");
        $details_stmt->execute(['id' => $row['salesid']]);
        
        $amount = 0;
        // Calculate the total amount for each sale
        foreach ($details_stmt as $details) {
            $subtotal = $details['price'] * $details['quantity'] + $details['shipping'];
            $amount += $subtotal;
        }
        $total += $amount;
        $output .= '
        <tr>
            <td>'.date('M d, Y', strtotime($row['sales_date'])).'</td>
            <td>'.$row['firstname'].' '.$row['lastname'].'</td>
            <td>'.$row['pay_id'].'</td>
            <td align="right">PHP '.number_format($amount, 2).'</td>
        </tr>';
    }

    // Display total sales
    $output .= '
    <tr>
        <td colspan="3" align="right"><b>Total</b></td>
        <td align="right"><b>PHP '.number_format($total, 2).'</b></td>
    </tr>';

    echo $output;

    $pdo->close();
}
?>
