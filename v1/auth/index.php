<?php

$settings = parse_ini_file(__DIR__ . "/../../settings.ini");
$isDevelopment = $settings["mode"] === "development";

if($isDevelopment) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

ini_set('log_errors', $settings["log_errors"]);
ini_set('error_log', $settings["error_log"]);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");


require_once(__DIR__ . "/../../src/lib/database/index.php");

$db = new DatabaseManager();

require_once(__DIR__ . "/../../src/controllers/index.php");
require_once(__DIR__ . "/../../src/lib/jwt/index.php");
require_once(__DIR__ . "/../../src/lib/middleware/authMiddleware.php");

$authController = new AuthController($db);
$jwt = new JWT();

$methodRoles = [
    "POST" => 0,
    "PUT" => 1,
    "DELETE" => 0,
    "GET" => 1
];

if($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    $username = $data["username"];
    $password = $data["password"];
    $token = $authController->login($username, $password);
    
    setcookie("token", $token, time() + 3600, "/");

    echo json_encode(["message" => "Login successful"]);

} elseif($_SERVER["REQUEST_METHOD"] === "PUT") {
    $payload = authenticate($jwt);
    authorizeMethod($payload, $methodRoles);

    $token = $_COOKIE["token"];
    $newToken = $jwt->verifyAndRefreshToken($token);
    if($newToken) {
        setcookie("token", $newToken, time() + 3600, "/");
        echo json_encode(["message" => "Token refreshed successfully"]);
    } else {
        echo json_encode(["message" => "Token is invalid"]);
    }
} elseif($_SERVER["REQUEST_METHOD"] === "DELETE") {
    setcookie("token", "", time() - 3600, "/");
    echo json_encode(["message" => "Logout successful"]);

} elseif($_SERVER["REQUEST_METHOD"] === "GET") {
    $payload = authenticate($jwt);
    authorizeMethod($payload, $methodRoles);

    $token = $_COOKIE["token"];
    $payload = $jwt->verifyToken($token);
    echo json_encode(["message" => "Token verified successfully"]);

} else {
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed"]);
}
?>