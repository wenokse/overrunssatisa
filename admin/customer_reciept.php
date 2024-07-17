<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
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
                <!--name--> ".$user_info['firstname']." ".$user_info['lastname']."<br>
                <!--address--> ".$user_info['address']."<br>
                <!--address2--> ".$user_info['address2']."<br>
                <!--contact-->".$user_info['contact_info']."
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
    width: 210mm; 
    margin: auto;
    padding: 20mm; 
    font-family: 'Arial', sans-serif;
  }
  .header, .info, .items, .total {
    margin-bottom: 50px;
  }
  .header {
    text-align: center;
  }
  .info {
    display: flex;
    justify-content: space-between;
  }
  .info > div {
    flex-basis: calc(33% - 40px);
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
<div class="receipt">
  <div class="header">
    <img src="../images/LOGO.png" style="max-width: 100px;"><br>
    <h1>RECEIPT</h1>
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
  <button id="downloadBtn" style="background-color: green; color: white;">Download PDF</button>
</center>

<script>
document.getElementById("downloadBtn").addEventListener("click", function() {
  window.open("generate_pdf.php?sale_id=<?php echo $sale_id; ?>", "_blank");
});
</script>

</body>
</html>
