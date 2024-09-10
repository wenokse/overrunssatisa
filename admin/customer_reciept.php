<?php
include 'includes/session.php';

if(isset($_GET['sale_id'])) {
    $sale_id = $_GET['sale_id'];
    $conn = $pdo->open();
    $output = array('list' => '', 'total' => '');

    try {
        $stmt = $conn->prepare("SELECT * FROM details LEFT JOIN products ON products.id=details.product_id WHERE details.sales_id=:id");
        $stmt->execute(['id' => $sale_id]);
        $total = 0;

        foreach ($stmt as $details) {
            $subtotal = $details['price'] * $details['quantity'] + $details['shipping'];
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

        $output['total'] = '<b>'.number_format($total, 2).'</b>';

        // Fetch user's information from the database based on the sale ID
        $stmt_user = $conn->prepare("SELECT * FROM sales LEFT JOIN users ON users.id = sales.user_id WHERE sales.id = :id");
        $stmt_user->execute(['id' => $sale_id]);
        $user_info = $stmt_user->fetch();
        
        $pdo->close();
    } catch(PDOException $e) {
        echo $e->getMessage();
    }

   
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
        </style>
    </head>
    <body>
        <div class="center">
            <img src="../images/print.png" width="500" height="140" /><br />
            <h1>RECEIPT</h1>
            </div>
            <div class="header-section">
            <div class="header-item left">
                <p><strong>FROM:</strong> Overruns Sa Tisa Online Shop<br>P.Lozada St Binaobao, Bantayan, Cebu</p>
            </div>
            <div class="header-item center">
                <p><strong>SHIP TO:</strong> '.$user_info['firstname'].' '.$user_info['lastname'].'<br>'.$user_info['address'].'<br>'.$user_info['address2'].'<br>'.$user_info['contact_info'].'</p>
            </div>
            <div class="header-item right">
                <p><strong>RECEIPT#:</strong> '.$user_info['pay_id'].'<br><strong>DATE:</strong> '.date('M d, Y').'</p>
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
        </table>
        <p class="center"><strong>Total:</strong> '.$output['total'].'</p>
    </body>
    </html>';
} else {
    echo "Sale ID is not provided.";
}
?>
