<?php
include 'Configure.php';
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['booking_details'])) {
    $_SESSION['popup_message'] = "Booking details missing. Please try again.";
    $_SESSION['redirect_url'] = "booking.php";
    header("Location: cardpayment.php");
    exit;
}
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    $_SESSION['popup_message'] = "User ID is missing. Please log in again.";
    $_SESSION['redirect_url'] = "login.php";
    header("Location: cardpayment.php");
    exit;
}
// Check if the user exists
$checkUserQuery = "SELECT id FROM users WHERE id = '$user_id'";
$checkResult = mysqli_query($conn, $checkUserQuery);
if (mysqli_num_rows($checkResult) == 0) {
    $_SESSION['popup_message'] = "Invalid user. Please try again.";
    $_SESSION['redirect_url'] = "booking.php";
    header("Location: cardpayment.php");
    exit;
}
// Fetch booking details
$booking_details = $_SESSION['booking_details'];
$name=$booking_details['name']??'';
$email=$booking_details['email']??'';
$phone=$booking_details['phone']??'';
$date = date('Y-m-d'); // Today's date
$time = "07:00 PM - 10:00 PM"; // Fixed time
$num_guests = $booking_details['guests'] ?? 1;
$special_requests = $booking_details['special_requests'] ?? '';
$table_id = $booking_details['table_id'] ?? 0;
$booking_details = $_SESSION['booking_details'] ?? [];
$table_name = $booking_details['table_name'] ?? '';

// Fetch price from book_tables
$priceQuery = "SELECT price FROM book_tables WHERE id = '$table_id'";
$priceResult = mysqli_query($conn, $priceQuery);
$price = ($priceRow = mysqli_fetch_assoc($priceResult)) ? $priceRow['price'] : 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $card_name = $_POST['card_name'];
    $card_id = $_POST['card_number'];
    $cvv=$_POST['cvv'];
    $date = date("Y-m-d H:i:s");
    // Insert correct price into reservation table
    $insertQuery = "INSERT INTO reservation (user_id,table_name,name,email,phone,  date, time, num_guests, special_requests, status, price, payment_method,transaction_id,cart_name,cvv) 
                    VALUES ('$user_id','$table_name','$name','$email','$phone', '$date', '$time', '$num_guests', '$special_requests', 'Reserved', '$price','Card Payment', '$card_id','$card_name','$cvv')";
        if (mysqli_query($conn, $insertQuery)) {
        // Mark table as "Not Available"
        $updateQuery = "UPDATE book_tables SET status = 'Not Available' WHERE id = '$table_id'";
        mysqli_query($conn, $updateQuery);
        $_SESSION['popup_message'] = "Payment successful! Your table is reserved.";
        $_SESSION['redirect_url'] = "MyProfile.php";
    } else {
        $_SESSION['popup_message'] = "Error processing payment. Please try again.";
        $_SESSION['redirect_url'] = null;
    }
    header("Location: cardpayment.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Card Payment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .payment-container {
            max-width: 400px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        input {
            width: 95%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        button {
            width: 100%;
            background: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }
        button:hover {
            background: #0056b3;
        }
        /* Popup Styling */
        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
        }
        .popup-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .popup-box button {
            background: #28a745;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="payment-container">
    <center><h3>Pay via New Card</h3></center>
    <form method="POST">
        <label>Card Number :</label>
        <input type="text" name="card_number" required>
        <label>CVV :</label>
        <input type="text" name="cvv" required>
        <label>Name on the Card :</label>
        <input type="text" name="card_name" required>
        <button type="submit">Pay Now</button>
    </form>
</div>
<!-- Popup Message -->
<div class="popup-overlay" id="popupOverlay">
    <div class="popup-box">
        <p id="popupMessage"></p>
        <button onclick="closePopup()">OK</button>
    </div>
</div>
<script>
    function showPopup(message, redirect = null) {
        document.getElementById('popupMessage').innerText = message;
        document.getElementById('popupOverlay').style.display = 'flex';
        if (redirect) {
            document.querySelector('.popup-box button').onclick = function() {
                window.location.href = redirect;
            };
        } else {
            document.querySelector('.popup-box button').onclick = function() {
                closePopup();
            };
        }
    }
    function closePopup() {
        document.getElementById('popupOverlay').style.display = 'none';
    }    // Check if there's a PHP session message
    <?php if (isset($_SESSION['popup_message'])) : ?>
        showPopup("<?php echo $_SESSION['popup_message']; ?>", "<?php echo $_SESSION['redirect_url'] ?? ''; ?>");
        <?php unset($_SESSION['popup_message']); unset($_SESSION['redirect_url']); ?>
    <?php endif; ?>
</script>
</body>
</html>
