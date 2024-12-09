<?php
include 'includes/session.php';
include 'includes/slugify.php';

if(isset($_POST['edit'])){
    $id = $_POST['id'];
    $name = $_POST['name'];
    $slug = slugify($name);
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $description = $_POST['description'];

    $conn = $pdo->open();

    try{
        $conn->beginTransaction();

        $stmt = $conn->prepare("UPDATE products SET name=:name, slug=:slug, category_id=:category, price=:price, stock=:stock, description=:description WHERE id=:id");
        $stmt->execute(['name'=>$name, 'slug'=>$slug, 'category'=>$category, 'price'=>$price, 'stock'=>$stock, 'description'=>$description, 'id'=>$id]);

        // Handle colors
        if(isset($_POST['edit_colors'])){
            $colors = $_POST['edit_colors'];
            $current_photos = $_POST['current_color_photos'] ?? [];
            $new_photos = $_FILES['edit_color_photos'];

            // Delete existing colors
            $stmt = $conn->prepare("DELETE FROM product_colors WHERE product_id=:id");
            $stmt->execute(['id'=>$id]);

            // Insert updated colors
            for($i = 0; $i < count($colors); $i++){
                $color = $colors[$i];
                $current_photo = $current_photos[$i] ?? '';
                $new_photo_name = $new_photos['name'][$i];
                
                // Ensure upload directory exists
                if (!is_dir('../images/colors')) {
                    mkdir('../images/colors', 0777, true);
                }

                if(!empty($new_photo_name)){
                    // Generate unique filename for the color photo
                    $color_ext = pathinfo($new_photo_name, PATHINFO_EXTENSION);
                    $new_color_filename = uniqid('color_') . '.' . $color_ext;
                    move_uploaded_file($new_photos['tmp_name'][$i], '../images/colors/' . $new_color_filename);
                } else {
                    $new_color_filename = $current_photo;
                }

                // Only insert if color is not empty
                if (!empty($color)) {
                    $stmt = $conn->prepare("INSERT INTO product_colors (product_id, color, photo) VALUES (:product_id, :color, :photo)");
                    $stmt->execute(['product_id'=>$id, 'color'=>$color, 'photo'=>$new_color_filename]);
                }
            }
        }

        // Handle sizes
        if(isset($_POST['edit_sizes'])){
            $sizes = $_POST['edit_sizes'];
            
            // Delete existing sizes
            $stmt = $conn->prepare("DELETE FROM product_sizes WHERE product_id=:id");
            $stmt->execute(['id'=>$id]);

            // Insert updated sizes
            foreach($sizes as $size){
                if(!empty($size)){
                    $stmt = $conn->prepare("INSERT INTO product_sizes (product_id, size) VALUES (:product_id, :size)");
                    $stmt->execute(['product_id'=>$id, 'size'=>$size]);
                }
            }
        }

        $conn->commit();
        $_SESSION['success'] = 'Product updated successfully';
    }
    catch(PDOException $e){
        $conn->rollBack();
        $_SESSION['error'] = $e->getMessage();
    }

    $pdo->close();
}
else{
    $_SESSION['error'] = 'Fill up edit product form first';
}

header('location: products');
?>
