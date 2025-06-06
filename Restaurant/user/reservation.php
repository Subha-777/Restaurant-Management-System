<?php
include 'Configure.php';
// Check if user is logged in
session_start();

// Fetch all tables
$query = "SELECT * FROM book_tables";
$result = mysqli_query($conn, $query);
$tables = [];
while ($row = mysqli_fetch_assoc($result)) {
    $tables[] = $row;
}
$is_logged_in = isset($_SESSION['user_id']);
$username = '';
if ($is_logged_in) {
    $user_query = $conn->query("SELECT username FROM users WHERE id = " . $_SESSION['user_id']);
    $user = $user_query->fetch_assoc();
    $username = $user['username'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table Booking</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(100deg, gray, #121212, #121212, #121212, gray);
            margin: 0;
            padding: 0;
            color: white;
        }
        header {
            background: #d9534f;
            padding: 20px 0;
            text-align: center;
            background-attachment:fixed;
        }
        .container {
            width: 90%;
            margin: 20px 65px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            justify-content: center;
        }
        .table-card {
            background: #1e1e1e;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.1);
            padding: 20px;
            text-align: center;
            border: 2px solid #d9534f;
        }
        .table-card img {
            width: 100%;
            height: 180px;
            border-radius: 10px;
        }
        .table-card h2 {
            color: #d9534f;
            margin: 10px 0;
        }
        .time-slots button {
            background: #d9534f;
            color: white;
            border: none;
            padding: 10px 15px;
            margin-top: 10px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            width: 100%;
        }
        .time-slots button:hover {
            background: #c9302c;
        }
        .filter-container {
            text-align: center;
            margin: 20px 0;
        }
        .filter-container select {
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: 2px solid #d9534f;
            background: #1e1e1e;
            color: white;
            cursor: pointer;
        }
header {
            background: #d9534f;
            padding: 30px 0;
            text-align: center;
        }
        nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            align-items: center;
        }
        nav ul .brand {
            font-size: 24px;
            font-weight: bold;
            color: white;
            margin-left:10px;
            margin-right: auto;
        }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 13px;
            margin-right: 10px;
        }
        .nav-links li {
            list-style: none;
        }
        .nav-links a {
            color: white;
            text-decoration: none;
            font-size: 18px;
        }
        .login-btn {
            background-color: wheat;
            color: #d9534f;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        .login-btn:hover {
            background-color: white;
        }
.hero {
            position: relative;
            background: url('table/home.webp');
            height: 400px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
        }
        .hero h1 {
            font-size: 40px;
            margin-bottom: 20px;
        }
        .hero h2{
            font-size:30px;
            margin-bottom:0px;
        }
        .view-tables {
            background: #d9534f;
            color: white;
            font-weight:bolder;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        .view-tables:hover{
            background:white;
            color:#d9534f;
        }
    </style>
    <script>
        function filterTables() {
            var selectedType = document.getElementById("tableFilter").value;
            var tableCards = document.getElementsByClassName("table-card");

            for (var i = 0; i < tableCards.length; i++) {
                var tableType = tableCards[i].getAttribute("data-type");

                if (selectedType === "all" || tableType === selectedType) {
                    tableCards[i].style.display = "block";
                } else {
                    tableCards[i].style.display = "none";
                }
            }
        }
    </script>
</head>
<body>
    <header>
        <nav>
            <ul>
                <li class="brand">Delicious Bites</li>
                <div class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="order.php">Order</a></li>
                    <li><a href="mycart.php">MyCart</a></li>
                    <li class="right"><a href="r_login.html" class="login-btn">Sign in</a></li>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li><a href="myprofile.php">MyProfile</a></li>
                    <?php endif ?>
                </div>
            </ul>
        </nav>
        <?php if ($is_logged_in): ?>
            <div class="header">
                <p>Welcome dear <?= htmlspecialchars($username) ?> | <a style="color:blue; text-decoration: none;" href="r_logout.php">Logout</a></p>
            </div>
        <?php endif; ?>
    </header>
    <div class="hero">
        <h2>New York City Restaurant</h2>
        <h1>Make a Reservation</h1>
        <a href="#container"> <button class="view-tables">View Tables</button></a>
    </div>
    <div class="filter-container">
        <label for="tableFilter"><b>Choose Table Type:</b></label>
        <select id="tableFilter" onchange="filterTables()">
            <option value="all">All Tables</option>
            <option value="Family Table">Family Table</option>
            <option value="Couple Table">Couple Table</option>
            <option value="Outdoor Table">Outdoor Table</option>
        </select>
    </div>
    <div id="container" class="container">
<?php
foreach ($tables as $row) {
    $booked_count = rand(5, 60);
    $button_text = ($row['status'] == 'available') ? "Book Now!" : "Not Available";
    $button_disabled = ($row['status'] == 'available') ? "" : "disabled";
    $button_class = ($row['status'] == 'available') ? "book-now" : "not-available";

    echo "<div class='table-card' data-type='{$row['table_type']}'>
        <img src='table/{$row['table_image']}' alt='{$row['table_type']}'>
        <h2>{$row['table_type']}</h2>
        <p><strong>Booked $booked_count times today</strong></p>
        <p style='color:pink'><b>Available at: 6.00pm to 10.00pm</b></p>
        <p><strong>Rs. {$row['price']}</strong></p>
        <div class='time-slots'>";

    if ($row['status'] == 'available') {
        if ($is_logged_in) {
            // If logged in, go to booking.php with table_id
            echo "<form action='booking.php' method='GET'>
                    <input type='hidden' name='table_id' value='{$row['id']}'>
                    <button type='submit' class='$button_class'>$button_text</button>
                  </form>";
        } else {
            // If not logged in, show login popup
            echo "<button onclick='checkLogin()' class='$button_class'>$button_text</button>";
        }
    } else {
        // If not available, disable the button
        echo "<button class='$button_class' disabled>$button_text</button>";
    }

    echo "</div></div>";
}
?>

<div id="loginPopup" class="popup">
    <p>You need to login first.</p>
    <div class="popup-buttons">
        <button class="cancel-btn" onclick="closePopup()">Cancel</button>
        <button class="ok-btn" onclick="window.location.href='r_login.html'">Login here!</button>
    </div>
</div>
<script>
function checkLogin() {
    var isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
    if (!isLoggedIn) {
        document.getElementById('loginPopup').style.display = 'block';
        return false;
    }
    return true;
}
function closePopup() {
    document.getElementById('loginPopup').style.display = 'none';
}
</script>
<style>
    .popup {
            display: none;
            position: fixed;
            width: 350px;
            color:black;
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
    border-radius:5px;
}     .popup .cancel-btn {
            background: red;
            color: white;
        }        .popup .ok-btn {
            background: green;
            color: white;
        }      
</style>

</body>
</html>
