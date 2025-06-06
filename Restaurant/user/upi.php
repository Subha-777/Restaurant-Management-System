<?php 
include 'Configure.php';
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['booking_details'])) {
    $_SESSION['popup_message'] = "Booking details missing. Please try again.";
    $_SESSION['redirect_url'] = "booking.php";
    header("Location: upi.php");
    exit;
}
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    $_SESSION['popup_message'] = "User ID is missing. Please log in again.";
    $_SESSION['redirect_url'] = "login.php";
    header("Location: upi.php");
    exit;
}
// Fetch booking details
$booking_details = $_SESSION['booking_details'];
$name = $booking_details['name'] ?? '';
$email = $booking_details['email'] ?? '';
$phone = $booking_details['phone'] ?? '';
$date = date("Y-m-d H:i:s"); // Today's date
$time = "07:00 PM - 10:00 PM"; // Fixed time
$num_guests = $booking_details['guests'] ?? 1;
$special_requests = $booking_details['special_requests'] ?? '';
$table_id = $booking_details['table_id'] ?? 0;
$table_name = $booking_details['table_name'] ?? '';
// Fetch price from book_tables
$priceQuery = "SELECT price FROM book_tables WHERE id = '$table_id'";
$priceResult = mysqli_query($conn, $priceQuery);
$price = ($priceRow = mysqli_fetch_assoc($priceResult)) ? $priceRow['price'] : 0;
// Handle UPI Payment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['payment_method'])) {
    $payment_method = $_POST['payment_method'];
    $upi_id=$_POST['upi_id'];
    $insertQuery = "INSERT INTO reservation (user_id, table_name, name,email,phone,date, time, num_guests, special_requests, status, price, payment_method,transaction_id,cart_name,cvv) 
                    VALUES ('$user_id', '$table_name','$name','$email','$phone', '$date', '$time', '$num_guests', '$special_requests', 'Reserved', '$price', '$payment_method','$upi_id','N/A','N/A')";
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
    header("Location: upi.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UPI Payment</title>
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
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
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
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }
        .pay-now {
            background: #28a745;
            color: white;
        }
        .pay-now:hover {
            background: #218838;
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
    <h3>Pay via UPI</h3>
    <form id="upiForm" method="POST">
        <label>UPI ID :</label>
        <input type="text" name="upi_id" required>
        <input type="hidden" name="payment_method" id="payment_method" value="UPI Payment">
        <button type="button" class="pay-now" onclick="submitPayNow()">Pay Now!</button>
<hr>
<h4>Scan the QR Code</h4>
<img src="QR.jpg" alt="QR Code" width="200">
    </form>
</div>
<script>
    function submitPayNow() {
        document.getElementById('upiForm').submit();
    }
</script>
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
    }
    <?php if (isset($_SESSION['popup_message'])) : ?>
        showPopup("<?php echo $_SESSION['popup_message']; ?>", "<?php echo $_SESSION['redirect_url'] ?? ''; ?>");
        <?php unset($_SESSION['popup_message']); unset($_SESSION['redirect_url']); ?>
    <?php endif; ?>
</script>
</body>
</html>
