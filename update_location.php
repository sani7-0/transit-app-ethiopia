<?php
$host = 'sql8.freesqldatabase.com';
$dbname = 'sql8784737';
$username = 'sql8784737';
$password = 'SNXWjH7Iih';
$port = 3306;

$conn = new mysqli($host, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



//
// 3) Update the driver_locations table
//
$sql = "INSERT INTO bus_driver_locations (driver_id, latitude, longitude, last_updated)
        VALUES (?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE latitude = VALUES(latitude),
                                longitude = VALUES(longitude),
                                last_updated = NOW()";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die(json_encode([ 'error' => "❌ Prepare failed: " . $conn->error ]));
}

$stmt->bind_param("idd", $driver_id, $latitude, $longitude);

if ($stmt->execute()) {
    echo json_encode([ 'success' => '✅ Location updated', 'driver_id' => $driver_id ]);
} else {
    echo json_encode([ 'error' => "❌ Execute failed: " . $stmt->error ]);
}

$stmt->close();
$conn->close();
?>