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
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

require_once(__DIR__ . "/../..//src/lib/database/index.php");

$db = new DatabaseManager();

require_once(__DIR__ . "/../../src/controllers/index.php");
require_once(__DIR__ . "/../../src/lib/jwt/index.php");
require_once(__DIR__ . "/../../src/lib/middleware/authMiddleware.php");

$jwt = new JWT();

$methodRoles = [
    "GET" => 0,
    "POST" => 2,
    "PUT" => 2,
    "DELETE" => 3
];

$applicationController = new ApplicationController($db); 

if($_SERVER["REQUEST_METHOD"] === "GET") {
    try {
        if(isset($_GET["id"]) && $_GET["id"] !== "all") {
            $application = $applicationController->getById($_GET["id"]);
            echo json_encode(["message" => "Application retrieved successfully", "data" => $application]);

        } elseif(!isset($_GET["id"]) || $_GET["id"] === "all") {
            $applications = $applicationController->getAll();
            echo json_encode(["message" => "Applications retrieved successfully", "data" => $applications]);
        }
    } catch (Exception $e) {
        error_log($e->getMessage(), 0, ini_get("error_log"));

        if($isDevelopment) {
            echo json_encode(["message" => "An error occurred", "data" => $e->getMessage()]);
        } else {
            echo json_encode(["message" => "Internal server error"]);
        }
    }
} elseif($_SERVER["REQUEST_METHOD"] === "PUT") {
    try {
        $payload = authenticate($jwt);
authorizeMethod($payload, $methodRoles);

        $data = json_decode(file_get_contents("php://input"), true);
        $token = $_COOKIE["token"];
        $payload = $jwt->getPayload($token);
        $user_id = $payload["id"];

        $updated_application = new ApplicationModel(
            $data["id"], 
            $data["name"] ?? null,
            $data["version"] ?? null,
            $data["type"] ?? null,
            $data["download_url"] ?? null,
            $data["created_by"] ?? null,
            $data["created_at"] ?? null,
            $user_id,
            $data["updated_at"] ?? date("m-d-Y H:i:s"),
        );

        $application = $applicationController->update($updated_application);
        echo json_encode(["message" => "Application updated successfully"]);
    } catch (Exception $e) {
        error_log($e->getMessage(), 0, ini_get("error_log"));

        if($isDevelopment) {
            echo json_encode(["message" => "An error occurred", "data" => $e->getMessage()]);
        } else {
            echo json_encode(["message" => "Internal server error"]);
        }
    }
} elseif($_SERVER["REQUEST_METHOD"] === "DELETE") {
    try {
        $payload = authenticate($jwt);
authorizeMethod($payload, $methodRoles);

        $data = json_decode(file_get_contents("php://input"), true);
        $application = $applicationController->delete($data["id"]);
        echo json_encode(["message" => "Application deleted successfully"]);
    } catch (Exception $e) {
        error_log($e->getMessage(), 0, ini_get("error_log"));

        if($isDevelopment) {
            echo json_encode(["message" => "An error occurred", "data" => $e->getMessage()]);
        } else {
            echo json_encode(["message" => "Internal server error"]);
        }
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $payload = authenticate($jwt);
        authorizeMethod($payload, $methodRoles);

        $data = json_decode(file_get_contents("php://input"), true);
        $token = $_COOKIE["token"];
        $payload = $jwt->getPayload($token);
        $user_id = $payload["id"];

        $new_application = new ApplicationModel(
            $data["id"] ?? null,
            $data["name"], 
            $data["version"],
            $data["type"],
            $data["download_url"],
            $user_id,
            $data["created_at"] ?? date("m-d-Y H:i:s"),
            $user_id,
            $data["updated_at"] ?? date("m-d-Y H:i:s")
        );

        $application = $applicationController->create($new_application);
    } catch (Exception $e) {
        error_log($e->getMessage(), 0, ini_get("error_log"));

        if($isDevelopment) {
            echo json_encode(["message" => "An error occurred", "data" => $e->getMessage()]);
        } else {
            echo json_encode(["message" => "Internal server error"]);
        }
    }
}

?>