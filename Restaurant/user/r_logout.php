<?php
session_start();
session_unset();
session_destroy();
header("Location: reservation.php"); // Redirect to the homepage after logout
exit();
?>