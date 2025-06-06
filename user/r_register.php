<?php
// register.php
include 'Configure.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password=$_POST['confirm_password'];
    $phone = $_POST['phone'];

    // Check if email already exists
    $check_email = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn,$check_email);

    if (mysqli_num_rows($result) > 0) {
        echo "<script>alert('Error: Email already exists.'); window.location = 'r_register.html';</script>";
    } 
    elseif($password!==$confirm_password) {
        echo "<script>alert('Error: Incorrect Password.'); window.location = 'r_register.html';</script>";
    }else{
        // Insert new user into database
        $sql = "INSERT INTO users (username, email, password,phone) VALUES ('$username', '$email', '$password', '$phone')";
        if (mysqli_query($conn,$sql)) {
            // Redirect to login page after successful registration
            header('Location: r_login.html');
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
$conn->close();
?>