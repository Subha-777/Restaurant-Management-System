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
$cartQuery = mysqli_query($conn, "SELECT c.food_item_id, c.quantity, f.price
                                  FROM cart c
                                  JOIN food_items f ON c.food_item_id = f.id
                                  WHERE c.user_id = '$user_id'");

$cartItems = [];
$totalPrice = 0;
while ($row = mysqli_fetch_assoc($cartQuery)) {
    $cartItems[] = $row;
    $totalPrice += $row['price'] * $row['quantity'];
}

// Initialize popup message variables
$popupMessage = "";
$redirect = false;

// Process payment
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $card_number = $_POST['card_number'];
    $cvv = $_POST['cvv'];
    $card_name = $_POST['card_name'];

    if (empty($card_number) || empty($cvv) || empty($card_name)) {
        $popupMessage = "All fields are required!";
    } else {
        // Insert each cart item into orders table
        foreach ($cartItems as $item) {
            $food_item_id = $item['food_item_id'];
            $quantity = $item['quantity'];
            $price = $item['price'] * $quantity;

            $insertOrder = mysqli_query($conn, "INSERT INTO orders (user_id, food_item_id, quantity, total_price, status, payment_method, transaction_id,cart_name,cvv) 
                                                VALUES ('$user_id', '$food_item_id', '$quantity', '$price', 'ordered', 'Card Payment', '$card_number','$card_name','$cvv')");
        }

        // Clear the cart
        mysqli_query($conn, "DELETE FROM cart WHERE user_id = '$user_id'");

        $popupMessage = "Payment successful! Your order has been placed.";
        $redirect = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Card Payment</title>
    <style>
        body { font-family: Arial, sans-serif; background: linear-gradient(100deg, gray, black, black, black, gray); margin: 0; padding: 0; }
        .container { background: linear-gradient(100deg,gray,black,black,black,gray); background-repeat: no-repeat; background-size: cover; text-align: center; padding: 50px; }
        form { display: inline-block; text-align: left; background:#e5b80b; padding: 20px; border-radius: 5px; width: 300px; }
        label { display: block; font-weight: bold; margin-top: 10px; }
        input { width: 90%; padding: 8px; margin-top: 5px; border-radius: 5px; }
        button { background: violet; margin-top: 15px; padding: 10px; cursor: pointer; width: 48%; } 
        .total-price { font-size: 30px; color: goldenrod; margin-top: 20px; }

        /* Popup Styling */
        .popup-container {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            justify-content: center;
            align-items: center;
        }
        .popup-box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            width: 300px;
        }
        .popup-box button {
            background: goldenrod;
            color: white;
            padding: 10px;
            border: none;
            margin-top: 10px;
            cursor: pointer;
            width: 80px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 style="color:goldenrod;">Pay via Card</h2>

    <form method="POST">
        <label>Card Number</label>
        <input type="text" name="card_number" required><br><br>

        <label>CVV</label>
        <input type="text" name="cvv" required><br><br>

        <label>Name on the Card</label>
        <input type="text" name="card_name" required><br><br>

       <center><button type="submit">Pay Now!</button></center>
    </form>

    <h2 class="total-price">Total: ₹<?= number_format($totalPrice, 2) ?></h2>
</div>

<!-- Popup Message -->
<div class="popup-container" id="popup">
    <div class="popup-box">
        <p id="popup-message"></p>
        <button onclick="closePopup()">OK</button>
    </div>
</div>

<script>
    function showPopup(message, redirect = false) {
        document.getElementById("popup-message").innerText = message;
        document.getElementById("popup").style.display = "flex";

        if (redirect) {
            document.querySelector(".popup-box button").onclick = function() {
                window.location.href = 'myprofile.php';
            };
        }
    }

    function closePopup() {
        document.getElementById("popup").style.display = "none";
    }

    <?php if (!empty($popupMessage)) : ?>
        showPopup("<?= $popupMessage ?>", <?= $redirect ? 'true' : 'false' ?>);
    <?php endif; ?>
</script>

</body>
</html>
