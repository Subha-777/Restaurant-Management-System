<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}
include 'Configure.php';

// DELETE TABLE
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM book_tables WHERE id = $id");
}

// TOGGLE STATUS
if (isset($_GET['toggle_status'])) {
    $id = $_GET['toggle_status'];
    $res = $conn->query("SELECT status FROM book_tables WHERE id = $id");
    $row = $res->fetch_assoc();
    $new_status = ($row['status'] == 'reserved') ? 'available' : 'reserved';
    $conn->query("UPDATE book_tables SET status = '$new_status' WHERE id = $id");
}

// ADD NEW TABLE
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $table_name = $_POST['table_name'];
    $table_type = $_POST['table_type'];
    $price = $_POST['price'];
    $status = $_POST['status'];
    $table_image_name = $_FILES['table_image']['name'];


$check = $conn->query("SELECT * FROM book_tables WHERE table_name = '$table_name'");
    if ($check->num_rows > 0) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                let popup = document.createElement('div');
                popup.innerHTML = '<div style=\"position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);background:#fff;padding:20px 30px;border-radius:10px;box-shadow:0 0 10px rgba(0,0,0,0.2);z-index:1000;text-align:center;font-family:sans-serif\"><h3 style=\"color:red;\">❗ Table name already exists!</h3><button onclick=\"this.parentElement.remove();\" style=\"padding:8px 15px;margin-top:10px;background:#00796b;color:#fff;border:none;border-radius:5px;cursor:pointer;\">OK</button></div>';
                document.body.appendChild(popup);
            });
        </script>";
    } else {
        $conn->query("INSERT INTO book_tables (table_name, table_type, table_image, price, status) 
              VALUES ('$table_name', '$table_type', '$table_image_name', '$price', '$status')");


}
    }

// GET ALL TABLES
$tables = $conn->query("SELECT * FROM book_tables");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Tables</title>
    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(to right, #f8f8f8, #e0f7fa);
        margin: 0;
        padding: 0;
    }
    header {
            background-color: #38b2ac;
            color: white;
            padding: 1rem;
            text-align: center;
        }
        .navbar {
    padding: 10px;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    background-color: rgba(0, 0, 0, 0.5);
    border-radius: 10px;
}

.navbar a {
    color: #fff;
    text-decoration: none;
    margin: 0.5rem;
    font-weight: bold;
    transition: color 0.3s;
}

.navbar a:hover {
    color: gray;
}

    h1 {
        margin-top: 30px;
        font-size: 36px;
        color: #004d40;
    }

    .container {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 20px;
    }

    .tables-list {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        margin-top: 30px;
        width: 100%;
        gap: 20px;
    }

    .table-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        padding: 15px;
        width: 220px;
        text-align: center;
        transition: transform 0.3s;
    }

    .table-card:hover {
        transform: scale(1.03);
    }

    .table-card img {
        width: 100%;
        height: 140px;
        object-fit: cover;
        border-radius: 8px;
    }

    .table-card p {
        margin: 10px 0;
        color: #333;
    }

    .btn {
        padding: 8px 12px;
        margin: 5px;
        border: none;
        border-radius: 6px;
        font-weight: bold;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .btn:hover {
        opacity: 0.9;
    }

    .reserved {
        background-color: #e53935;
        color: white;
    }

    .available {
        background-color: #43a047;
        color: white;
    }

    .form-box {
        text-align:center;
        width: 100%;
        max-width: 600px;
        background-color: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        padding: 25px 30px;
        margin-top: 50px;
        margin-bottom: 50px;
    }

    .form-box h2 {
        color: #00695c;
        margin-bottom: 20px;
        text-align: center;
    }

    .form-box label {
        font-weight: 600;
        color: #333;
        display: block;
        margin-top: 10px;
    }

    .form-box input[type="text"],
    .form-box input[type="number"],
    .form-box input[type="file"],
    .form-box select {
        width: 90%;
        padding: 10px;
        margin-top: 6px;
        border: 1px solid #ccc;
        border-radius: 8px;
        outline: none;
        transition: border-color 0.3s ease;
    }

    .form-box input:focus,
    .form-box select:focus {
        border-color: #26a69a;
    }

    .form-box button {
        width: 100%;
        padding: 12px;
        background-color: #00796b;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: bold;
        margin-top: 20px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .form-box button:hover {
        background-color: #004d40;
    }

    @media (max-width: 768px) {
        .table-card {
            width: 90%;
        }

        .form-box {
            width: 90%;
        }
    }
</style>

</head>
<body>
    <header>
        <h1>Manage Tables</h1>
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
    </header>
<div class="container">
    <!-- Tables Display -->
    <div class="tables-list">
        <?php while ($row = $tables->fetch_assoc()): ?>
            <div class="table-card">
                <img src="<?= $row['table_image'] ?>" alt="<?= $row['table_type'] ?>" width="100">

                <p><strong>Name:</strong> <?= $row['table_name'] ?></p>
                <p><strong>Type:</strong> <?= $row['table_type'] ?></p>
                <p><strong>Price:</strong> ₹<?= $row['price'] ?></p>

                <a href="?delete=<?= $row['id'] ?>" class="btn" style="background:#444; color:#fff; text-decoration:none">Delete</a>
                
                <a style="text-decoration:none" href="?toggle_status=<?= $row['id'] ?>" 
                   class="btn <?= $row['status'] == 'reserved' ? 'reserved' : 'available' ?>">
                   <?= ucfirst($row['status']) ?> 
                </a>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Add Table Form -->
    <div class="form-box">
        <h2>Add New Table</h2>
        <form method="POST" enctype="multipart/form-data">
            <label>Table Name</label>
            <input type="text" name="table_name" required placeholder="Eg: F1, C2, O3">

            <label>Table Type</label>
            <select name="table_type" required>
                <option value="Couple Table">Couple Table</option>
                <option value="Family Table">Family Table</option>
                <option value="Outdoor Table">Outdoor Table</option>
            </select>

            <label>Table Image</label>
            <input type="file" name="table_image"  required>
            <img id="preview" src="#" alt="Image Preview" style="display:none; width:150px; margin-top:10px;" />

            <label>Price</label>
            <input type="number" name="price" step="0.01" required>

            <label>Status</label>
            <select name="status" required>
                <option value="available">Available</option>
                <option value="reserved">Reserved</option>
            </select>

            <button type="submit">Add Table</button>
        </form>
    </div>

</div>
<script>
document.querySelector('input[name="table_image"]').addEventListener('change', function(e) {
    const preview = document.getElementById('preview');
    const file = e.target.files[0];
    if (file) {
        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
    }
});
</script>

</body>
</html>
