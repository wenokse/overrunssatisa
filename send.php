<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve email from form input
    $email = $_POST['email'];

    // Connect to your database (Replace 'localhost', 'username', 'password', and 'database' with your actual database credentials)
    $conn = mysqli_connect('localhost', 'username', 'password', 'database');

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Prepare SQL statement to retrieve password for the given email
    $sql = "SELECT password FROM users WHERE email = ?";

    // Prepare and bind parameters
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);

    // Execute query
    mysqli_stmt_execute($stmt);

    // Bind result variables
    mysqli_stmt_bind_result($stmt, $password);

    // Fetch value
    mysqli_stmt_fetch($stmt);

    // Close statement
    mysqli_stmt_close($stmt);

    // Close connection
    mysqli_close($conn);

    if ($password) {
        // Send password via email
        $to = $email;
        $subject = "Your Password";
        $message = "Your password is: $password";
        $headers = "From: your_email@example.com"; // Replace with your email

        // Send email
        $mail_sent = mail($to, $subject, $message, $headers);

        if ($mail_sent) {
            echo "Password sent to your email.";
        } else {
            echo "Failed to send password. Please try again later.";
        }
    } else {
        echo "No user found with that email address.";
    }
}
?>
