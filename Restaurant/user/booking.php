<?php  
include 'Configure.php'; // Include your database connection file
session_start(); // Start session to check login status

// Get the table ID from URL parameter
$tableId = isset($_GET['table_id']) ? $_GET['table_id'] : null;
$tableImage = 'default.jpg'; // Default image if not found
$restaurantName = 'Restaurant Name';
$reservationTime = '07:00 PM - 10:00 PM'; // Hardcoded time
$date = date('Y-m-d'); // Get today's date
$guests = '2'; // Default guest count
$tablePrice = 0; // Default price
// Fetch table details from the database if table ID is available
if ($tableId) {
    $query = "SELECT table_name, table_image, table_type, price FROM book_tables WHERE id = $tableId";
    $result = mysqli_query($conn, $query);
        if ($row = mysqli_fetch_assoc($result)) {
        $tableImage = $row['table_image'];
        $table_name= $row['table_name'];
        $tableType = $row['table_type'];
        $tablePrice = $row['price'];
        // Set guest count based on table type
        if ($tableType == 'Couple Table') {
            $guests = '2';
        } elseif ($tableType == 'Family Table') {
            $guests = '5';
        } elseif ($tableType == 'Outdoor Table') {
            $guests = '4';
        }
}}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $table_name = $_POST['table_name'] ?? ''; // Get table name
    $_SESSION['booking_details'] = [
        'table_id' => $_POST['table_id'] ?? '',
        'table_name' => $table_name,  
        'date' => $_POST['date'] ?? '',
        'guests' => $_POST['guests'] ?? '',
        'name' => $_POST['name'] ?? '',
        'email' => $_POST['email'] ?? '',
        'phone' => $_POST['phone'] ?? '',
        'special_requests' => $_POST['special_request'] ?? ''
    ];
    header("Location: booking_payment.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        .container {
            display: flex;
            max-width: 1200px;
            margin: auto;
            padding: 20px;
            gap: 5%;
        }
        .left-side {
            width: 60%;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .right-side {
            width: 30%;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .table-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }
        .info {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 18px;
            margin-top: 10px;
        }
        .info img {
            width: 20px;
        }
        hr {
            margin: 20px 0;
            border: 1px solid #ddd;
        }
        .form-group {
            margin-bottom: 15px;
            padding-right:20px;
        }
        label {
            font-weight: bold;
        }
        input, textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            width: 100%;
            background: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #0056b3;
        }
        .popup {
            display: none;
            position: fixed;
            width: 350px;
            background: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }        .popup p {
            font-size: 18px;
           margin-bottom: 20px;
        }    .popup-buttons {
    display: flex;
    justify-content: center;
    gap: 10px;
}
.popup button {
    padding: 8px 15px;
    font-size: 14px;
}     .popup .cancel-btn {
            background: red;
            color: white;
        }        .popup .ok-btn {
            background: green;
            color: white;
        }      
    </style>
</head>
<body>
<div class="container">
    <div class="left-side">
        <img src="table/<?php echo htmlspecialchars($tableImage); ?>" alt="Table Image" class="table-image">
        <h2><?php echo htmlspecialchars($restaurantName); ?></h2>
        <h3>Table Type: <?php echo htmlspecialchars($tableType); ?></h3>
        <h3>Table Name: <?php echo htmlspecialchars($table_name);?></h3>
        <div class="info">
            <img src="table/calender.png" alt="calender"> <?php echo $date; ?>
            <img src="table/clock.jpg" alt="clock"> <?php echo $reservationTime; ?>
            <img src="table/people.jpg" alt="people"> <?php echo htmlspecialchars($guests); ?> Guests
        </div>
        <hr>
        <center><h3>Fill the details</h3></center>
        <form id="reservationForm" action="booking.php" method="POST" onsubmit="return checkLogin()">
    <input type="hidden" name="price" value="<?php echo htmlspecialchars($tablePrice); ?>">
    <input type="hidden" name="table_name" value="<?php echo htmlspecialchars($table_name); ?>"> 
    <input type="hidden" name="table_id" value="<?php echo htmlspecialchars($tableId); ?>">
    <input type="hidden" name="guests" value="<?php echo htmlspecialchars($guests); ?>">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email"  required>
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone"  required>
            </div>
            <div class="form-group">
                <label>Special Request</label>
                <textarea name="special_request" rows="3"></textarea>
            </div>
            <button type="submit">Reserve now!</button>
        </form>
    </div>
<!-- Right Side -->
    <div class="right-side">
        <h3>What to know before you go</h3>
        <p><b>Important dining information</b></p>
        <p>We have a 15-minute grace period. Please call us if you are running later than 15 minutes after your reservation time.</p>
        <p>We may contact you about this reservation, so please ensure your email and phone number are up to date.</p>
        <p><b>A note from the restaurant</b></p>
        <p>Thank you for making a reservation with us. If you have any questions, please call (212) 935-3785.</p>
        <p>Please let us know if you or any of your guests have allergies or dietary restrictions prior to your reservation.</p>
    </div>
</div>
</body></html>
