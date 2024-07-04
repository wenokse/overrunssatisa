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

            // Generate HTML for each item in the receipt
            $output['list'] .= "
                <tr class='prepend_items'>
                    <td>".$details['name']."</td>
                    <td>&#8369; ".number_format($details['price'], 2)."</td>
                    <td>".$details['size']."</td>
                    <td>".$details['color']."</td>
                    <td>".$details['quantity']."</td>
                    <td>".$details['shipping']."</td>
                    <td>&#8369; ".number_format($subtotal, 2)."</td>
                </tr>
            ";
        }

        // Generate total HTML
        $output['total'] = '<b>&#8369; '.number_format($total, 2).'</b>';

        // Fetch user's information from the database based on the sale ID
        $stmt_user = $conn->prepare("SELECT * FROM sales LEFT JOIN users ON users.id = sales.user_id WHERE sales.id = :id");
        $stmt_user->execute(['id' => $sale_id]);
        $user_info = $stmt_user->fetch();

        // Populate the "SHIP TO" section with user's information
        $ship_to = "
            <div class='ship-to'>
                <strong>SHIP TO</strong><br><br>
                ".$user_info['firstname']." ".$user_info['lastname']."<br>
                ".$user_info['address']."<br>
                ".$user_info['address2']."<br>
                ".$user_info['contact_info']."
            </div>
        ";
        
        $pdo->close();
    } catch(PDOException $e) {
        echo $e->getMessage();
    }
} else {
    echo "Sale ID is not provided.";
}
?>

<style>
  .receipt {
    width: 100%; 
    padding: 20px; 
    font-family: 'Arial', sans-serif;
  }
  .header, .info, .items, .total {
    margin-bottom: 20px;
  }
  .header {
    text-align: center;
  }
  .info {
    display: flex;
    justify-content: space-between;
  }
  .info > div {
    flex-basis: calc(33% - 20px);
  }
  .items table {
    width: 100%;
    border-collapse: collapse;
  }
  .items table, .items th, .items td {
    border: 1px solid #000;
  }
  .items th, .items td {
    padding: 5px;
    text-align: center;
  }
  .total {
    text-align: right;
  }
</style>

</head>
<body>
  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#receiptModal">Show Receipt</button>

  <div class="modal fade" id="receiptModal" tabindex="-1" role="dialog" aria-labelledby="receiptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="receiptModalLabel">Receipt</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="receipt">
            <div class="header">
              <h1>RECEIPT</h1>
              <img src="image/overrun.png" style="max-width: 100px;">
            </div>
            <div class="info">
              <div class="bill-to">
                <strong>FROM</strong><br><br>
                Overruns Sa Tisa Online Shop<br>
                P.Lozada St Binaobao, Bantayan, Cebu
              </div>
              <div class="ship-to">
                <strong>SHIP TO</strong><br><br>
                <?php echo $user_info['firstname'] . ' ' . $user_info['lastname']; ?><br>
                <?php echo $user_info['address']; ?><br>
                <?php echo $user_info['address2']; ?><br>
                <?php echo $user_info['contact_info']; ?>
              </div>
              <div class="receipt-info">
                <b>RECEIPT#:</b> &nbsp; <?php echo isset($user_info['pay_id']) ? $user_info['pay_id'] : 'N/A'; ?><br><br>
                <b>DATE:</b>&nbsp; <?php echo date('M d, Y'); ?><br><br>
              </div>
            </div>
            <div class="items">
              <table>
                <tr>
                  <th>Product</th>
                  <th>Price</th>
                  <th>Size</th>
                  <th>Color</th>
                  <th class="text-center">Quantity</th>
                  <th>Shipping</th>
                  <th>Subtotal</th>
                </tr>
                <?php if(isset($output['list'])) { echo $output['list']; } ?>
              </table>
            </div>
            <div class="total">
              <strong>Total: </strong> <?php if(isset($output['total'])) { echo $output['total']; } ?>
            </div>
          </div>
          <center>
            <button id="downloadBtn" class="btn btn-success">Download PDF</button>
          </center>
        </div>
      </div>
    </div>
  </div>

  <script>
  document.getElementById("downloadBtn").addEventListener("click", function() {
    window.open("generate_pdf.php?sale_id=<?php echo $sale_id; ?>", "_blank");
  });
  </script>
</body>