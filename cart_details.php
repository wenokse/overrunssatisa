<?php
    include 'includes/session.php';
    $conn = $pdo->open();

    $output = '';

    if(isset($_SESSION['user'])){
        if(isset($_SESSION['cart'])){
            foreach($_SESSION['cart'] as $row){
                $stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM cart WHERE user_id=:user_id AND product_id=:product_id AND size=:size");
                $stmt->execute(['user_id'=>$user['id'], 'product_id'=>$row['productid'], 'size'=>$row['size']]);
                $crow = $stmt->fetch();
                if($crow['numrows'] < 1){
                    $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity, size, color) VALUES (:user_id, :product_id, :quantity, :size, :color)");
                    $stmt->execute(['user_id'=>$user['id'], 'product_id'=>$row['productid'], 'quantity'=>$row['quantity'], 'size'=>$row['size'], 'color'=>$row['color']]);
                }
                else{
                    $stmt = $conn->prepare("UPDATE cart SET quantity=:quantity WHERE user_id=:user_id AND product_id=:product_id AND size=:size");
                    $stmt->execute(['quantity'=>$row['quantity'], 'user_id'=>$user['id'], 'product_id'=>$row['productid'], 'size'=>$row['size']]);
                }
            }
            unset($_SESSION['cart']);
        }

        try{
            $total = 0;
            $sql = "
            SELECT c.*, c.id AS cartid, c.size AS size, c.user_id AS cart_user_id, 
                   p.*, p.name AS prodname, u.store AS vendor_store, u.photo AS vendor_photo,
                   pc.photo AS color_photo, p.photo AS default_photo
            FROM cart c
            LEFT JOIN products p ON p.id = c.product_id 
            LEFT JOIN users u ON u.id = c.admin_id
            LEFT JOIN product_colors pc ON pc.product_id = c.product_id AND pc.color = c.color
            WHERE c.user_id = :user
            ORDER BY u.id";
            $stmt = $conn->prepare($sql);
            $stmt->execute(['user' => $user['id']]);
    
            $current_admin_id = null;
            
            foreach($stmt as $row){
                if(!empty($row['color_photo'])) {
                    $smallImage = 'images/colors/' . $row['color_photo'];
                    $largeImage = 'images/colors/large-' . $row['color_photo'];
                } else {
                    $smallImage = !empty($row['default_photo']) ? 'images/' . $row['default_photo'] : 'images/noimage.jpg';
                    $largeImage = !empty($row['default_photo']) ? 'images/large-' . $row['default_photo'] : 'images/noimage.jpg';
                }

                $subtotal = $row['price'] * $row['quantity'];
                $total += $subtotal;
    
                if ($current_admin_id !== $row['admin_id']) {
                    $current_admin_id = $row['admin_id'];
                    $output .= "
                    <tr>
                        <td colspan='9' class='bg-light'>
                            <a href='shop?id=" . $row['admin_id'] . "' style='color: inherit; text-decoration: none;'>
                                <strong>
                                    <img src='" . (!empty($row['vendor_photo']) ? 'images/'.$row['vendor_photo'] : 'images/noimage.jpg') . "' 
                                         style='width: 30px; height: 30px; object-fit: cover; border-radius: 50%;'> 
                                    {$row['vendor_store']}
                                </strong>
                            </a>
                        </td>
                    </tr>
                ";
                }
    
                $output .= "
                    <tr>
                        <td>
    <input type='checkbox' 
           class='product-checkbox' 
           data-id='".$row['cartid']."' 
           data-price='".$row['price']."' 
           data-quantity='".$row['quantity']."' 
           data-admin-id='".$row['admin_id']."'
           data-shipping='100'>
</td>
 <td><button type='button' data-id='".$row['cartid']."' class='btn btn-danger btn-flat cart_delete'><i class='fa fa-remove'></i></button></td>
                        <td>
                            <a href='product?product=".$row['slug']."'>
                                 <img class='pic' src='$smallImage'>
                                  <img class='picbig' src='$smallImage'>
                            </a>
                        </td>
                        <td>".$row['prodname']."</td>
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
                        <td>&#8369; ".number_format($subtotal, 2)."</td>
                    </tr>
                ";
            }

            if(empty($output)) {
                $output .= "
                    <tr>
                        <td colspan='9' align='center'>Shopping cart empty</td>
                    </tr>
                ";
            } else {
                $output .= "
                    <tr>
                        <td colspan='8' align='right'><b>Total</b></td>
                        <td><b>&#8369; ".number_format($total, 2)."</b></td>
                    </tr>
                ";
            }
        }
        catch(PDOException $e){
            $output .= $e->getMessage();
        }
    }
    else{
        if(count($_SESSION['cart']) != 0){
            $total = 0;
            foreach($_SESSION['cart'] as $row){
                $stmt = $conn->prepare("
                    SELECT *, products.name AS prodname, category.name AS catname, 
                           product_colors.photo AS color_photo, products.photo AS default_photo
                    FROM products 
                    LEFT JOIN category ON category.id=products.category_id 
                    LEFT JOIN product_colors ON product_colors.product_id=products.id 
                        AND product_colors.color=:color
                    WHERE products.id=:id
                ");
                $stmt->execute(['id'=>$row['productid'], 'color'=>$row['color']]);
                $product = $stmt->fetch();
                
                if(!empty($product['color_photo'])) {
                    $smallImage = 'images/colors/' . $product['color_photo'];
                    $largeImage = 'images/colors/large-' . $product['color_photo'];
                } else {
                    $smallImage = !empty($product['default_photo']) ? 'images/' . $product['default_photo'] : 'images/noimage.jpg';
                    $largeImage = !empty($product['default_photo']) ? 'images/large-' . $product['default_photo'] : 'images/noimage.jpg';
                }

                $subtotal = $product['price']*$row['quantity'];
                $total += $subtotal;
                $output .= "
                    <tr>
                        <td><button type='button' data-id='".$row['productid']."' class='btn btn-danger btn-flat cart_delete'><i class='fa fa-remove'></i></button></td>
                        <td>
                            <a href='product?product=".$product['slug']."'>
                                <img class='pic' src='$smallImage'>
                                <img class='picbig' src='$smallImage'>
                            </a>
                        </td>
                        <td>".$product['prodname']."</td>
                        <td>".$row['size']."</td>
                        <td>".$row['color']."</td>
                        <td>&#8369; ".number_format($product['price'], 2)."</td>
                        <td class='input-group text-center'>
                            <span class='input-group-btn'>
                                <button type='button' id='minus' class='btn btn-default btn-flat minus' data-id='".$row['productid']."'><i class='fa fa-minus'></i></button>
                            </span>
                            <input type='text' class='form-control text-center' value='".$row['quantity']."' id='qty_".$row['productid']."' data-stock='".$product['stock']."' readonly>
                            <span class='input-group-btn'>
                                <button type='button' id='add' class='btn btn-default btn-flat add' data-id='".$row['productid']."'><i class='fa fa-plus'></i></button>
                            </span>
                        </td>
                        <td>&#8369; ".number_format($subtotal, 2)."</td>
                    </tr>
                ";
            }

            $output .= "
                <tr>
                    <td colspan='7' align='right'><b>Total</b></td>
                    <td><b>&#8369; ".number_format($total, 2)."</b></td>
                </tr>
            ";
        }
        else{
            $output .= "
                <tr>
                    <td colspan='8' align='center'>Shopping cart empty</td>
                </tr>
            ";
        }
    }

    $pdo->close();
    echo json_encode($output);
?>