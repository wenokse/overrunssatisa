<?php
    include 'includes/session.php';
    $conn = $pdo->open();

    $output = '';

    if(isset($_SESSION['user'])){
        if(isset($_SESSION['cart'])){
            foreach($_SESSION['cart'] as $row){
                $stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM cart WHERE user_id=:user_id AND product_id=:product_id");
                $stmt->execute(['user_id'=>$user['id'], 'product_id'=>$row['productid']]);
                $crow = $stmt->fetch();
                if($crow['numrows'] < 1){
                    $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (:user_id, :product_id, :quantity)");
                    $stmt->execute(['user_id'=>$user['id'], 'product_id'=>$row['productid'], 'quantity'=>$row['quantity']]);
                }
                else{
                    $stmt = $conn->prepare("UPDATE cart SET shipping=:shipping, quantity=:quantity WHERE user_id=:user_id AND product_id=:product_id");
                    $stmt->execute(['shipping'=>$row['shipping'], 'quantity'=>$row['quantity'], 'user_id'=>$user['id'], 'product_id'=>$row['productid']]);
                }
            }
            unset($_SESSION['cart']);
        }

        if(isset($_SESSION['user'])){
            try{
                $total = 0;
                $sql = "
                SELECT *, cart.id AS cartid, cart.size AS size, cart.user_id AS cart_user_id
                FROM cart 
                LEFT JOIN products ON products.id = cart.product_id 
                WHERE cart.user_id = :user";
                $stmt = $conn->prepare($sql);
                $stmt->execute(['user' => $user['id']]);
                foreach($stmt as $row){
                    $image = (!empty($row['photo'])) ? 'images/'.$row['photo'] : 'images/noimage.jpg';
                    $subtotal = $row['price'] * $row['quantity'] + $row['shipping'];
                    $total += $subtotal;
                    $output .= "
                        <tr>
                            <td><button type='button' data-id='".$row['cartid']."' class='btn btn-danger btn-flat cart_delete'><i class='fa fa-remove'></i></button></td>
                            <td>
                                <img class='pic' src='".$image."'>
                                <img class='picbig' src='".$image."'>
                            </td>
                            <td>".$row['name']."</td>
                            <td>".$row['size']."</td>
                            <td>".$row['color']."</td>
                            <td>&#8369; ".number_format($row['price'], 2)."</td>
                            <td class='input-group text-center'>
                                <span class='input-group-btn'>
                                    <button type='button' id='minus' class='btn btn-default btn-flat minus' data-id='".$row['cartid']."'><i class='fa fa-minus'></i></button>
                                </span>
                                <input type='text' class='form-control text-center' value='".$row['quantity']."' id='qty_".$row['cartid']."' data-stock='".$row['stock']."' readonly>
                                <span class='input-group-btn'>
                                    <button type='button' id='add' class='btn btn-default btn-flat add' data-id='".$row['cartid']."'><i class='fa fa-plus'></i></button>
                                </span>
                            </td>
                            <td>&#8369; ".number_format($row['shipping'], 2)."</td>
                            <td>&#8369; ".number_format($subtotal, 2)."</td>
                        </tr>
                    ";
                }
                $output .= "
                    <tr>
                        <td colspan='5' align='right'><b>Total</b></td>
                        <td><b>&#8369; ".number_format($total, 2)."</b></td>
                    </tr>
                ";
            }
            catch(PDOException $e){
                $output .= $e->getMessage();
            }
        
        
        catch(PDOException $e){
            $output .= $e->getMessage();
        }

    }
    else{
        if(count($_SESSION['cart']) != 0){
            $total = 0;
            foreach($_SESSION['cart'] as $row){
                $stmt = $conn->prepare("SELECT *, products.name AS prodname, category.name AS catname FROM products LEFT JOIN category ON category.id=products.category_id WHERE products.id=:id");
                $stmt->execute(['id'=>$row['productid']]);
                $product = $stmt->fetch();
                $image = (!empty($product['photo'])) ? 'images/'.$product['photo'] : 'images/noimage.jpg';
                $subtotal = $product['price']*$row['quantity']*$row['shipping'];
                $total += $subtotal;
                $output .= "
                    <tr>
                        <td><button type='button' data-id='".$row['productid']."' class='btn btn-danger btn-flat cart_delete'><i class='fa fa-remove'></i></button></td>
                        <td>
                            <img class='pic' src='".$image."'>
                            <img class='picbig' src='".$image."'>
                        </td>
                        <td>".$product['name']."</td>
                        <td>".$product['size']."</td>
                        <td>".$row['color']."</td>
                        <td>&#8369; ".number_format($product['price'], 2)."</td>
                        <td class='input-group'  text-center'>
                            <span class='input-group-btn'>
                                <button type='button' id='minus' class='btn btn-default btn-flat minus' data-id='".$row['productid']."'><i class='fa fa-minus'></i></button>
                            </span>
                            <input type='text' class='form-control  text-center'' value='".$row['quantity']."' id='qty_".$row['productid']."'  data-stock='".$product['stock']."' readonly>
                            <span class='input-group-btn'>
                                <button type='button' id='add' class='btn btn-default btn-flat add' data-id='".$row['productid']."'><i class='fa fa-plus'></i>
                                </button>
                            </span>
                        </td>
                        <td>&#8369; ".number_format($subtotal, 2)."</td>
                    </tr>
                ";
                
            }

            $output .= "
                <tr>
                    <td colspan='5' align='right'><b>Total</b></td>
                    <td><b>&#8369; ".number_format($total, 2)."</b></td>
                <tr>
            ";
        }

        else{
            $output .= "
                <tr>
                    <td colspan='6' align='center'>Shopping cart empty</td>
                <tr>
            ";
        }
        
    }
}

    $pdo->close();
    echo json_encode($output);

?>
