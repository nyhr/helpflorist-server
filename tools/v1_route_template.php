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


$payload = authenticate($jwt);

$methodRoles = [
    "GET" => 2,
    "POST" => 2,
    "PUT" => 2,
    "DELETE" => 3
];

authorizeMethod($payload, $methodRoles);

$xController = new xController($db); 


if($_SERVER["REQUEST_METHOD"] === "GET") {
    try {
        if(isset($_GET["id"]) && $_GET["id"] !== "all") {

        } elseif(!isset($_GET["id"]) || $_GET["id"] === "all") {

        }
            echo json_encode(["message" => "x retrieved successfully", "data" => $x]);
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