<?php
$servername = "sql8.freesqldatabase.com";
$username = "sql8784737";
$password = "SNXWjH7Iih";  // Replace this once it finishes loading
$database = "sql8784737";

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