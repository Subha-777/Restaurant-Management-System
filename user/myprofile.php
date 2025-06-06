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
$userQuery = mysqli_query($conn, "SELECT username, email, phone,address FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($userQuery);

// Fetch ordered items
$orderedQuery = mysqli_query($conn, "SELECT o.id, f.food_name, o.quantity, f.price, f.food_image, o.payment_method, o.order_date,o.status 
                                     FROM orders o 
                                     JOIN food_items f ON o.food_item_id = f.id
                                     WHERE o.user_id = '$user_id' AND (o.status = 'ordered'  OR o.status='Pending' OR o.status=' In Progress')");
$orderedItems = mysqli_fetch_all($orderedQuery, MYSQLI_ASSOC);

// Fetch canceled items
$canceledQuery = mysqli_query($conn, "SELECT f.food_name, o.quantity, f.price, f.food_image ,o.status
                                      FROM orders o 
                                      JOIN food_items f ON o.food_item_id = f.id
                                      WHERE o.user_id = '$user_id' AND o.status = 'cancelled'");
$canceledItems = mysqli_fetch_all($canceledQuery, MYSQLI_ASSOC);

// Handle cancel request
if (isset($_POST['cancel_order_id'])) {
    $order_id = $_POST['cancel_order_id'];

    // Fetch order details
    $query = "SELECT o.quantity, f.price, o.order_date 
              FROM orders o 
              JOIN food_items f ON o.food_item_id = f.id
              WHERE o.id = '$order_id' AND o.user_id = '$user_id' 
              AND (o.payment_method = 'Card Payment' OR o.payment_method = 'UPI Payment')";
    $result = mysqli_query($conn, $query);
    $order = mysqli_fetch_assoc($result);

    if ($order) {
        $order_time = strtotime($order['order_date']);
        $current_time = time();
        $time_difference = ($current_time - $order_time) / 60; // Difference in minutes

        if ($time_difference <= 15) {
            $total_price = $order['quantity'] * $order['price'];
            $refund_amount = $total_price * 0.75; // Refund 75% amount

            // Update order status to 'canceled'
            mysqli_query($conn, "UPDATE orders SET status = 'cancelled' WHERE id = '$order_id'");

            echo json_encode(['success' => true, 'refund' => $refund_amount]);
        } else {
            echo json_encode(['error' => 'You can only cancel within 15 minutes of placing the order.']);
        }
    } else {
        echo json_encode(['error' => 'Invalid order or already canceled.']);
    }
    exit();
}
// Fetch ordered items
$reserveQuery = mysqli_query($conn, "SELECT r.id, b.table_name, b.table_type, b.price, b.table_image, r.payment_method, r.date, r.status 
                                     FROM reservation r 
                                     JOIN book_tables b ON r.table_name = b.table_name        
                                     WHERE r.user_id = '$user_id' AND r.status = 'reserved'");

$orderedtable = mysqli_fetch_all($reserveQuery, MYSQLI_ASSOC);

// Fetch canceled items
$cancelreserveQuery = mysqli_query($conn, "SELECT b.table_name,b.table_type, b.price, b.table_image ,r.status
                                      FROM reservation r 
                                      JOIN book_tables b ON r.table_name = b.table_name      
                                      WHERE r.user_id = '$user_id' AND r.status = 'cancelled'");
$canceledtable = mysqli_fetch_all($cancelreserveQuery, MYSQLI_ASSOC);
// Handle cancel reservation
if (isset($_POST['cancel_reservation_id'])) {
    $reservation_id = $_POST['cancel_reservation_id'];

    // Fetch reservation details
    $query = "SELECT b.price, r.date, r.status 
              FROM reservation r 
              JOIN book_tables b ON r.table_name = b.table_name
              WHERE r.id = '$reservation_id' AND r.user_id = '$user_id' 
              AND (r.payment_method = 'Card Payment' OR r.payment_method = 'UPI Payment') " ;
    $result = mysqli_query($conn, $query);
    $reservation = mysqli_fetch_assoc($result);
    if ($reservation) {
        if ($reservation['status'] == 'cancelled') {
            echo json_encode(['error' => 'This reservation is already canceled.']);
            exit();
        }

        // Ensure 'date' includes both date and time
        $reservation_time = strtotime($reservation['date']);
        $current_time = time();
        $time_difference = ($current_time - $reservation_time) / 60; // Difference in minutes

        if ($time_difference <= 15) {
            $refund_amount = $reservation['price'] * 0.75; // Refund 75% amount

            // Update reservation status to 'canceled'
            $update_query = "UPDATE reservation SET status = 'cancelled' WHERE id = '$reservation_id'";
            mysqli_query($conn, $update_query);

            echo json_encode(['success' => true, 'refund' => $refund_amount]);
        } else {
            echo json_encode(['error' => 'You can only cancel within 15 minutes of booking.']);
        }
    } else {
        echo json_encode(['error' => 'Invalid reservation.']);
    }
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            /* background: linear-gradient(100deg, #ff758c, #ff7eb3); */
            background: linear-gradient(100deg, gray, black, black, black, gray);
            display: flex;
            flex-direction: column;
            align-items: center;
        }.cancel-btn {
            background-color: red;
            color: white;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            margin-left: 10px;
        }
        .cancel-btn:disabled {
            background-color: gray;
            cursor: not-allowed;
        }
    .header {
    color: black;
    background-color:gold;
    background-size:cover;
    background-repeat:no-repeat;
    font-size: 17px;
    padding-left:10px;
}
.header .anger:hover{
    color:purple;
}
header {
    background: gold;/* Beige color */
    background-size:cover;
    background-repeat:no-repeat;
    color: gold;
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
h2,h3{
    color:goldenrod;
}
        .profile-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 350px;
            margin-top: 50px;
        }
        .profile-card img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 4px solid goldenrod;
            margin-top: -50px;
        }
        .profile-card h2 {
            margin: 10px 0;
            color: #333;
        }
        .profile-card p {
            color: #777;
            margin: 5px 0;
        }
        .food-section {
            width: 80%;
            max-width: 800px;
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .food-item {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .food-item img {
            width:150px;
            height: 150px;
            border-radius: 10px;
        }
.food-item .details {
    flex: 1;
}
.cancel-btn {
    background-color: red;
    color: white;
    font-size:20px;
    padding: 7px 14px;
    border: none;
    border-radius:5px;
    cursor: pointer;
    margin-left: auto; /* Moves button to the right */
    margin-right:20px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.cancel-btn:disabled {
    background-color: gray;
    cursor: not-allowed;
}
        #ordered-box { border-left: 5px solid #28a745;border-right: 5px solid #28a745; }
        #canceled-box { border-left: 5px solid #dc3545; border-right: 5px solid #dc3545; }
    </style>
    <?php
    if (isset($_POST['cancel_cod_order_id'])) {
    $order_id = $_POST['cancel_cod_order_id'];

    // Only update status for Cash on Delivery (No refund)
    mysqli_query($conn, "UPDATE orders SET status = 'cancelled' WHERE id = '$order_id'");

    echo json_encode(['success' => true]);
    exit();
}
?><script>
function cancelCODOrder(orderId, orderTime) {
    let currentTime = Math.floor(Date.now() / 1000);
    let timeDiff = (currentTime - orderTime) / 60; // Convert seconds to minutes
    if (timeDiff > 15) {
        showPopup("Cancellation period has expired.");
        return;
    }
    showPopup("Are you sure you want to cancel this order?", () => {
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "myprofile.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                let response = JSON.parse(xhr.responseText);
                if (response.success) {
                    showPopup("Order canceled successfully.", () => {
                        location.reload();
                    });
                } else {
                    showPopup(response.error);
                }
            }
        };
        xhr.send("cancel_cod_order_id=" + orderId);
    }, true);
}
</script>
    <script>
    function cancelOrder(orderId, orderTime) {
    let currentTime = Math.floor(Date.now() / 1000);
    let timeDiff = (currentTime - orderTime) / 60; // Convert seconds to minutes
    if (timeDiff > 15) {
        showPopup("Cancellation period has expired.");
        return;
    }
    showPopup(
        "If you cancel, you will only receive a 75% refund. Do you want to proceed?",
        () => {
            let xhr = new XMLHttpRequest();
            xhr.open("POST", "myprofile.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    let response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        showPopup(`Order canceled successfully. Refund: ₹${response.refund.toFixed(2)}`, () => {
                            location.reload();
                        });
                    } else {
                        showPopup(response.error);
                    }
                }
            };
            xhr.send("cancel_order_id=" + orderId);
        },
        true // Show cancel button
    );
}
function showPopup(message, confirmCallback = null, showCancel = false) {
    let popup = document.createElement("div");
    popup.className = "popup-container";
    popup.innerHTML = `
        <div class="popup">
            <p>${message}</p>
            <div class="popup-buttons">
                ${showCancel ? '<button class="c" onclick="closePopup()">Cancel</button>' : ''}
                <button class="o" onclick="confirmPopup()">OK</button>
            </div>
        </div>
    `;
    document.body.appendChild(popup);

    window.closePopup = function () {
        document.body.removeChild(popup);
    };

    window.confirmPopup = function () {
        document.body.removeChild(popup);
        if (confirmCallback) confirmCallback();
    };
}
</script>
<style>
    .popup-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        font-size:20px;
        background: rgba(0, 0, 0, 0.6);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .popup {
        background: white;
        padding: 20px;
        border-radius: 10px;
        text-align: center;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
    .popup button {
        margin: 10px;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
    }
    .c{
            background: red;
            color: white;
        }
        .o {
            background: green;
            color: white;
        }
</style>
</head>
<body>
<header>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="order.php">Order</a></li>
            <li><a href="mycart.php">MyCart</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="myprofile.php">MY Profile</a></li>
                    <?php endif;?>
            </ul>
        </nav>
            <?php if (isset($_SESSION['user_id'])): ?>
            <div class="header">
            <p>Welcome dear <?= $_SESSION['username']; ?>    <a style="color:red; text-decoration: none;" href="logout.php">Logout</a></p>
            </div>
            <?php endif; ?>
</header>
    <div class="profile-card">
        <img src="food/profile.jpg" alt="Profile Picture">
        <h2><?= $user['username'] ?></h2>
        <p><?= $user['email'] ?></p>
        <p><?= $user['phone'] ?></p>
    </div>

    <div class="food-section">
        <h3>Ordered Items</h3>
        <div>
        <?php foreach ($orderedItems as $item) { ?>
            <div id="ordered-box"class="food-item">
                <img src="<?= $item['food_image'] ?>" alt="<?= $item['food_name'] ?>">
                <div class="details">
                    <h4><?= $item['food_name'] ?></h4>
                    <p>Quantity: <?= $item['quantity'] ?></p>
                    <p>Total: ₹<?= number_format($item['price'] * $item['quantity'], 2) ?></p>
                    <p>Order At:<?= $item['order_date']?></p>
                    <p><?= $item['payment_method']?></p>
                    <p><?= $item['status']?></p>
                    <?php      
                    if ($item['payment_method'] == 'UPI Payment'|| $item['payment_method']=='Card Payment') {
                        $orderTime = strtotime($item['order_date']);
                        $current_time = time();
                        $time_difference = ($current_time - $orderTime) / 60;
                        ?>
                        </div>
                        <div>                        
                        <button class="cancel-btn" onclick="cancelOrder(<?= $item['id'] ?>, <?= $orderTime ?>)" <?= $time_difference > 15 ? 'disabled' : '' ?>>Cancel</button>
                    <?php } ?>
                </div>
        <!-- Cancel Button for Cash on Delivery (Only Updates Status) -->
        <?php if ($item['payment_method'] == 'Cash on Delivery') { 
            $orderTime = strtotime($item['order_date']);
            $current_time = time();
            $time_difference = ($current_time - $orderTime) / 60;    
        ?>
        <div>
            <button class="cancel-btn" onclick="cancelCODOrder(<?php echo $item['id']; ?>, <?= $orderTime ?>)" <?= $time_difference > 15 ? 'disabled' : '' ?>>
                Cancel
            </button>
        </div>
        <?php } ?>
            </div>
        <?php } ?>
        </div>
    </div>
    <div class="food-section">
        <h3>Canceled Items</h3>
        <?php foreach ($canceledItems as $item) { ?>
            <div id="canceled-box" class="food-item">
                <img src="<?= $item['food_image'] ?>" alt="<?= $item['food_name'] ?>">
                <div class="details">
                    <h4><?= $item['food_name'] ?></h4>
                    <p>Quantity: <?= $item['quantity'] ?></p>
                    <p>Total: ₹<?= number_format($item['price'] * $item['quantity'], 2) ?></p>
                    <p><?= $item['status']?></p>
                </div>
            </div>
        <?php } ?>
    </div>
    <div class="food-section">
        <h3>Reserve Tables</h3>
        <div>
        <?php foreach ($orderedtable as $table): ?>
    <div id="ordered-box"class="food-item">
        <img src="<?= 'table/'. $table['table_image']; ?>" alt="<?= $table['table_name']; ?>">
        <div class="details">
            <h4><?= $table['table_name']; ?> (<?= $table['table_type']; ?>)</h4>
            <p>Price: Rs.<?= $table['price']; ?></p>
            <p>Date: <?= $table['date']; ?></p>
        </div>
        <?php
        // Get the reservation timestamp
        $reservation_time = strtotime($table['date']);
        $current_time = time();
        $time_difference = ($current_time - $reservation_time) / 60; // In minutes
        ?>
        <button class="cancel-btn" 
            onclick="cancelReservation(<?= $table['id']; ?>, <?= $reservation_time; ?>)" 
            <?= ($time_difference > 15) ? 'disabled' : ''; ?>>
            Cancel
        </button>
    </div>
<?php endforeach; ?>

        </div>
    </div>
    <div class="food-section">
        <h3>Canceled Reservation</h3>
        <?php foreach ($canceledtable as $table) { ?>
            <div id="canceled-box" class="food-item">
            <img src="<?= 'table/'. $table['table_image'] ?>" alt="<?= $table['table_type'] ?>">
                <div class="details">
                    <h4><?= $table['table_type'] ?></h4>
                    <p>Rate Rs.<?= number_format($table['price'],2) ?></p>
                    <p><?= $table['status']?></p>
                </div>
            </div>
        <?php } ?>
    </div>
    <script>
       function cancelReservation(reservationId, reservationTime) {
    let currentTime = Math.floor(Date.now() / 1000); // Get current time in seconds
    let timeDiff = (currentTime - reservationTime) / 60; // Convert to minutes

    if (timeDiff > 15) {
        showPopup("Cancellation period has expired.");
        return;
    }

    showPopup(
        "If you cancel, you will only receive a 75% refund. Do you want to proceed?",
        () => {
            let xhr = new XMLHttpRequest();
            xhr.open("POST", "myprofile.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    let response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        showPopup(`Reservation canceled successfully. Refund: ₹${response.refund.toFixed(2)}`, () => {
                            location.reload();
                        });
                    } else {
                        showPopup(response.error);
                    }
                }
            };
            xhr.send("cancel_reservation_id=" + reservationId);
        },
        true // Show cancel button
    );
}
    </script>
</body>
</html>
