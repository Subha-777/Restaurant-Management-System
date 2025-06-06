<?php
session_start();
require 'Configure.php'; // Database connection
// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
// Fetch user details
$userQuery = mysqli_query($conn, "SELECT username,phone,address FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($userQuery);
// Fetch cart items
$cartQuery = mysqli_query($conn, "SELECT c.food_item_id, c.quantity, f.food_name, f.price 
                                  FROM cart c 
                                  JOIN food_items f ON c.food_item_id = f.id 
                                  WHERE c.user_id = '$user_id'");
$totalPrice = 0;
$cartItems = [];
while ($row = mysqli_fetch_assoc($cartQuery)) {
    $cartItems[] = $row;
    $totalPrice += $row['price'] * $row['quantity'];
}
// Handle Confirm Order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $updateQuery = "UPDATE users SET address = '$address' WHERE id = '$user_id'";
    if (!mysqli_query($conn, $updateQuery)) {
        die("Error updating address: " . mysqli_error($conn));
    }
    $paymentMethod = $_POST['payment_method'];
    if ($paymentMethod === "Cash on Delivery") {
        header("Location: cashondelivery.php");
    } else {
        header("Location: online_payment.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
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
    <h2 style="color:goldenrod;">Payment Details</h2>
    <form method="post">
        <label>Name:</label>
        <input type="text" name="name" value="<?= $user['username'] ?>" required>
        <label>Phone Number:</label>
        <input type="text" name="phone" value="<?= $user['phone'] ?>" required>
        <label>Address:</label>
        <textarea type="text" name="address" value="<?= $user['address'] ?>" required></textarea>
        <label>Payment Method:</label>
        <select name="payment_method" required>
            <option value="Cash on Delivery">Cash on Delivery</option>
            <option value="Online Payment">Online Payment</option>
        </select>
        <div class="button">
        <button type="submit" id="co" name="confirm">Confirm Order</button>
        <button type="button" id="c" onclick="showCancelPopup()">Cancel</button>
</div>
</form>
</div>
<style>
    body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
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
}    .welcome { color: yellow; font-weight: bold; }
    .container {background:linear-gradient(100deg,gray,black,black,black,gray);   background-repeat: no-repeat;background-size: cover; text-align: center; padding: 50px; }
    form { display: inline-block; text-align: left; background:#e5b80b; padding: 20px; border-radius: 5px; width: 300px; }
    label { display: block; font-weight: bold; margin-top: 10px; }
    input, textarea, select { width: 90%; padding: 8px; margin-top: 5px;border-radius:5px;}
    button { margin-top: 15px; padding: 10px; cursor: pointer; width: 48%; } #c{background:red;color:white;border-radius:5px;}#co{background:green;color:white;border-radius:5px;}
    #co:hover{background:lightgreen;}#c:hover{background:#ff474c;}
    .popup { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.3); }
    .popup-content { text-align: center; }
    .popup-content button { margin-top: 10px; padding: 5px 10px; cursor: pointer; }
</style>
<script>
function showCancelPopup() {
    let popup = document.createElement("div");
    popup.innerHTML = `
        <div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);
                    background: white; padding: 20px; box-shadow: 0px 0px 10px black;
                    border-radius: 10px; text-align: center; z-index: 1000;">
            <p style="font-size: 18px; margin-bottom: 20px;">Are you sure you want to cancel the order?</p>
            <button onclick="closePopup()" style="background: red; color: white; padding: 10px 20px;
                    border: none; cursor: pointer; margin-right: 10px;">No</button>
            <button onclick="cancelOrder()" style="background: green; color: white; padding: 10px 20px;
                    border: none; cursor: pointer;">Yes</button>
        </div>
        <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5);
                    z-index: 999;" onclick="closePopup()"></div>
    `;
    document.body.appendChild(popup);
}function closePopup() {
    document.body.removeChild(document.body.lastChild);
}function cancelOrder() {
    window.location.href = "cancel_order.php"; // Redirect to cancel order page
}
</script>
</body>
</html>
