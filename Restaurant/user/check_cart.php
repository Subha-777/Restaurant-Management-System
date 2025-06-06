<?php
session_start();
include 'Configure.php';
if (!isset($_POST['user_id'])) {
    echo "empty";
    exit;  }
$user_id = $_POST['user_id'];
$query = "SELECT COUNT(*) as total FROM cart WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
if ($row['total'] > 0) {
    echo "not_empty"; // User has items in the cart
} else {
    echo "empty"; // Cart is empty
}?>
