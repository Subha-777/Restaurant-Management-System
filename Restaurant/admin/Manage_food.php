<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
include 'Configure.php';

// Handle delete request using prepared statements
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM food_items WHERE id = ?");
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        $message = "Food item deleted successfully!";
    } else {
        $message = "Error deleting food item: " . $conn->error;
    }
}

$food_items_result = $conn->query("SELECT * FROM food_items");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manage Food Items</title>
  <style>
    /* General Styles */
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(to right, #f8f8f8, #e0f7fa);
      margin: 0;
      padding: 0;
    }
    h1 {
      margin-top: 30px;
      font-size: 36px;
      color: #004d40;
      text-align: center;
    }
    .container {
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 20px;
    }
    /* Navigation Bar */
    .navbar {
      width: 100%;
      background-color: rgba(0,0,0,0.4);
      padding: 10px;
      display: flex;
      justify-content: center;
      border-radius: 10px;
      margin-bottom: 20px;
    }
    .nav-links {
      list-style: none;
      display: flex;
      gap: 15px;
      flex-wrap: wrap;
      justify-content: center;
    }
    .nav-links a {
      text-decoration: none;
      color: #fff;
      padding: 5px 10px;
      border-radius: 5px;
      transition: background 0.3s ease;
    }
    .nav-links a:hover {
      background: rgba(0,0,0,0.8);
    }
    /* Cards Container */
    .cards-container {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 20px;
      width: 100%;
      max-width: 1200px;
    }
    /* Food Item Card */
    .card {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      width: 250px;
      padding: 15px;
      text-align: center;
      transition: transform 0.3s;
    }
    .card:hover {
      transform: scale(1.03);
    }
    .card img {
      width: 100%;
      height: 160px;
      object-fit: cover;
      border-radius: 8px;
      margin-bottom: 10px;
    }
    .card h3 {
      margin: 10px 0;
      color: #333;
    }
    .card p {
      color: #555;
      margin: 5px 0;
    }
    /* Action Buttons */
    .actions {
      margin-top: 10px;
    }
    .btn-edit,
    .btn-delete {
      padding: 8px 12px;
      border: none;
      border-radius: 6px;
      font-weight: bold;
      color: #fff;
      cursor: pointer;
      margin: 5px;
      transition: background 0.3s ease;
      text-decoration: none;
    }
    .btn-edit {
      background: #4CAF50;
    }
    .btn-edit:hover {
      background: #45a049;
    }
    .btn-delete {
      background: #f44336;
    }
    .btn-delete:hover {
      background: #d32f2f;
    }
    /* Add Food Button */
    .btn-add {
      display: inline-block;
      padding: 12px 20px;
      background-color: #007BFF;
      color: #fff;
      border-radius: 8px;
      text-decoration: none;
      margin-top: 20px;
      transition: background-color 0.3s;
    }
    .btn-add:hover {
      background-color: #0056b3;
    }
    @media (max-width: 768px) {
      .card {
        width: 90%;
      }
      .nav-links {
        flex-direction: column;
        align-items: center;
        gap: 10px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Manage Food Items</h1>
    <!-- Navigation Bar -->
    <nav class="navbar">
      <div class="nav-links">
        <a href="home.php">Dashboard</a>
        <a href="Manage_food.php">Manage Food Items</a>
        <a href="Manage_Orders.php">Manage Orders</a>
        <a href="Manage_Users.php">Manage Users</a>
        <a href="manage_tables.php">Table Management</a>
        <a href="table_management.php">Reservation</a>
        <a href="admin_feedback.php">Feedback</a>
        <a href="admin_profile.php">Admin Profile</a>
        <a href="profit.php">Profit</a>
        <a href="alogin.php">Logout</a>
      </div>
    </nav>
    
    <!-- Message Display -->
    <?php if (isset($message)) { echo "<p>$message</p>"; } ?>
    
    <!-- Food Items Cards -->
    <div class="cards-container">
      <?php while ($row = $food_items_result->fetch_assoc()) { ?>
        <div class="card">
          <img src="<?= $row['food_image']; ?>" alt="<?= $row['food_name']; ?>">
          <h3><?= $row['food_name']; ?></h3>
          <p><?= $row['description']; ?></p>
          <p><strong>Price:</strong> ₹<?= $row['price']; ?></p>
          <p><strong>Buying Price:</strong> ₹<?= $row['buying_price']; ?></p>
          <p><strong>Quantity:</strong> <?= $row['quantity']; ?></p>
          <div class="actions">
            <a href="edit_food.php?id=<?= $row['id']; ?>" class="btn-edit">Edit</a>
            <a href="Manage_food.php?delete=<?= $row['id']; ?>" class="btn-delete">Delete</a>
          </div>
        </div>
      <?php } ?>
    </div>
    
    <!-- Add Food Button -->
    <a href="add_food.php" class="btn-add">Add Food</a>
  </div>
</body>
</html>
