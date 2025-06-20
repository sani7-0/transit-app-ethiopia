<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "sql8.freesqldatabase.com";
$username   = "sql8784737";
$password   = "SNXWjH7Iih";
$database   = "sql8784737";
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    echo json_encode(["error" => "❌ DB connection failed: " . $conn->connect_error]);
    exit;
}

$TOMTOM_API_KEY = "TJ1OHQZFL3Gsaeg1GE0SdmB5347JETtr";

$sql = "SELECT * FROM routes";
$result = $conn->query($sql);
if (!$result) {
    echo json_encode(["error" => "❌ Query failed: " . $conn->error]);
    exit;
}

$routes = [];

while ($route = $result->fetch_assoc()) {
    $routeId = $route['id'];

    // Fetch stops
    //$stops = [];
    //$stopQuery = "SELECT name, latitude AS lat, longitude AS lng FROM stops WHERE route_id = $routeId ORDER BY stop_order ASC";
    //$stopResult = $conn->query($stopQuery);
    //if ($stopResult && $stopResult->num_rows > 0) {
      //  while ($stop = $stopResult->fetch_assoc()) {
        //    $stops[] = $stop;
        //}
    //}

    // Prepare waypoints
    $waypoints = [];

    if (!empty($route['start_lat']) && !empty($route['start_lng'])) {
        $waypoints[] = "{$route['start_lat']},{$route['start_lng']}";
    }

    foreach ($stops as $stop) {
        $waypoints[] = "{$stop['lat']},{$stop['lng']}";
    }

    if (!empty($route['end_lat']) && !empty($route['end_lng'])) {
        $waypoints[] = "{$route['end_lat']},{$route['end_lng']}";
    }

    $coordinates = [];

    if (count($waypoints) > 1) {
        $coordinateStr = implode(":", $waypoints);
        $tomtom_url = "https://api.tomtom.com/routing/1/calculateRoute/$coordinateStr/json?key=$TOMTOM_API_KEY";

        // ✅ Use cURL instead of file_get_contents
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $tomtom_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && $response !== false) {
            $data = json_decode($response, true);
            if (isset($data['routes'][0]['legs'])) {
                foreach ($data['routes'][0]['legs'] as $leg) {
                    foreach ($leg['points'] as $point) {
                        $coordinates[] = [
                            'lat' => $point['latitude'],
                            'lng' => $point['longitude']
                        ];
                    }
                }
            }
        } else {
            error_log("❌ TomTom API failed for route $routeId. HTTP $httpCode");
        }
    }

    $routes[] = [
        'id'          => $routeId,
        'route_name'  => $route['route_name'],
        'route_color' => $route['route_color'] ?? '#007bff',
        'coordinates' => $coordinates,
        'stops'       => $stops
    ];
}

file_put_contents('debug_routes.json', json_encode($routes, JSON_PRETTY_PRINT));
$conn->close();
echo json_encode($routes);
