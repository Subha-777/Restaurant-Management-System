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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Payment</title>
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
                    <?php endif;?>
        </ul>
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="header">
            <p>Welcome dear <?= $_SESSION['username']; ?>    <a style="color:red; text-decoration: none;" href="logout.php">Logout</a></p>
            </div>
            <?php endif; ?>
    </nav>
</header>
<div class="container">
    <h2 style="color:goldenrod;">Online Payment</h2>
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
        <h3 style="color:goldenrod;">Select Payment Method:</h3>
        <a href="cardOrder.php">Pay with card</a>
        <a href="upi_order.php">UPI Payment</a>
    </div>
</div>
<style> body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
    .header {
    color: black;
    background-color:gold;
    font-size: 17px;
    padding-left:10px;
}
.header .anger:hover{
    color:purple;
}
header {
    background-color: gold; /* Beige color */
    color: black;
    padding: 15px 0;
    position: sticky;
    background-attachment: fixed;
    top: 0;
    width: 100%;
}
header .container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 20px;
}
header h1 {
    margin: 0;
    color: black;
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
header nav ul li a:hover{
    color: whitesmoke;
}    .cart-items {
    display: flex;
    flex-wrap: wrap; /* Allow items to wrap */
    justify-content: center; /* Center items */
    gap: 30px; /* Adjust spacing */
    background: white;
    padding: 15px;
    margin: 10px 0;
    border-radius: 5px;
    box-shadow: 0 0 5px rgba(0,0,0,0.1);
    max-width: 90%; /* Restrict width */
    margin: auto; /* Center the section */
}
.cart-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 150px; /* Set a fixed width for each item */
    text-align: center;
}
.cart-item .food-img {
    width: 100px;
    height: 100px;
    border-radius: 10px;
}    .item-details p {
        margin: 5px 0;
    }
    a {
    color: black;
    text-decoration: none;
    font-weight: bold;
}
    .container { text-align: center; padding: 50px;background:linear-gradient(100deg, gray, black, black, black, gray);;  background-repeat: no-repeat;background-size: cover;  }
    .cart-items { margin-top: 20px; }
    table { width: 100%; border-collapse: collapse; background: white; }
    th, td { padding: 10px; border: 1px solid #ddd; text-align: center; }
    .food-img { width: 50px; height: 50px; border-radius: 5px; }
    .total-price { font-size: 50px; color: #d9534f; margin-top: 20px; }
    .payment-options { margin-top: 30px; }
    .payment-options a { padding: 10px; margin: 5px; cursor: pointer; font-size: 16px; border: none; border-radius: 5px; background: #28a745; color: white; }
</style>
</body>
</html>