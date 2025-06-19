<?php
$servername = "sql7.freesqldatabase.com";
$username = "sql7777349";
$password = "8Iib6bgQeK";  // Replace this once it finishes loading
$database = "sql7777349";

// Old DB connection removed
$conn = new mysqli("sql8.freesqldatabase.com", "sql8784737", "SNXWjH7Iih", "sql8784737", 3306);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$servername = "sql7.freesqldatabase.com";
$username = "sql7777349";
$password = "8Iib6bgQeK";  // Replace this once it finishes loading
$database = "sql7777349";

// Old DB connection removed
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Debugging: Show error if query fails
$sql = "SELECT driver_id, latitude, longitude FROM bus_driver_locations";
$result = $conn->query($sql);

if (!$result) {
    die("SQL Query Failed: " . $conn->error);
}

$drivers = [];
while ($row = $result->fetch_assoc()) {
    $drivers[] = $row;
}

// Send JSON response
echo json_encode($drivers);

$conn->close();
?>