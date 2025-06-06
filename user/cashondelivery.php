<?php 
session_start();
require 'Configure.php'; // Database connection
// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
// Fetch cart items
$cartQuery = mysqli_query($conn, "SELECT c.food_item_id, c.quantity, f.food_name, f.price, f.food_image
                                  FROM cart c
                                  JOIN food_items f ON c.food_item_id = f.id
                                  WHERE c.user_id = '$user_id'");

$totalPrice = 0;
$cartItems = [];
while ($row = mysqli_fetch_assoc($cartQuery)) {
    $cartItems[] = $row;
    $totalPrice += $row['price'] * $row['quantity'];
}
?>
<?php
// Check if order button is clicked
if (isset($_POST['submit'])) {
    foreach ($cartItems as $item) {
        $food_id = $item['food_item_id'];
        $quantity = $item['quantity'];
        $total = $item['price'] * $quantity;

        mysqli_query($conn, "INSERT INTO orders (user_id, food_item_id, quantity, total_price, status, payment_method, transaction_id,cart_name,cvv) 
                             VALUES ('$user_id', '$food_id', '$quantity', '$total', 'Ordered', 'Cash on Delivery', 'N/A', 'N/A', 'N/A')");
    }
    mysqli_query($conn, "DELETE FROM cart WHERE user_id = '$user_id'");
    // Show popup message using JavaScript
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            showPopup('Your food order has been placed successfully!');
            setTimeout(() => { window.location.href = 'mycart.php'; }, 2000);
        });
    </script>";
}?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cash on Delivery</title>
</head>
<body>
<header>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="order.php">Order</a></li>
            <li><a href="mycart.php">MyCart</a></li>
            <li><a href="payment.php">Payment page</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="myprofile.php">MY Profile</a></li>
            <?php endif; ?>
        </ul>
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="header">
                <p>Welcome dear <?= $_SESSION['username']; ?> <a style="color:red; text-decoration: none;" href="logout.php">Logout</a></p>
            </div>
        <?php endif; ?>
    </nav>
</header>
<div class="container">
    <h2 style="color:goldenrod;text-align:center;">Cash On Delivery</h2>
        <div class="cart-items">
        <?php if (!empty($cartItems)) { ?>
            <?php foreach ($cartItems as $item) { ?>
                <div class="cart-item">
                    <img src="<?= $item['food_image'] ?>" alt="<?= $item['food_name'] ?>" class="food-img">
                    <div class="item-details">
                        <p class="food-name"><strong><?= $item['food_name'] ?></strong></p>
                        <p>Quantity: <?= $item['quantity'] ?></p>
                        <p>Price: ₹<?= number_format($item['price'] * $item['quantity'], 2) ?></p>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <p>No items in your cart.</p>
        <?php } ?>
    </div>
    <h2 class="total-price">Total: ₹<?= number_format($totalPrice, 2) ?></h2>
        <div class="payment-options">
        <form method="POST">
            <button type="submit" name="submit">Order now!</button>
        </form>
    </div>
</div>
<!-- Popup Message -->
<div id="popup" class="popup">
    <div class="popup-content">
        <p id="popup-message"></p>
        <button onclick="closePopup()">OK</button>
    </div>
</div>
<style>
body { font-family: Arial, sans-serif; margin: 0; padding: 0;
    background: linear-gradient(100deg, gray, black, black, black, gray); }
.header {
    color: black;
    background-color:gold;
    font-size: 17px;
    padding-left:10px;
}
header {
    background-color: gold;
    color: black;
    padding: 15px 0;
    position: sticky;
    top: 0;
    width: 100%;
}
header nav ul {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
}
header nav ul li {
    margin-left: 20px;
}
header nav ul li a {
    color: black;
    text-decoration: none;
    font-weight: bold;
}
header nav ul li a:hover {
    color: whitesmoke;
}
.cart-items {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 30px;
    background: white;
    padding: 15px;
    margin: 10px 0;
    border-radius: 5px;
    max-width: 90%;
    margin: auto;
}
.cart-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 150px;
    text-align: center;
}
.cart-item .food-img {
    width: 100px;
    height: 100px;
    border-radius: 10px;
}
.total-price {
    font-size: 50px;
    text-align:center;
    color: #d9534f;
    margin-top: 20px;
}
.payment-options {
    margin-top: 30px;
}
.payment-options button {
    padding: 10px;
    margin: 5px;
    cursor: pointer;
    font-size: 16px;
    border: none;
    border-radius: 5px;
    background: #28a745;
    color: white;
    display: block;  /* Makes it a block-level element */
    margin: 0 auto;  /* Centers it horizontally */
    text-align: center; /* Ensures the text inside is centered */
    margin-bottom:30px;
}
.popup {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0,0,0,0.3);
}
.popup-content {
    text-align: center;
}
.popup-content button {
    margin-top: 10px;
    padding: 5px 10px;
    cursor: pointer;
}
</style>
<script>
function showPopup(message) {
    document.getElementById("popup-message").innerText = message;
    document.getElementById("popup").style.display = "block";
    setTimeout(() => { closePopup(); }, 2000);
}
function closePopup() {
    document.getElementById("popup").style.display = "none";
}
</script></body></html>
