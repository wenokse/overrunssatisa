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

    // Allowed image file extensions
    $allowed_extensions = ['png', 'jpg', 'jpeg'];

    // Validate main product image
    if(!empty($filename)){
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if(!in_array($ext, $allowed_extensions)){
            $_SESSION['error'] = 'Invalid file type for main product image. Only PNG, JPG, and JPEG are allowed.';
            header('location: products');
            exit();
        }
    }

    // Add 10 to the original price
    $price += 10;

    $conn = $pdo->open();

    $stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM products WHERE slug=:slug");
    $stmt->execute(['slug'=>$slug]);
    $row = $stmt->fetch();

    if($row['numrows'] > 0){
        $_SESSION['error'] = 'Product already exists';
    }
    else{
        if(!empty($filename)){
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            // Generate unique filename using uniqid and the original extension
            $new_filename = uniqid('product_') . '.' . $ext;
            move_uploaded_file($_FILES['photo']['tmp_name'], '../images/' . $new_filename);	
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
                    if (!empty($color_photos['name'][$i])) {
                        $color_filename = $color_photos['name'][$i];
                        $color_ext = strtolower(pathinfo($color_filename, PATHINFO_EXTENSION));
                        
                        // Validate color image file type
                        if(!in_array($color_ext, $allowed_extensions)){
                            $conn->rollBack();
                            $_SESSION['error'] = 'Invalid file type for color image. Only PNG, JPG, and JPEG are allowed.';
                            header('location: products');
                            exit();
                        }
                        
                        $color_tmp = $color_photos['tmp_name'][$i];
                        
                        // Ensure upload directory exists
                        if (!is_dir('../images/colors')) {
                            mkdir('../images/colors', 0777, true);
                        }
                    
                        // Generate unique color photo filename
                        $new_color_filename = uniqid('color_') . '.' . $color_ext;
                        move_uploaded_file($color_tmp, '../images/colors/' . $new_color_filename);
                    
                        $stmt = $conn->prepare("INSERT INTO product_colors (product_id, color, photo) VALUES (:product_id, :color, :photo)");
                        $stmt->execute([
                            'product_id' => $product_id,
                            'color' => $colors[$i],
                            'photo' => $new_color_filename,
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