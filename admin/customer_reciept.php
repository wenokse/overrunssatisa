<?php
include 'includes/session.php';

// Check if sale_id is set in the URL
if(isset($_GET['sale_id'])) {
    $sale_id = $_GET['sale_id'];

    // Fetch sale details from the database using the provided sale_id
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
                <td>'.date('M d, Y', strtotime($details['date_added'])).'</td>
                <td>'.$details['name'].'</td>
                <td align="right">&#8369; '.number_format($details['price'], 2).'</td>
                <td align="center">'.$details['quantity'].'</td>
                <td align="right">&#8369; '.number_format($subtotal, 2).'</td>
            </tr>
            ';
        }

        $output['total'] = '<b>&#8369; '.number_format($total, 2).'</b>';

        // Fetch user's information from the database based on the sale ID
        $stmt_user = $conn->prepare("SELECT * FROM sales LEFT JOIN users ON users.id = sales.user_id WHERE sales.id = :id");
        $stmt_user->execute(['id' => $sale_id]);
        $user_info = $stmt_user->fetch();
        
        $pdo->close();
    } catch(PDOException $e) {
        echo $e->getMessage();
    }

    // Generate PDF
    require_once('../tcpdf/tcpdf.php');  
    $pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);  
    $pdf->SetCreator(PDF_CREATOR);  
    $pdf->SetTitle('Receipt: '.$sale_id);  
    $pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);  
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));  
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
    $pdf->SetDefaultMonospacedFont('helvetica');  
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);  
    $pdf->SetMargins(PDF_MARGIN_LEFT, '10', PDF_MARGIN_RIGHT);  
    $pdf->setPrintHeader(false);  
    $pdf->setPrintFooter(false);  
    $pdf->SetAutoPageBreak(TRUE, 10);  
    $pdf->SetFont('helvetica', '', 11);  
    $pdf->AddPage();  
    
    $content = '';  
    $content .= '
    <style>
    table {
        border-collapse: collapse;
        width: 100%;
    }
    th, td {
        border: 1px solid black;
        padding: 8px;
    }
    </style>
    ';

    // Add image and header
    $content .= '<img src="../images/LOGO.png" width="500" height="100" align="center" />';
    $content .= '<h1 style="text-align: center;">RECEIPT</h1>';

    // Add user information
    $content .= '
    <table style="margin-bottom: 20px;">
        <tr>
            <td><strong>FROM:</strong><br>Overruns Sa Tisa Online Shop<br>P.Lozada St Binaobao, Bantayan, Cebu</td>
            <td><strong>SHIP TO:</strong><br>'.$user_info['firstname'].' '.$user_info['lastname'].'<br>'.$user_info['address'].'<br>'.$user_info['address2'].'<br>'.$user_info['contact_info'].'</td>
            <td><strong>RECEIPT#:</strong> '.$user_info['pay_id'].'<br><strong>DATE:</strong> '.date('M d, Y').'</td><br><br>
        </tr>
    </table>';

    // Add items table
    $content .= '
    <table>
        <tr>
            <th>Date</th>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
        </tr>
        '.$output['list'].'
        <tr>
            <td colspan="4" align="right"><b>Total</b></td>
            <td align="right">'.$output['total'].'</td>
        </tr>
    </table>
    ';

    $pdf->writeHTML($content);  
    $pdf->Output('receipt_'.$sale_id.'.pdf', 'I');

} else {
    echo "Sale ID is not provided.";
}
?>