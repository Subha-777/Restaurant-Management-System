<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Delicious Bites</h1>
            <nav>
                <ul>
                    <li><a href="#home">Home</a></li>
                    <li><a href="#menu">Menu</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#testimonials">Testimonials</a></li>
                    <li><a href="order.php">Orders</a></li>
                    <li><a href="mycart.php">My Cart</a></li>
                    <li><a href="reservation.php">Reservation</a></li>
                    <li><a href="#contact">Contact</a></li>
                     <!-- Feedback Link with Login Check -->
                     <li>
                        <a href="#" onclick="checkLogin(event)">Feedback</a>
                    </li>
                    <li><a href="login.html">Login</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="myprofile.php">MY Profile</a></li>
                    <?php endif;?>
                </ul>
            </nav>
        </div>
            <?php if (isset($_SESSION['user_id'])): ?>
            <div class="header">
            <p>Welcome dear <?= $_SESSION['username']; ?>    <a style="color:red; text-decoration: none;" href="logout.php">Logout</a></p>
            </div>
            <?php endif; ?>
    </header>

    <!-- Popup Message (Hidden by Default) -->
    <div id="loginPopup" class="popup">
        <div class="popup-content">
            <p>You need to log in first.</p>
            <button onclick="redirectToLogin()">Login Here</button>
            <button onclick="closePopup()">Cancel</button>
        </div>
    </div><script>function checkLogin(event) {
    event.preventDefault(); // Prevent default link action

    // Check if user is logged in
    let isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
    
    if (isLoggedIn) {
        window.location.href = "feedback.php"; // Redirect to Feedback Page
    } else {
        document.getElementById("loginPopup").style.display = "flex"; // Show Popup
    }
}

function closePopup() {
    document.getElementById("loginPopup").style.display = "none"; // Hide Popup
}

function redirectToLogin() {
    window.location.href = "login.html"; // Redirect to Login Page
}
</script>
    <style>
        /* Popup Styling */
.popup {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
}

.popup-content {
    background: white;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    width: 300px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
}

.popup-content button {
    margin: 10px;
    padding: 10px 20px;
    border: none;
    background: red;
    color: white;
    border-radius: 5px;
    cursor: pointer;
}

.popup-content button:nth-child(2) {
    background: green;
}

    </style>
    <section id="home">
        <div class="hero">
            <h2>Welcome to Delicious Bites</h2>
            <p>Experience the taste of perfection</p>
            <a href="#menu" class="btn">View Menu</a>
        </div>
    </section>
    <section id="menu">
        <div class="container">
            <h2>Our Menu</h2>
            <div class="menu-items">
                <div class="item">
                    <h3>Breakfast</h3>
                    <img src="food/idlywithdosa.webp" alt="Idly with Dosa" width="250" height="250">
                    <p>Morning Healthy dishes.</p>
                </div>
                <div class="item">
                    <h3>Meals</h3>
                    <img src="food/burger.jpeg" alt="Burger" width="250" height="250">
                    <p>Marvelous Meals.</p>
                </div>
                <div class="item">
                    <h3>Dinner</h3>
                    <img src="food/chicken salad.jpg" alt="Chicken salad" width="250" height="250">
                    <p>Delicious Dinner.</p>
                </div>
                <div class="item">
                    <h3>Dessert</h3>
                    <img src="food/chocolateicecream.jpg" alt="Brownie Pudding" width="250" height="250">
                    <p>Yummy Dessert.</p>
                </div>
                <!-- Add more dishes as needed -->
            </div>
        </div>
    </section>
    <section id="about">
        <div class="container">
            <h2>About Us</h2>
            <p>Welcome to Delicious Bites, where we serve exquisite cuisine with a touch of elegance. Our chefs use the finest ingredients to create memorable dining experiences.</p>
        </div>
    </section>
    <section id="testimonials">
        <div class="container">
            <h2>Testimonials</h2>
            <div class="menu-items">
                <div class="item">
                    <p>"This place has the best pasta I've ever had. Will come back for sure!"</p>
                    <p>- Food Lover</p>
                </div>
                <div class="item">
                    <p>"The ambiance and service were outstanding. Five stars!"</p>
                    <p>- Culinary Critic</p>
                </div>
            </div>
        </div>
    </section>
    <section id="reservation">
        <div class="container">
            <h2>Reservation</h2>
            <p>To make a reservation, please call us at +1 234 567 890 or email us at reservations@deliciousbites.com. We look forward to serving you!</p>
        </div>
    </section>
    <section id="contact">
        <div class="container">
            <h2>Contact Us</h2>
            <p>Address: 123 Culinary Street, Food City</p>
            <p>Phone: +1 234 567 890</p>
            <p>Email: info@deliciousbites.com</p>
        </div>
    </section>
    <footer>
        <div class="container">
            <p>&copy; 2024 Delicious Bites. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
