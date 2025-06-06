<!DOCTYPE html>
<head>
<meta charset="UTF-8">
<meta name="viewport"
content="width=device-width, initialscale=1.0">
<title>Feedback Management</title>
<style>
/* General body styling */
body {
  font-family: 'Arial', sans-serif;
  margin: 0;
  padding: 0;
  background: url('back7.avif')no-repeat center center fixed; /* Replace with your background image URL */
  background-size: cover;
  background-attachment: fixed;
  color: #333;
}

/* Page Wrapper */
.page-wrapper {
  background: rgba(0,0,0,0.7); /* Transparent white overlay */
  padding: 20px;
  margin: 20px auto;
  max-width: 1200px;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}

/* Header */
h1 {
  text-align: center;
  font-size: 2rem;
  color: #333;
  margin-bottom: 20px;
}

/* Navigation Menu */
/* .navbar {
  display: flex;
  justify-content: space-around;
  align-items: center;
  background: #333;
  padding: 10px 20px;
  /* border-radius: 60px; */
  /* margin-bottom: 20px; */
  .navbar {
     background-color: rgba(0, 0, 0, 0.8);
    padding: 20px;
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;
    border-radius: 60px;
}

.navbar a {
  text-decoration: none;
  color: #fff;
  padding: 8px 15px;
  border-radius: 5px;
  /* transition: background 0.3s ease; */
  font-size: 1rem;
}

.navbar a:hover {
  background: gray;
}


/* Table Styling */
.table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

.table th,
.table td {
  padding: 15px;
  text-align: center;
  border: 1px solid #ddd;
  font-size: 16px;
}

.table th {
  background: rgba(0,0,0,0.6);
  color: white;
  font-weight: bold;
}
.table td{
  color: white;
}

 
.table a {
  color: red;
  text-decoration: none;
  font-weight: bold;
}

.table a:hover {
  text-decoration: none;
}

/* Responsive Design */
@media (max-width: 768px) {
  .table th,
  .table td {
    font-size: 14px;
    padding: 10px;
  }
  .navbar {
              flex-direction: column;
              align-items: flex-start;
              gap: 10px;
            }

            .navbar a {
                margin: 0.5rem 0;
               
            }
}

@media (max-width: 480px) {
  .table th,
  .table td {
    font-size: 12px;
    padding: 8px;
  }

  h1 {
    font-size: 1.5rem;
   
  }
}
h1{
  color: white;
}
h2{
color: white;
text-align: center;
}

</style>
</head>
<body>
<h1>Manage Feedbacks</h1>
<div class="navbar">
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
<div class="page-wrapper">
<h2>User Feedback</h2>
<table class="table">
    <thead>
<tr>
<th>Name</th>
<th>Feedback</th>
<th>Rating</th>
<th>Date</th>
<th>Action</th>
</tr>
</thead>

<?php
include 'Configure.php';
// Delete feedback entry
if (isset($_GET['delete_id'])) {
$delete_id = $_GET['delete_id'];
$sql_delete = "DELETE FROM feedback WHERE id = $delete_id";
$conn->query($sql_delete);
}
// Retrieve feedback
$sql = "SELECT f.id, f.username,f.feedback, f.rating, f.created_at FROM feedback f  ORDER BY f.created_at DESC";
$result = $conn->query($sql);
if ($result === FALSE) {
    echo "Error: " . $conn->error;
} elseif ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc())
    echo "<tr>
<td>" . $row["username"] . "</td>
<td>" . $row["feedback"] ."</td>
<td>" . $row["rating"] . "</td>
<td>" . $row["created_at"] ."</td>
<td><a href='?delete_id=" .$row["id"] . "' class='deletebutton'>Delete</a></td>
</tr>";
}
 else {
echo "<tr><td colspan='5'>No feedback found.</td></tr>";
}


$conn->close();
?>
</table>
</div>
</body>
</html>