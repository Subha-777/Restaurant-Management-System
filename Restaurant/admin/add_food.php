<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])) {
 header('Location: Login.php');
 exit();
}
include 'Configure.php';
if($_SERVER['REQUEST_METHOD']=='POST') {
$name=$_POST['name'];
$description=$_POST['description'];
$price=$_POST['price'];
$quantity=$_POST['quantity'];
$buying_price=$_POST['buying_price'];
$food_image=$_POST['food_image'];

$stmt = $conn->prepare("INSERT INTO food_items(food_name, description, price, quantity, buying_price, food_image) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssdiis", $name, $description, $price, $quantity, $buying_price, $food_image);

if($stmt->execute()){
 header('Location: Manage_food.php');
 exit();
}
else{
$message="Error adding food item:".$conn->error;
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Add Food Item</title>
 <link rel="stylesheet" href="">
<style>
body{
 background:gray;
 margin: 0;
 padding: 0;
}
h1{
    color:black;
}
header{
 background-color: transparent;
 
 padding: 20px;
 text-align: center;
}
header h1{
 margin: 0;
}
nav{
 margin: 20px 0;
 text-align: center;
}
nav a{
 color: black;
 margin: 0 15px;
 text-align: none;
 font-size: 1.1em;
 text-decoration:none;
}
nav a:hover{
 text-decoration: underline;
}
section{
 margin: 20px;
 padding: 20px;
 background-color:transparent;
 border-radius: 10px;
 box-shadow: 0 0 10px rgba(0,0,0,0.1);
}
form{
 margin-bottom: 20px;
 display: flex;
 flex-direction: column;
}
label{
 margin: 10px 0 5px;
 text-weight: bold;
}
input, textarea{
 padding: 10px;
 margin-bottom: 10px;
 border: 1px solid black;
 border-radius: 5px;
 font-size: 1em;
}

/*textarea{
 resize: vertical;
}*/
button{
 width: 200px;
 margin-left: 40em;
 margin-top: 1em;
 
 padding: 10px 20px;
 border: none;
 border-radius: 5px;
 cursor: pointer;
 font-size: 1em;
}
button:hover{
 background-color: rgba(0,0,0,0.5);
}
</style>
</head>
<body>
 <header>
  <h1>Add Food Item</h1>
  <nav>
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
  </nav>
 </header>
 <section>
 <?php if(isset($message)){echo "<p>$message</p>";}?>
<form action="add_food.php" method="POST">
<label for="name">Name:</label>
<input type="text" name="name" id="name" required>
<lable for="description">Description:</lable>
<textarea name="description" id="description" required></textarea>
<label for="price">Price:</label>
<input type="number" step="0.01" name="price" id="price" required>
<label for="buying_price">Buying Price:</label>
<input type="number" step="0.01" name="buying_price" id="buying_price" required>
<label for="quantity">Quantity:</label>
<input type="number" name="quantity" id="quantity" required>
<label for="image">IMAGE :</label>
<input type="file" name="food_image" id="food_image" required>
<button type="submit">Add food Item</button>
</form>
</section>
</body>
</html>
