<?php
session_start(); // Start the session
include 'Configure.php'; 

// Fetch all feedback
$query = "SELECT * FROM feedback ORDER BY created_at ASC";
$result = mysqli_query($conn, $query);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback</title>
    <link rel="stylesheet" href="feed.css">
    <script src="script.js" defer></script>
</head>
<body>
    <h2>Customer Feedback</h2>
    <table id="feedbackTable" border="1">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Feedback</th>
            <th>Rating</th>
            <th>Submitted On</th>
            <th>Action</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['username']; ?></td>
                <td><?php echo $row['feedback']; ?></td>
                <td><?php echo $row['rating']; ?>/5</td>
                <td><?php echo $row['created_at']; ?></td>
                <td></td>
            </tr>
        <?php } ?>
    </table>

    <button onclick="addFeedbackRow()">Create Your Own Feedback</button>

</body>
</html>
