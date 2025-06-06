<?php
session_start();
include 'Configure.php';

// Check if user is logged in
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$guest_id = isset($_SESSION['guest_id']) ? $_SESSION['guest_id'] : null;

//  Transfer guest cart items to user_id after login
if ($user_id && $guest_id) {
    $update_cart = "UPDATE cart SET user_id = '$user_id', guest_id = NULL WHERE guest_id = '$guest_id'";
    mysqli_query($conn, $update_cart);
}

//  Now fetch cart items (only based on user_id after transfer)
$cartQuery = "SELECT c.id, c.food_item_id, c.quantity, f.price, c.added_at, f.food_name, f.food_image
              FROM cart c 
              JOIN food_items f ON c.food_item_id = f.id 
              WHERE c.user_id = '$user_id' OR (c.guest_id IS NOT NULL AND c.guest_id = '$guest_id')";

$cartResult = mysqli_query($conn, $cartQuery);

// Check if query execution is successful
if (!$cartResult) {
    die("Error in SQL Query: " . mysqli_error($conn));
}

// Calculate total price
$totalPrice = 0;
?>

<?php
include 'Configure.php';
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
    <title>My Cart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(100deg, gray, black, black, black, gray);
            margin:0px 0px 0px;
        }        .header {
    color: black;
    background-color:gold;
    font-size: 17px;
    padding-left:10px;
}header {
    background-color: gold; /* Beige color */
    color: black;
    padding: 15px 0;
    position: sticky;
    background-attachment: fixed;
    top: 0;
    width: 100%;
}header .container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 20px;
}header h1 {
    margin: 0;
    color: black;
}header nav ul {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
}header nav ul li {
    margin-left: 20px;
}header nav ul li a {
    color: black;
    text-decoration: none;
    font-weight: bold;
}header nav ul li a:hover{
    color: whitesmoke;
}.cart-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }        .cart-item {
            width: 270px;
            border: 1px solid goldenrod;
            border-radius: 10px;
            text-align: center;
            padding: 10px;
            color: goldenrod;
            margin-bottom: 20px;
        }        .cart-item img {
            width: 100%;
            height: 200px;
            border-radius: 10px;
        }        .cart-item button {
            width: 80%;
            background-color: goldenrod;
            color: black;
            font-weight: bold;
            padding: 10px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
        }        .cart-item button:hover {
            background-color: rgba(0, 0, 0, 0.1);
            color: goldenrod;
        }        .checkout-btn {
            margin-top: 20px;
            background-color: goldenrod;
            color: black;
            padding: 15px;
            font-size: 18px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            display: block;  /* Makes it a block-level element */
            margin: 0 auto;  /* Centers it horizontally */
            text-align: center; /* Ensures the text inside is centered */
            margin-bottom:20px;
        }  .total {
            text-align:center;
            font-size: 20px;
            font-weight: bold;
            margin-top: 20px;
            color:goldenrod;
        }.popup {
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
        }        .popup button {
            margin: 10px;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
        }        .popup .cancel-btn {
            background: red;
            color: white;
        }        .popup .ok-btn {
            background: green;
            color: white;
        }        </style>  </head><body>
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
        </div>
        <?php if ($is_logged_in): ?>
            <div class="header">
                <p>Welcome dear <?= htmlspecialchars($username) ?> | <a style="color:red; text-decoration: none;" href="logout.php">Logout</a></p>
            </div>
        <?php endif; ?>
    </header>
<script>
    function confirmCancel(cartId) {
    document.getElementById("popup").style.display = "block";
    document.getElementById("confirmDelete").onclick = function () {
        removeCartItem(cartId);
    };
}function removeCartItem(cartId) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "remove_cart_item.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            if (xhr.responseText.trim() === "success") {
                document.getElementById("cart-item-" + cartId).remove();
                document.getElementById("popup").style.display = "none";
                // Refresh the page after 1 second
                setTimeout(function () {
                    location.reload();
                }, 0);
            } else {
                alert("Failed to remove item.");
            }
        }
    };
    xhr.send("cart_id=" + cartId);
}    </script>
    <h2 style="color: goldenrod; font-size: 25px; text-align:center;">My Cart</h2>
    <div class="cart-container">
        <?php while ($row = mysqli_fetch_assoc($cartResult)):$totalPrice += $row['price'] * $row['quantity']; ?>
            <div class="cart-item" id="cart-item-<?php echo $row['id']; ?>">
                <img src="<?= $row['food_image']; ?>" alt="<?= $row['food_name']; ?>">
                <h3><?= $row['food_name']; ?></h3>
                <p><b>Quantity:</b> <?= $row['quantity']; ?></p>
                <p><b>Total Price: ₹</b><?= number_format($row['price'] * $row['quantity'], 2); ?></p>
                <p><b>Added at: </b><?= $row['added_at'];?></p>
                <button type="button" onclick="confirmCancel(<?= $row['id']; ?>)">Cancel</button>
            </div>
        <?php endwhile; ?>
    </div>
    <h3 class="total">Total Price: ₹<?= number_format($totalPrice, 2); ?></h3>
    <button class="checkout-btn" onclick="checkout()" style="text-align:center;">Checkout</button>
    <div id="popup" class="popup">
        <p>Are you sure you want to remove this item?</p>
        <button onclick="document.getElementById('popup').style.display='none'" class="cancel-btn">Cancel</button>
        <button id="confirmDelete" class="ok-btn">OK</button>
    </div>
    </script>    <script>
function checkout() {
    var isLoggedIn = <?= isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
        if (!isLoggedIn) {
        // Show login popup if not logged in
        document.getElementById("loginPopup").style.display = "block";
    } else {
        // Check if user has items in the cart
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "check_cart.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                if (xhr.responseText.trim() === "empty") {
                    // Show popup if cart is empty
                    document.getElementById("emptyCartPopup").style.display = "block";
                } else {
                    // Redirect to payment page if cart has items
                    window.location.href = "payment.php";
                }
            }
        };
        xhr.send("user_id=<?= $_SESSION['user_id'] ?? ''; ?>");
    }
}</script>
<!-- Login Required Popup -->
<div id="loginPopup" class="popup">
    <p>You need to log in first.</p>
    <button onclick="document.getElementById('loginPopup').style.display='none'" class="cancel-btn">Cancel</button>
    <button onclick="window.location.href='o_login.html'" class="ok-btn">login here!</button>
</div>
<!-- Empty Cart Popup -->
<div id="emptyCartPopup" class="popup">
    <p>You haven't selected any food items.</p>
    <button onclick="document.getElementById('emptyCartPopup').style.display='none'" class="ok-btn">OK</button>
</div>
</body>
</html>
