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
                <td> '.number_format($details['price'], 2).'</td>
                <td>'.$details['size'].'</td>
                <td>'.$details['color'].'</td>
                <td>'.$details['quantity'].'</td>
                <td> '.number_format($subtotal, 2).'</td>
            </tr>
            ';
        }

        $output['total'] = '<b> '.number_format($total, 2).'</b>';

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
         margin: 20px 0;
    }
    th, td {
        border: 1px solid black;
        padding: 8px;
    }
    </style>
    ';

   
    $content .= '<img src="../images/print.png" width="500" height="140" align="center" />';
    $content .= '<h1 style="text-align: center;">RECEIPT</h1>';

    
    $content .= '&nbsp;&nbsp;
    <table>
        <tr>
            <td><strong>FROM:</strong><br>Overruns Sa Tisa Online Shop<br>P.Lozada St Binaobao, Bantayan, Cebu</td>
            <td><strong>SHIP TO:</strong><br>'.$user_info['firstname'].' '.$user_info['lastname'].'<br>'.$user_info['address'].'<br>'.$user_info['address2'].'<br>'.$user_info['contact_info'].'</td>
            <td><strong>RECEIPT#:</strong> '.$user_info['pay_id'].'<br><strong>DATE:</strong> '.date('M d, Y').'</td><br><br>
        </tr>
    </table>';

    $content .= '<br><br>';
    $content .= '
    <table>
        <tr>
            <th align="center">Product</th>
            <th align="center">Price</th>
            <th align="center">Size</th>
            <th align="center">Color</th>
            <th align="center">Quantity</th>
            <th align="center">Subtotal</th>
        </tr>
        '.$output['list'].'
       
    </table>
    ';
    $content .= '<p style="text-align: right;"><b>Total: </b>'.$output['total'].'</p>';
    $pdf->writeHTML($content);  
    $pdf->Output('receipt_'.$sale_id.'.pdf', 'I');

} else {
    echo "Sale ID is not provided.";
}
?>