<?php
include 'Configure.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $feedback = $_POST['feedback'];
    $rating = $_POST['rating'];

    $query = "INSERT INTO feedback (username, feedback, rating) VALUES ('$username', '$feedback', '$rating')";
    if (mysqli_query($conn, $query)) {
        echo "Success";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
