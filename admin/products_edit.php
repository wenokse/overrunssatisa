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

        if (isset($_POST['edit_colors'])) {
            $colors = $_POST['edit_colors'];
            $current_photos = $_POST['current_color_photos'];
            $new_photos = $_FILES['edit_color_photos'];
            $colorSet = [];
        
            // Delete existing colors
            $stmt = $conn->prepare("DELETE FROM product_colors WHERE product_id=:id");
            $stmt->execute(['id' => $id]);
        
            // Insert updated colors
            for ($i = 0; $i < count($colors); $i++) {
                $color = strtolower(trim($colors[$i])); // Normalize for comparison
                if (in_array($color, $colorSet)) {
                    throw new Exception("Duplicate color detected: " . $colors[$i]);
                }
                $colorSet[] = $color;
        
                $current_photo = $current_photos[$i];
                $new_photo = $new_photos['name'][$i];
        
                if (!empty($new_photo)) {
                    $photo_ext = pathinfo($new_photo, PATHINFO_EXTENSION);
                    $photo_filename = $slug . '_color_' . ($i + 1) . '.' . $photo_ext;
                    move_uploaded_file($new_photos['tmp_name'][$i], '../images/colors/' . $photo_filename);
                } else {
                    $photo_filename = $current_photo;
                }
        
                $stmt = $conn->prepare("INSERT INTO product_colors (product_id, color, photo) VALUES (:product_id, :color, :photo)");
                $stmt->execute(['product_id' => $id, 'color' => $colors[$i], 'photo' => $photo_filename]);
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