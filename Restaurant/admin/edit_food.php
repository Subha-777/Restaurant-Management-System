<?php
session_start();
if(!isset($_SESSION['admin_logged_in']))
{
 header('Location:Login.php');
 exit();
}
include 'Configure.php';
$id=$_GET['id'];
$food_item=$conn->query("SELECT * FROM food_items WHERE id=$id")->fetch_assoc();
if($_SERVER['REQUEST_METHOD']=='POST')
{
 $name=$_POST['name'];
 $description=$_POST['description'];
 $price=$_POST['price'];
 $buying_price=$_POST['buying_price'];
 $quantity=$_POST['quantity'];
 $food_image=$_POST['food_image'];

 

 //Update database
 $stmt=$conn->prepare("UPDATE food_items SET food_name=?, description=?,price=?,buying_price=?,quantity=?,food_image=? WHERE id=?");
 $stmt->bind_param('ssdidsi',$name,$description,$price,$buying_price,$quantity,$food_image,$id);
 if($stmt->execute()) 
{
 header('Location: Manage_food.php');
 exit();
 }
 else{
 $message="Error updating food item:".$conn->error;
 }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Edit Food Item</title>
 <link rel="stylesheet" href="edit_food.css">
 <style>
    body{
 background:gray;
 margin: 0;
 padding: o;
}
header{
 background-color: transparent;
 color: black;
 padding: 20px;
 text-align: center;
}
header h1{
 margin: 0;
}
nav{
 margin: 20px 0px;
 text-align: center;
}
nav a{
 color: black;
 margin: 0 15px;
 text-decoration: none;
 font-size: 1.1em;
}
nav a:hover{
 text-decoration: underline;
}
section{
 margin: 20px;
 padding: 20px;
 background-color: transparent;
 border-radius: 10px;
 box-shadow: 0 0 10px (rgba 0, 0, 0, 0.1)
}
form{
 margin-bottom: 20px;
 display: flex;
 flex-direction: column;
}
label{
 margin: 10px 0 5px;
 font-weight: bold;
}
input, textarea{
 padding: 10px;
 margin-bottom: 10px;
 border: 1px solid black;
 border-radius: 5px;
 font-size: 1em;
}
textarea{
 resize: vertical;
}
button{
 width: fit-content;
 align-self: flex-start;
 background-color: black;
 color: white;
 padding: 10px 20px;
 border: none;
 border-radius: 5px;
 cursor: pointer;
 font-size: 1em;
}
button:hover{
 background-color: gray;
}
 
</style>
</head>
<body>
 <header>
  <h1> Edit Food Items</h1>
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
  <nav>
  
 </header>
 <section>
  <?php if(isset($message))
  {
   echo "<p>$message</p>";
  }
  ?>
 <form action ="edit_food.php ?id=<?php echo $id;?>" method="POST">
 <label for="name">Name:</label>
 <input type="text" name="name" id="name" value="<?php echo ($food_item['food_name']);?>"required>
 <label for="description">Description:</label>
 <textarea name="description" id="description" required><?php echo($food_item['description']);?></textarea>
 <label for="price" >Price:</label>
  <input type="number" step="0.01" name="price" id="price" value="<?php echo ($food_item['price']);?>"required>
  <label for="price" >Buying Price:</label>
  <input type="number" step="0.01" name="buying_price" id="buying_price" value="<?php echo ($food_item['buying_price']);?>"required>
 <label for="quantity">Quantity:</label>
 <input type="number" name="quantity" id="quantity" value="<?php echo ($food_item['quantity']);?>"required>

    <form action="add_food.php" method="post">
        <label for="food_image">Food Image:</label>
        <input type="text" name="food_image" id="food_image" value="<?php echo ($food_item['food_image']);?>">
        <button type="submit">Update food Item</button>
    </form>
 </form>
 </section>
 </body>
</html>
