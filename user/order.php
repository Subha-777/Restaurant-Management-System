<?php
session_start();
include 'Configure.php';

if (!isset($_SESSION['user_id']) && !isset($_SESSION['guest_id'])) {
    $_SESSION['guest_id'] = uniqid("guest_");
}

$search = "";
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $foodQuery = "SELECT * FROM food_items WHERE food_name LIKE '%$search%'";
} else {
    $foodQuery = "SELECT * FROM food_items";
}
$foodResult = mysqli_query($conn, $foodQuery);

$popupMessage = ""; // Store popup message
$itemPlaced = false; // Check if item is placed

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_order'])) {
    $food_item_id = $_POST['food_item_id'];
    $quantity = (int) $_POST['quantity'];

    $query = "SELECT food_name, price, quantity FROM food_items WHERE id = $food_item_id";
    $result = mysqli_query($conn, $query);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $food_name = $row['food_name'];
        $price = $row['price'];
        $available_quantity = $row['quantity'];

        if ($quantity > 0) {
            if ($quantity <= $available_quantity) {
                $total_price = $price * $quantity;

                $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : "NULL";
                $guest_id = isset($_SESSION['guest_id']) ? $_SESSION['guest_id'] : "NULL";

                $insertQuery = "INSERT INTO cart (user_id, guest_id, food_item_id, quantity, total_price) 
                                VALUES ($user_id, '$guest_id', '$food_item_id', '$quantity', '$total_price')";

                if (mysqli_query($conn, $insertQuery)) {
                    $new_quantity = $available_quantity - $quantity;
                    $updateQuery = "UPDATE food_items SET quantity = $new_quantity WHERE id = $food_item_id";
                    mysqli_query($conn, $updateQuery);

                    $popupMessage = "The $food_name placed successfully!";
                    $itemPlaced = true;
                } else {
                    $popupMessage = "Error placing order: " . mysqli_error($conn);
                }
            } else {
                $popupMessage = "Remaining quantity is $available_quantity";
            }
        } else {
            $popupMessage = "Please enter a valid quantity.";
        }
    } else {
        $popupMessage = "Food item not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Food</title>
    <style>
             body {
            font-family: Arial, sans-serif;
            background: linear-gradient(100deg, gray, black, black, black, gray);
            margin:0px 0px 0px;
        }
        .search-bar {
            margin-left: auto;
        }
        .search-bar input {
            padding: 5px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .search-bar button{
            padding:5px 10px;
            border-radius:5px;
            background:#ff758c;
            color:white;
            border: 2px solid black;
        }
        .search-bar button:hover{
            background:#ff7eb3;
            color:white;
        }
        .header {
    color: black;
    background-color:gold;
    font-size: 17px;
    padding-left:10px;
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
}
        .menu-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }
        .menu-item {
            width: 270px;
            margin: 20px;
            border: 1px solid goldenrod;
            border-radius: 10px;
            text-align: center;
            color: goldenrod;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .menu-item img {
            margin-top: 10px;
            width: 88%;
            height: 200px;
            border-radius: 10px;
            object-fit: cover;
        }
        .menu-item h3, .menu-item p {
            margin: 10px 0;
        }
        .menu-item input {
            width: 80%;
            margin: 10px 0;
            padding: 5px;
            border-radius: 10px;
            font-size: 15px;
            border: 1px solid #ccc;
        }
        .menu-item button {
            width: 80%;
            margin-bottom: 10px;
            border-radius: 10px;
            background-color: goldenrod;
            color: black;
            font-weight: bold;
            font-size: 15px;
            padding: 10px;
            border: none;
            cursor: pointer;
        }
        .menu-item button:disabled {
            background-color: gray;
            cursor: not-allowed;
        }
        .menu-item button:hover:not(:disabled) {
            background-color: rgba(0, 0, 0, 0.1);
            color: goldenrod;
        }
        /* Popup */
        .overlay, .popup {
            display: none;
            position: fixed;
            z-index: 1000;
        }
        .overlay {
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }
        .popup {
            width: 350px;
            background: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            position: fixed;
        }
        .popup p { font-size: 18px; margin-bottom: 20px; }
        .popup button {
            margin: 10px;
            padding: 10px 20px;
            border: none;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }
        .popup .ok-btn { background: green; color: white; }
    </style>
</head>
<body>
<header>
        <div class="container">
            <h1>Delicious Bites</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="order.php">Orders</a></li>
                    <li><a href="mycart.php">My Cart</a></li>
                    <li><a href="reservation.php">Reservation</a></li>
                    <li><a href="login.html">Login</a></li>
                    <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="myprofile.php">MyProfile</a></li>
                    <?php endif ?>
                </ul>
            </nav>
            <div class="search-bar">
                <form method="GET" action="">
                    <input type="text" name="search" placeholder="Search food..." value="<?php echo $search; ?>">
                    <button type="submit">Search</button>
                </form>
            </div>
        </div>
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="header">
            <p>Welcome dear <?= $_SESSION['username'] ?>   <a style="color:red; text-decoration: none;" href="logout.php">Logout</a></p>
            </div>
        <?php endif; ?>
    </header>
    <h2 style="text-align: center; font-size: 35px; color: goldenrod;">Order Food</h2>
    <div class="menu-container">
        <?php while ($row = mysqli_fetch_assoc($foodResult)) { ?>
            <div class="menu-item">
                <img src="<?php echo $row['food_image']; ?>" alt="<?php echo $row['food_name']; ?>">
                <h3><?php echo $row['food_name']; ?></h3>
                <p><b>Price: $</b><?= number_format($row['price'],2); ?></p>
                <p><b>Available Quantity: </b><?= $row['quantity']; ?></p>
                <form method="POST">
                    <input type="hidden" name="food_item_id" value="<?= $row['id']; ?>">
                    <input type="number" name="quantity" min="1" required <?= $row['quantity'] == 0 ? 'disabled' : ''; ?>>
                    <input type="hidden" name="confirm_order" value="1">
                    <button type="submit" <?= $row['quantity'] == 0 ? 'disabled' : ''; ?>>Place Item</button>
                </form>
            </div>
        <?php } ?>
    </div>

    <!-- Popup Message -->
    <?php if (!empty($popupMessage)) : ?>
        <div class="overlay" id="popupOverlay"></div>
        <div class="popup" id="popupMessage">
            <p><?php echo $popupMessage; ?></p>
            <button class="ok-btn" onclick="closePopup()">OK</button>
        </div>

        <script>
            document.getElementById("popupOverlay").style.display = "block";
            document.getElementById("popupMessage").style.display = "block";

            function closePopup() {
                document.getElementById("popupOverlay").style.display = "none";
                document.getElementById("popupMessage").style.display = "none";
                window.location.href = 'order.php';
            }
        </script>
    <?php endif; ?>
</body>
</html>
