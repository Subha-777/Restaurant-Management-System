<?php
// login.php
session_start();
include 'Configure.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // SQL to get the user details
    $sql ="SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
    
    // Move guest_id items to user_id in the cart
        if (isset($_COOKIE['guest_id']))
        {
            $guest_id = $_COOKIE['guest_id'];
            $user_id = $user['id'];

            // Update cart table: change guest_id to user_id
            $updateCart = "UPDATE cart SET user_id = '$user_id', guest_id = NULL WHERE guest_id = '$guest_id'";
            mysqli_query($conn, $updateCart);
        
            // Remove guest_id cookie
            setcookie('guest_id', '', time() - 3600, "/");
        }

        // Redirect to the homepage or any page after login
        header('Location: reservation.php');
        exit();
    }
    else
    {
        echo "<script>alert('Error: Invalid Username or Password'); window.location = 'r_login.html';</script>";
    }
}

$conn->close();
?>