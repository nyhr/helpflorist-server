<?php
// authMiddleware.php

/**
 * Handles JWT Authentication.
 *
 * @param object $jwt An instance of your JWT handling class.
 * @return array The decoded JWT payload.
 */
function authenticate($jwt) {
    // Check if the JWT token exists in the cookies
    if (isset($_COOKIE["token"])) {
        $token = $_COOKIE["token"];

        // Verify the JWT token
        if ($jwt->verifyToken($token)) {
            $payload = $jwt->getPayload($token);
            return $payload;
        } else {
            http_response_code(401);
            echo json_encode(["message" => "Invalid token"]);
            exit;
        }
    } else {
        http_response_code(401);
        echo json_encode(["message" => "Unauthorized"]);
        exit;
    }
}

/**
 * Checks if the request method is allowed.
 *
 * @param array $allowedMethods An array of allowed methods.
 */
function allowedMethods($allowedMethods) {
    if (!in_array($_SERVER["REQUEST_METHOD"], $allowedMethods)) {
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
        exit;
    }
}

 /**
 * Handles Role-Based Authorization.
 *
 * @param array $payload The decoded JWT payload.
 * @param array $methodRoles An associative array mapping HTTP methods to required role IDs.
 */
function authorizeMethod($payload, $methodRoles) {
    $method = $_SERVER["REQUEST_METHOD"];
    
    if (!isset($methodRoles[$method])) {
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
        exit;
    }

    $requiredRole = $methodRoles[$method];
    
    if ($requiredRole > 0) {
        if (!isset($payload["role_id"]) || $payload["role_id"] < $requiredRole) {
            http_response_code(403);
            echo json_encode(["message" => "Forbidden: Insufficient privileges"]);
            exit;
        }
    }
}


/**
 * Handles Role-Based Authorization.
 *
 * @param array $payload The decoded JWT payload.
 * @param int $requiredRole The minimum role_id required to access the route.
 */
function authorize($payload, $requiredRole = 2) {
    if (!isset($payload["role_id"]) || $payload["role_id"] < $requiredRole) {
        http_response_code(403);
        echo json_encode(["message" => "Forbidden"]);
        exit;
    }
}
?>
