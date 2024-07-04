<?php
include 'includes/session.php';

if(isset($_SESSION['user'])){
    $output = array('count' => 0, 'list' => '');

    $conn = $pdo->open();

    $stmt = $conn->prepare("SELECT * FROM sales WHERE user_id=:user_id AND status=2 ORDER BY sales_date DESC");
    $stmt->execute(['user_id' => $_SESSION['user']['id']]);

    $count = $stmt->rowCount();
    $output['count'] = $count;

    if($count > 0){
        foreach($stmt as $row){
            $output['list'] .= "
                <li>
                    <a href='transaction_view.php?id=".$row['id']."'>
                        <i class='fa fa-truck text-aqua'></i> Your order #".$row['pay_id']." is on delivery
                    </a>
                </li>
            ";
        }
    }

    $pdo->close();
    echo json_encode($output);
}
?>
