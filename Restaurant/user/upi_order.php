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

// Function to process payment
function processPayment($conn, $user_id, $payment_option) {
    global $cartItems, $totalPrice;

    foreach ($cartItems as $item) {
        $food_id = $item['food_item_id'];
        $quantity = $item['quantity'];
        $price = $item['price'] * $quantity;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $upi_id=$_POST['upi_id'];
        $insertOrder = "INSERT INTO orders (user_id, food_item_id, quantity, total_price, status, payment_method, transaction_id,cart_name,cvv) 
                        VALUES ('$user_id', '$food_id', '$quantity', '$price', 'ordered', 'UPI Payment', '$upi_id','N/A','N/A')";
        mysqli_query($conn, $insertOrder);
    }
}
    // Clear cart after successful order
    mysqli_query($conn, "DELETE FROM cart WHERE user_id = '$user_id'");

    // Set session variable to show popup after reload
    $_SESSION['payment_success'] = true;
    header("Location: upi_order.php");
    exit();
}

// Handle payment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['pay_now'])) {
        $upi_id = trim($_POST['upi_id']);
        if (!empty($upi_id)) {
            processPayment($conn, $user_id, 'UPI Payment');
        } else {
            $_SESSION['error_message'] = "Please enter your UPI ID.";
            header("Location: upi_order.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UPI Payment</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f5f5f5; text-align: center; padding: 50px; }
        .payment-container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); width: 350px; margin: auto; }
        .payment-container h2 { margin-bottom: 20px; }
        input[type="text"] { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px; }
        .pay-btn { background: green; color: white; padding: 10px; border: none; border-radius: 5px; cursor: pointer; width: 100%; }
        .pay-btn:hover { background: darkgreen; }
        .support-buttons { margin-top: 20px; }
        .support-buttons button { background: blue; color: white; padding: 10px; border: none; border-radius: 5px; margin: 5px; cursor: pointer; width: 30%; }
        .support-buttons button:hover { background: darkblue; }

        /* Popup Message */
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            text-align: center;
            width: 300px;
        }
        .popup button {
            background: green;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        .popup button:hover { background: darkgreen; }
        .popup h3 { margin-bottom: 10px; }
        .popup-error { background: red; color: white; }
    </style>
</head>
<body>

<div class="payment-container">
    <h2>Pay via UPI</h2>
    <form method="POST">
        <label>UPI ID:</label>
        <input type="text" name="upi_id" required>
        <button type="submit" name="pay_now" class="pay-btn">Pay Now!</button>
    </form>

    <hr>

    <h3>Scan the QR Code</h3>
    <img src="QR.jpg" alt="QR Code" width="200">
</div>


<!-- Success Popup -->
<div id="popup-message" class="popup">
    <h3>Payment Successful!</h3>
    <p>Your order has been placed.</p>
    <button onclick="redirectToProfile()">OK</button>
</div>

<!-- Error Popup -->
<div id="popup-message-error" class="popup popup-error">
    <h3>Error!</h3>
    <p id="error-text"></p>
    <button onclick="closeErrorPopup()">OK</button>
</div>

<script>
    function redirectToProfile() {
        window.location.href = "myprofile.php";
    }

    function closeErrorPopup() {
        document.getElementById("popup-message-error").style.display = "none";
    }

    // Show success popup if session variable is set
    <?php if (isset($_SESSION['payment_success'])): ?>
        document.getElementById("popup-message").style.display = "block";
        <?php unset($_SESSION['payment_success']); ?> // Clear session variable
    <?php endif; ?>

    // Show error popup if session variable is set
    <?php if (isset($_SESSION['error_message'])): ?>
        document.getElementById("popup-message-error").style.display = "block";
        document.getElementById("error-text").innerText = "<?php echo $_SESSION['error_message']; ?>";
        <?php unset($_SESSION['error_message']); ?> // Clear session variable
    <?php endif; ?>
</script>
</body>
</html>
