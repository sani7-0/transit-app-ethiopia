<?php
// get_routes.php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Log function
function log_error($message) {
    file_put_contents('error_log.txt', "[" . date('Y-m-d H:i:s') . "] " . $message . "\n", FILE_APPEND);
}

// DB credentials
$servername = "sql8.freesqldatabase.com";
$username = "sql8784737";
$password = "SNXWjH7Iih";
$database = "sql8784737";
$TOMTOM_API_KEY = "TJ1OHQZFL3Gsaeg1GE0SdmB5347JETtr";

$conn = new mysqli($servername, $username, $password, $database, 3306);
if ($conn->connect_error) {
    log_error("DB connection failed: " . $conn->connect_error);
    echo json_encode([]);
    exit;
}

$routes = [];
$routeQuery = "SELECT * FROM routes";
$routeResult = $conn->query($routeQuery);

if ($routeResult && $routeResult->num_rows > 0) {
    while ($route = $routeResult->fetch_assoc()) {
        $routeId = $route['id'];
        $start = "{$route['start_lng']},{$route['start_lat']}";
        $end = "{$route['end_lng']},{$route['end_lat']}";

        // Fetch stops
        $stops = [];
        $waypoints = [$start];
        $stopQuery = "SELECT name, latitude AS lat, longitude AS lng FROM stops WHERE route_id = $routeId ORDER BY stop_order ASC";
        $stopResult = $conn->query($stopQuery);
        if ($stopResult && $stopResult->num_rows > 0) {
            while ($stop = $stopResult->fetch_assoc()) {
                $stops[] = $stop;
                $waypoints[] = "{$stop['lng']},{$stop['lat']}";
            }
        }

        $waypoints[] = $end;
        $coordinateStr = implode(":", $waypoints);
        $tomtom_url = "https://api.tomtom.com/routing/1/calculateRoute/$coordinateStr/json?key=$TOMTOM_API_KEY";

        $coordinates = [];
        try {
            $response = file_get_contents($tomtom_url);
            $data = json_decode($response, true);

            if (isset($data['routes'][0]['legs'][0]['points'])) {
                foreach ($data['routes'][0]['legs'][0]['points'] as $point) {
                    $coordinates[] = [
                        "lat" => $point['latitude'],
                        "lng" => $point['longitude']
                    ];
                }
            } else {
                log_error("TomTom response missing for route $routeId");
            }
        } catch (Exception $e) {
            log_error("TomTom fetch failed for route $routeId: " . $e->getMessage());
        }

        $routes[] = [
            "id" => $routeId,
            "route_name" => $route['route_name'],
            "route_color" => $route['route_color'],
            "coordinates" => $coordinates,
            "stops" => $stops
        ];
    }
}

$conn->close();
echo json_encode($routes);
?>
