<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle both JSON and form-data input
if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
    $input = json_decode(file_get_contents('php://input'), true);
    $_POST = $input ?: [];
}

// Validate input
if (!isset($_POST['driver_id'], $_POST['latitude'], $_POST['longitude'])) {
    echo json_encode(['error' => '❌ Missing required parameters.']);
    exit;
}

$driver_id = intval($_POST['driver_id']);
$latitude = floatval($_POST['latitude']);
$longitude = floatval($_POST['longitude']);

// DB config
$host = 'sql8.freesqldatabase.com';
$dbname = 'sql8784737';
$username = 'sql8784737';
$password = 'SNXWjH7Iih';
$port = 3306;

$conn = new mysqli($host, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    echo json_encode(['error' => "❌ Connection failed: " . $conn->connect_error]);
    exit;
}

// SQL statement
$sql = "INSERT INTO bus_driver_locations (driver_id, latitude, longitude, last_updated)
        VALUES (?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE 
            latitude = VALUES(latitude),
            longitude = VALUES(longitude),
            last_updated = NOW()";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['error' => "❌ Prepare failed: " . $conn->error]);
    exit;
}

$stmt->bind_param("idd", $driver_id, $latitude, $longitude);

if ($stmt->execute()) {
    echo json_encode(['success' => '✅ Location updated', 'driver_id' => $driver_id]);
} else {
    echo json_encode(['error' => "❌ Execute failed: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
