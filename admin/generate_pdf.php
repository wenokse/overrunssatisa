<?php
require_once('../tcpdf/tcpdf.php');  
include 'includes/session.php';

if (isset($_GET['sale_id'])) {
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

            $output['list'] .= "
                <tr>
                    <td>".$details['name']."</td>
                    <td>".number_format($details['price'], 2)."</td>
                    <td>".$details['size']."</td>
                    <td>".$details['color']."</td>
                    <td>".$details['quantity']."</td>
                    <td>".$details['shipping']."</td>
                    <td>".number_format($subtotal, 2)."</td>
                </tr>
            ";
        }

        $output['total'] = '<b>'.number_format($total, 2).'</b>';

        $stmt_user = $conn->prepare("SELECT * FROM sales LEFT JOIN users ON users.id = sales.user_id WHERE sales.id = :id");
        $stmt_user->execute(['id' => $sale_id]);
        $user_info = $stmt_user->fetch();

        $pdo->close();
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetTitle('Receipt');
    $pdf->SetHeaderData('', 0, 'Receipt', '');
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    $pdf->AddPage();

    // Get the logo path
    $logoPath = 'image/overrun.png'; 

    $content = "
        <style>
            .receipt { font-family: 'Arial', sans-serif; }
            .header { position: relative; text-align: center; margin-bottom: 50px; }
            .header img { position: absolute; top: 0; right: 0; width: 100px; }
            .info { display: flex; justify-content: space-between; margin-bottom: 50px; }
            .info > div { width: 32%; }
            .items table { width: 100%; border-collapse: collapse; margin-bottom: 50px; }
            .items th, .items td { border: 1px solid #000; padding: 5px; text-align: center; }
            .total { text-align: right; }
        </style>
        <div class='receipt'>
            <div class='header'>
                <h1>RECEIPT</h1>
                <img src='".$logoPath."' alt='Logo'>
            </div>
            <div class='info'>
                <div class='bill-to'>
                    <strong>FROM</strong><br><br>
                    Overruns Sa Tisa Online Shop<br>
                    P.Lozada St Binaobao, Bantayan, Cebu
                </div>
                <div class='ship-to'>
                    <strong>SHIP TO</strong><br><br>
                    ".$user_info['firstname']." ".$user_info['lastname']."<br>
                    ".$user_info['address']."<br>
                    ".$user_info['address2']."<br>
                    ".$user_info['contact_info']."
                </div>
                <div class='receipt-info'>
                    <b>RECEIPT#:</b> ".(isset($user_info['pay_id']) ? $user_info['pay_id'] : 'N/A')."<br>
                    <b>DATE:</b> ".date('M d, Y')."
                </div>
            </div>
            <div class='items'>
                <table>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Size</th>
                        <th>Color</th>
                        <th>Quantity</th>
                        <th>Shipping</th>
                        <th>Subtotal</th>
                    </tr>
                    ".(isset($output['list']) ? $output['list'] : '')."
                </table>
            </div>
            <div class='total'>
                <strong>Total: </strong> ".(isset($output['total']) ? $output['total'] : '')."
            </div>
        </div>";

    $pdf->writeHTML($content, true, false, true, false, '');
    $pdf->Output('receipt.pdf', 'D');
} else {
    echo "Sale ID is not provided.";
}
?>
