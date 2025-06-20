<?php
// get_locations.php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ✅ Correct DB credentials
$servername = "sql8.freesqldatabase.com";
$username   = "sql8784737";
$password   = "SNXWjH7Iih";
$database   = "sql8784737";

// Connect to MySQL
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    echo json_encode(["error" => "❌ Connection failed: " . $conn->connect_error]);
    exit;
}

// Get drivers updated in the last 2 minutes
$twoMinutesAgo = date('Y-m-d H:i:s', strtotime('-2 minutes'));

$sql = "
  SELECT l.driver_id, l.latitude, l.longitude, l.last_updated,
         d.route, r.route_name, r.route_color
    FROM bus_driver_locations l
    JOIN bus_drivers d ON l.driver_id = d.id
    JOIN routes r ON d.route = r.id
   WHERE l.last_updated >= '$twoMinutesAgo'
   ORDER BY l.last_updated DESC
";

$result = $conn->query($sql);
if (!$result) {
    echo json_encode(["error" => "❌ Query failed: " . $conn->error]);
    exit;
}

$locations = [];
while ($row = $result->fetch_assoc()) {
    $locations[] = [
        'user_id'      => (int)$row['driver_id'],
        'latitude'     => (float)$row['latitude'],
        'longitude'    => (float)$row['longitude'],
        'last_updated' => $row['last_updated'],
        'route'        => $row['route'],
        'route_name'   => $row['route_name'],
        'route_color'  => $row['route_color']
    ];
}

$conn->close();
echo json_encode($locations);
?>
