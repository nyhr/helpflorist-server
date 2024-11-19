<?php

header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

try {
    http_response_code(200);
    echo json_encode(["message" => "OK"]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["message" => "Internal server error"]);
}

?>