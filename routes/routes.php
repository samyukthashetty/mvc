<?php

require_once '../controllers/AuthController.php';
require_once '../Database/Database.php';

// Initialize database and repositories
$database = new Database();
$repository = new AuthControllerRepository($database);
$tokenRepository = new TokenRepository($database);

// Pass both the repository and the database to the AuthController
$authController = new AuthController($repository, $tokenRepository, $database);

// Get request method and URI
$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Log the method and URI for debugging
error_log("Request method: $method, Request URI: $uri");

// Normalize the URI to remove any trailing slashes
$normalizedUri = rtrim($uri, '/');

// Define routes
$routes = [
    'POST' => [
        '/fdapp/routes/routes.php/api/register' => 'registerUser',
        '/fdapp/routes/routes.php/api/login' => 'loginUser',
        '/fdapp/routes/routes.php/api/logout' => 'logoutUser',
    ],
    'GET' => [
        '/fdapp/routes/routes.php/api/validate-token' => 'validateToken',
    ],
];

// Function to extract the token from the Authorization header
function getBearerToken() {
    $headers = apache_request_headers();
    if (isset($headers['Authorization'])) {
        $matches = [];
        if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
            return $matches[1];
        }
    }
    return null;
}

// Check if the route exists for the requested method and URI
if (isset($routes[$method][$normalizedUri])) {
    // Get the controller method associated with the route
    $controllerMethod = $routes[$method][$normalizedUri];

    // Retrieve request data
    $data = json_decode(file_get_contents('php://input'), true);

    // Extract token for routes that require it
    if (in_array($controllerMethod, ['validateToken', 'logoutUser'])) {
        $token = getBearerToken();
        if (!$token) {
            http_response_code(401);
            echo json_encode(['error' => 'Authorization token not found']);
            exit;
        }
        $data = ['token' => $token];
    }

    // Call the appropriate method of AuthController
    $response = call_user_func([$authController, $controllerMethod], $data);

    // Return response
    header('Content-Type: application/json');
    echo json_encode(['message' => $response]);
} else {
    // Handle invalid routes
    http_response_code(404);
    echo json_encode(['error' => 'Invalid route']);
}
?>
