<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ✅ Set timezone to Ethiopia
date_default_timezone_set('Africa/Addis_Ababa');

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
$now = date('Y-m-d H:i:s');  // ✅ Local Ethiopia time

// DB config
$host     = 'sql8.freesqldatabase.com';
$dbname   = 'sql8784737';
$username = 'sql8784737';
$password = 'SNXWjH7Iih';
$port     = 3306;

$conn = new mysqli($host, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    echo json_encode(['error' => "❌ Connection failed: " . $conn->connect_error]);
    exit;
}

// SQL: Insert or update driver's location
$sql = "INSERT INTO bus_driver_locations (driver_id, latitude, longitude, last_updated)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            latitude = VALUES(latitude),
            longitude = VALUES(longitude),
            last_updated = VALUES(last_updated)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['error' => "❌ Prepare failed: " . $conn->error]);
    exit;
}

$stmt->bind_param("idds", $driver_id, $latitude, $longitude, $now);

if ($stmt->execute()) {
    echo json_encode(['success' => '✅ Location updated', 'timestamp' => $now]);
} else {
    echo json_encode(['error' => "❌ Execute failed: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
