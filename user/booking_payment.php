<?php 
include 'Configure.php'; // Database connection
session_start();
// Check if booking details are set
if (!isset($_SESSION['booking_details'])) {
    echo "<script>alert('No booking details found. Please select a table first.'); window.location.href='booking.php';</script>";
    exit();
}
$booking = $_SESSION['booking_details'];
$tableId = $booking['table_id'];
$date = date('Y-m-d'); // Get today's date
$guests = $booking['guests'];
// Fetch table details (name, price, image) from the database
$query = "SELECT table_name,table_type, price, table_image FROM book_tables WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $tableId);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt,$tableId, $tableName, $tableRate, $tableImage);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Payment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            padding: 20px;
            text-align: center;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        img {
            width: 100%;
            border-radius: 8px;
        }
        .info {
            font-size: 18px;
            margin: 10px 0;
        }
        .buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        a {
            width: 48%;
            background: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            margin:10px;
        }
        a:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Confirm Your Booking</h2>
        <img src="table/<?php echo htmlspecialchars($tableImage); ?>" alt="Table Image">
        <div class="info"><strong><?php echo htmlspecialchars($tableName); ?></strong> </div>
        <div class="info">Table ID: <?php echo htmlspecialchars($tableId); ?></div>
        <div class="info">Date: <?php echo htmlspecialchars($date); ?></div>
        <div class="info">Time: 07:00 PM - 10:00 PM</div>
        <div class="info">Guests: <?php echo htmlspecialchars($guests); ?></div>
        <div class="info">Table Rate: Rs.<?php echo htmlspecialchars(number_format($tableRate, 2)); ?></div>
        <div class="buttons">
        <a href="cardPayment.php" id="payCard">Pay with Card</a>
        <a href="upi.php" id="payUPI">UPI</a>
        </div>
    </div>
</body>
</html>
