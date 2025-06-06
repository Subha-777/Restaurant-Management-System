<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device,width, initial-scale=1.0">
<title>Manage Users</title>
<style>
    /* General Styles */
/* General Styles */
body {
  font-family: 'Arial', sans-serif;
  margin: 0;
  padding: 0;
  background-image: url('back2.webp'); /* Replace with your background image URL */
  background-size: cover;
  background-attachment: fixed;
  color: #333;
}

/* Wrapper */
.page-wrapper {
  background: rgba(0,0,0,0.7); /* Transparent white overlay */
  padding: 20px;
  margin: 20px auto;
  max-width: 1200px;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}

/* Header */
.header {
  margin-bottom: 20px;
  text-align: center;
}

.navbar {
  display: flex;
  justify-content: center;
  align-items: center;
  background: #333;
  padding: 15px 20px;
  border-radius: 10px;
  font-weight:bold;

}

.navbar .logo {
  color: #fff;
  font-size: 1.8rem;
  font-weight: bold;
}

.nav-links {
  list-style: none;
  display: flex;
  gap: 15px;
}

.nav-links a {
  text-decoration: none;
  color: #fff;
  padding: 10px 15px;
  border-radius: 5px;
  transition: background 0.3s ease;
}

.nav-links a:hover {
  background: #575757;
}

/* Page Title */
h1 {
  text-align: center;
  font-size: 2rem;
  color: #444;
  margin-bottom: 30px;
  color: white;
}

/* Table */
.user-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 20px;
}

.user-table th,
.user-table td {
  padding: 15px;
  text-align: center;
  border: 1px solid #ddd;
  color: white;
}

.user-table th {
  background: #333;
  color: #fff;
}

/* .user-table tr:nth-child(even) {
  background: #f9f9f9;
} */

/* .user-table tr:hover {
  background: #f1f1f1;
} */

.user-table img {
  border-radius: 10px;
  width: 50px;
  height: auto;
}

/* Buttons */
.btn-delete {
  padding: 10px 15px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  color: #fff;
  background: #f44336;
  transition: background 0.3s ease;
}

.btn-delete:hover {
  background: #d32f2f;

}

/* Responsive Design */
@media (max-width: 768px) {
  .navbar {
    flex-direction: column;
  }

  .user-table th,
  .user-table td {
    font-size: 0.9rem;
  }

  .btn-delete {
    width: 100%;
    padding: 10px;
  }
}
.navbar {
  display: flex;
  justify-content: center;
  align-items: center;
  /* background: #333; */
  padding: 10px 10px;
  border-radius: 50px;
  position: relative;
  background-color: rgba(0,0,0,0.5);
}

.nav-links {
  list-style: none;
  display: flex;
  gap: 15px;
}

.nav-links a {
  text-decoration: none;
  color: #fff;
  padding: 10px 15px;
  border-radius: 5px;
  transition: background 0.3s ease;
}

.nav-links a:hover {
  background: rgba(0,0,0,0.8);
}

/* Hamburger Menu */
.hamburger-menu {
  display: none;
  font-size: 1.8rem;
  color: #fff;
  cursor: pointer;
}

/* Responsive Design */
@media (max-width: 768px) {
  .nav-links {
    display: none;
    flex-direction: column;
    position: absolute;
    top: 60px;
    right: 20px;
    background: #333;
    padding: 10px 20px;
    border-radius: 10px;
    width: 200px; /* Adjust width for better visibility */
  }

  .nav-links.active {
    display: flex;
  }

  .hamburger-menu {
    display: block;
  }

  .nav-links a {
    text-align: left;
    padding: 10px;
    font-size: 1rem;
  }
}


   </style>
<script>
  function toggleMenu() {
    const navLinks = document.querySelector('.nav-links');
    navLinks.classList.toggle('active');
  }
</script>


</head>
<body>
    <h1>Manage Users</h1>
 <!-- <div class="page-wrapper"> -->
<div class="navbar">
<div class="hamburger-menu" onclick="toggleMenu()">☰</div>
    <ul class="nav-links">
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
</ul>
</div>
<div class="page-wrapper">
<h1>Users Lists</h1>
<table id="usersTable" class="user-table">
<thead>
<tr>
<th>Name</th>
<th>Email</th>
<th>Phone</th>
<th>Address</th>
<th>Action</th>
</tr>
</thead>
<tbody>
</div>
<?php
include 'Configure.php';
// Fetch users from the database
$sql = "SELECT id, username, email, phone, address FROM users";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
// Output data of each row
while($row = $result->fetch_assoc()) {
echo "<tr>
<td>" . $row["username"] . "</td>
<td>" . $row["email"] . "</td>
<td>" . $row["phone"] . "</td>
<td>".$row["address"]."</td>
<td><form method='post'>
<input type='hidden' name='delete_user_id' value='" . $row["id"] . "'>
<button type='submit' onclick='return confirm(\"Are you sure you want to delete this user?\")'>Delete</button>
</form></td>
</tr>";
}
} else {
echo "<tr><td colspan='4'>No users
found</td></tr>";
}
$conn->close();
?>
</tbody>
</table>
<?php
// Handle user deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" &&
isset($_POST["delete_user_id"])) {
$deleteUserId = $_POST["delete_user_id"];
// Database connection
$conn = new mysqli($servername, $username,
$password, $dbname);
// Check connection
if ($conn->connect_error) {
die("Connection failed: " . $conn->connect_error);
}
// Delete user from the database
$sql = "DELETE FROM users WHERE id='$deleteUserId'";
if ($conn->query($sql) === TRUE) {
echo "<div id='successMessage'>User deleted successfully!</div>";
} else {
echo "Error deleting user: " . $conn->error;
}
$conn->close();
}
?>
</body>
</html>