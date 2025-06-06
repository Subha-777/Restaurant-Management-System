<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}
include 'Configure.php';

// Fetch admin details
$sql = "SELECT * FROM admin WHERE id = 1";
$result = $conn->query($sql);
$admin = $result->fetch_assoc();

// Handle profile update
if (isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $profile_picture = $admin['profile_picture']; // Keep old picture if no new upload

    // Handle file upload
    if (!empty($_FILES['profile_picture']['name'])) {
        $target_dir = "uploads/";  // Folder where images will be stored
        $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
        
        // Move uploaded file to the folder
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            $profile_picture = $target_file; // Update profile picture path
        } else {
            echo "Error uploading file.";
        }
    }

    // Update admin details
    $conn->query("UPDATE admin SET name='$name', email='$email', password='$password', profile_picture='$profile_picture' WHERE id=1");
    header("Location: admin_profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('bg.avif') no-repeat center center fixed;
            background-size: cover;
            color: white;
            text-align: center;
        }
        .profile-container {
            width: 40%;
            margin:80px 350px;
            background: rgba(0, 0, 0, 0.7);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(255, 255, 255, 0.3);
            text-align: center;
        }
        .profile-pic {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 3px solid white;
        }
        .edit-btn {
            background: purple;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        .modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            color: black;
        }
        .modal input {
            width: 100%;
            padding: 8px;
            margin: 10px 0;
        }
        a{
            margin-top:10px;
            padding:10px;
            border-radius:5px;
            width:20px;
            color:white;
            background:purple;
            text-decoration:none;
            margin-right:1270px;
            
        }
    </style>
</head>
<body>
    <a href="home.php"><</a>
    <div class="profile-container">
        <img src="<?php echo $admin['profile_picture']; ?>" class="profile-pic" alt="Profile Picture">
        <h2><?php echo $admin['name']; ?></h2>
        <p><strong>Email:</strong> <?php echo $admin['email']; ?></p>
        <p><strong>Password:</strong> <?php echo $admin['password']; ?> </p>
        <button class="edit-btn" onclick="document.getElementById('editModal').style.display='block'">Edit Profile</button>
    </div>

    <div id="editModal" class="modal">
        <form method="POST" action="" enctype="multipart/form-data">
            <h2>Edit Profile</h2>
            <input type="text" name="name" value="<?php echo $admin['name']; ?>" required>
            <input type="email" name="email" value="<?php echo $admin['email']; ?>" required>
            <input type="password" name="password" placeholder="New Password (Optional)">
            <input type="file" name="profile_picture">
            <button type="submit" name="update_profile">Save Changes</button>
            <button type="button" onclick="document.getElementById('editModal').style.display='none'">Cancel</button>
        </form>
    </div>
</body>
</html>
