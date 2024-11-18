<?php
include 'includes/session.php';
include 'includes/slugify.php';

if(isset($_POST['add'])){
    $name = $_POST['name'];
    $slug = slugify($name);
    $category = $_POST['category'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $filename = $_FILES['photo']['name'];
    $user_id = $_POST['user_id']; // Get the user ID from the form

    // Add 10 to the original price
    $price += 10;

    $conn = $pdo->open();

    $stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM products WHERE slug=:slug");
    $stmt->execute(['slug'=>$slug]);
    $row = $stmt->fetch();

    if($row['numrows'] > 0){
        $_SESSION['error'] = 'Product already exist';
    }
    else{
        if(!empty($filename)){
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $new_filename = $slug.'.'.$ext;
            move_uploaded_file($_FILES['photo']['tmp_name'], '../images/'.$new_filename);	
        }
        else{
            $new_filename = '';
        }

        try{
            $conn->beginTransaction();

            $stmt = $conn->prepare("INSERT INTO products (category_id, name, description, slug, price, stock, photo, user_id) VALUES (:category, :name, :description, :slug, :price, :stock, :photo, :user_id)");
            $stmt->execute(['category'=>$category, 'name'=>$name, 'description'=>$description, 'slug'=>$slug, 'price'=>$price, 'stock'=>0, 'photo'=>$new_filename, 'user_id'=>$user_id]);
            $product_id = $conn->lastInsertId();

            if(isset($_POST['colors']) && isset($_FILES['color_photos'])) {
                $colors = $_POST['colors'];
                $color_photos = $_FILES['color_photos'];
                
                for($i = 0; $i < count($colors); $i++) {
                    if(!empty($color_photos['name'][$i])) {
                        $color_filename = $color_photos['name'][$i];
                        $color_tmp = $color_photos['tmp_name'][$i];
                        $color_ext = pathinfo($color_filename, PATHINFO_EXTENSION);
                        $new_color_filename = $slug . '_color_' . ($i + 1) . '.' . $color_ext;
                        
                        if(!is_dir('../images/colors')) {
                            mkdir('../images/colors', 0777, true);
                        }
                        
                        move_uploaded_file($color_tmp, '../images/colors/' . $new_color_filename);
                        
                        $stmt = $conn->prepare("INSERT INTO product_colors (product_id, color, photo) 
                                             VALUES (:product_id, :color, :photo)");
                        $stmt->execute([
                            'product_id' => $product_id,
                            'color' => $colors[$i],
                            'photo' => $new_color_filename
                        ]);
                    }
                }
            }
            if(isset($_POST['sizes'])) {
                $sizes = $_POST['sizes'];
                
                foreach($sizes as $size) {
                    if(!empty($size)) {
                        $stmt = $conn->prepare("INSERT INTO product_sizes (product_id, size) 
                                             VALUES (:product_id, :size)");
                        $stmt->execute([
                            'product_id' => $product_id,
                            'size' => $size
                        ]);
                    }
                }
            }


            $conn->commit();
            $_SESSION['success'] = 'Product added successfully';
        }
        catch(PDOException $e){
            $conn->rollBack();
            $_SESSION['error'] = $e->getMessage();
        }
    }

    $pdo->close();
}
else{
    $_SESSION['error'] = 'Fill up product form first';
}

header('location: products');
?>
