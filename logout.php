<?php
session_start();
session_destroy();
header("Location: login.php");
exit();
?>
$conn = new mysqli("sql8.freesqldatabase.com", "sql8784737", "SNXWjH7Iih", "sql8784737", 3306);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
session_start();
session_destroy();
header("Location: login.php");
exit();
?>
