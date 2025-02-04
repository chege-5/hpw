<?php
session_start();

include('db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

echo "Welcome, " . $_SESSION['username'] . "!<br>";
echo "<a href='logout.php'>Logout</a>";
?>
