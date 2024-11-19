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

$userController = new UserController($db); 


if($_SERVER["REQUEST_METHOD"] === "GET") {
    try {
        if(isset($_GET["id"]) && $_GET["id"] !== "all") {
            $user = $userController->getById($_GET["id"]);
            unset($user["password"]);
            echo json_encode(["message" => "User retrieved successfully", "data" => $user]);
        } elseif(!isset($_GET["id"]) || $_GET["id"] === "all") {
        $users = $userController->getAll();
        if(is_array($users)) {
            foreach($users as &$u) {
                unset($u["password"]);
            }
        }
            echo json_encode(["message" => "Users retrieved successfully", "data" => $users]);
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
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (!isset($data["id"])) {
            throw new Exception("User ID is required");
        }
        
        $updated_user = new UserModel(
            $data["id"],
            $data["username"] ?? null,
            $data["email"] ?? null,
            $data["password"] ?? null,
            $data["role_id"] ?? null
        );
        
        $user = $userController->update($updated_user);
        echo json_encode(["message" => "User updated successfully"]);
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
        $data = json_decode(file_get_contents("php://input"), true);
        $user = $userController->delete($data["id"]);
        echo json_encode(["message" => "User deleted successfully"]);
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
        $data = json_decode(file_get_contents("php://input"), true);
        $new_us = new UserModel(0, $data["username"], $data["email"], $data["password"], $data["role_id"]);
        $user = $userController->create($data);
        echo json_encode(["message" => "User created successfully", "data" => $user]);
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