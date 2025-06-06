<?php
session_start();
require 'Configure.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Move canceled items to the orders table
mysqli_query($conn, "INSERT INTO orders (user_id, food_item_id, quantity, total_price, status) 
                     SELECT user_id, food_item_id, quantity, (quantity * price), 'Canceled' 
                     FROM cart WHERE user_id = '$user_id'");

// Remove canceled items from the cart
mysqli_query($conn, "DELETE FROM cart WHERE user_id = '$user_id'");

// Redirect to MyCart
header("Location: mycart.php");
exit();
?>
