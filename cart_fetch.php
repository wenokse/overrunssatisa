<?php
include 'includes/session.php';
$conn = $pdo->open();

$output = array('list'=>'', 'count'=>0, 'total'=>0);

if(isset($_SESSION['user'])){
    try{
        $stmt = $conn->prepare("SELECT c.*, p.name AS prodname, p.price AS price, p.photo, ca.name AS catname 
                                FROM cart c 
                                LEFT JOIN products p ON p.id = c.product_id 
                                LEFT JOIN category ca ON ca.id = p.category_id 
                                WHERE c.user_id = :user_id");
        $stmt->execute(['user_id'=>$user['id']]);

        foreach($stmt as $row){
            $output['count'] += $row['quantity'];
            $subtotal = $row['price'] * $row['quantity'];
            $output['total'] += $subtotal;

            $image = (!empty($row['photo'])) ? 'images/'.$row['photo'] : 'images/noimage.jpg';
            $productname = (strlen($row['prodname']) > 30) ? substr_replace($row['prodname'], '...', 27) : $row['prodname'];
            
            $output['list'] .= "
                <li>
                    <a href='cart_view?product=".$row['product_id']."'>
                        <div class='pull-left'>
                            <img src='".$image."' class='thumbnail' alt='User Image'>
                        </div>
                        <h4>
                            <b>".$row['catname']."</b>
                            <small>&times; ".$row['quantity']."</small>
                        </h4>
                        <p>".$productname."</p>
                    </a>
                </li>
            ";
        }
    }
    catch(PDOException $e){
        $output['message'] = $e->getMessage();
    }
}
else{
    if(!isset($_SESSION['cart'])){
        $_SESSION['cart'] = array();
    }

    if(empty($_SESSION['cart'])){
        $output['count'] = 0;
        $output['total'] = 0;
    }
    else{
        foreach($_SESSION['cart'] as $row){
            $output['count'] += $row['quantity'];
            $stmt = $conn->prepare("SELECT *, products.name AS prodname, category.name AS catname FROM products LEFT JOIN category ON category.id=products.category_id WHERE products.id=:id");
            $stmt->execute(['id'=>$row['productid']]);
            $product = $stmt->fetch();
            
            $subtotal = $product['price'] * $row['quantity'];
            $output['total'] += $subtotal;

            $image = (!empty($product['photo'])) ? 'images/'.$product['photo'] : 'images/noimage.jpg';
            $output['list'] .= "
                <li>
                    <a href='cart_view?product=".$product['slug']."'>
                        <div class='pull-left'>
                            <img src='".$image."' class='img-circle' alt='User Image'>
                        </div>
                        <h4>
                            <b>".$product['catname']."</b>
                            <small>&times; ".$row['quantity']."</small>
                        </h4>
                        <p>".$product['prodname']."</p>
                    </a>
                </li>
            ";
        }
    }
}

$pdo->close();
echo json_encode($output);
?>