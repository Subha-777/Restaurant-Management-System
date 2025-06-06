<?php
session_start();
include 'Configure.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['cart_id'])) {
    $cart_id = intval($_POST['cart_id']);

    // Debugging: Check if cart_id is received
    if (!$cart_id) {
        echo "error: cart_id is missing";
        exit;
    }

    $deleteQuery = "DELETE FROM cart WHERE id = $cart_id";

    if (mysqli_query($conn, $deleteQuery)) {
        echo "success";
    } else {
        echo "error: " . mysqli_error($conn); // Debugging: Print error message
    }

    mysqli_close($conn);
} else {
    echo "error: Invalid request";
}
?>
