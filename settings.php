<?php

ini_set('display_errors', parse_ini_file(__DIR__ . "/settings.ini")["error_logging"]["display_errors"]);
ini_set('log_errors', parse_ini_file(__DIR__ . "/settings.ini")["error_logging"]["log_errors"]);
ini_set('error_log', __DIR__ . "/" . parse_ini_file(__DIR__ . "/settings.ini")["error_logging"]["error_log"]);

if(php_sapi_name() !== "cli") {
    http_response_code(403);
    echo json_encode(["message" => "Forbidden"]);
    exit;
}


require_once(__DIR__ . "/src/controllers/UserController.php");
require_once(__DIR__ . "/src/lib/database/index.php");

$db = new DatabaseManager();

$userTable = new Table($db);
$userTable->create("users")
->ifNotExists()
->addColumn("id", "INTEGER", 'PRIMARY KEY AUTOINCREMENT')
->addColumn("username", "TEXT", 'UNIQUE NOT NULL')
->addColumn("password", "TEXT", 'NOT NULL')
->addColumn("email", "TEXT", 'UNIQUE NOT NULL')
->addColumn("role_id", "INTEGER", 'NOT NULL DEFAULT 1')
->addColumn("created_at", "TEXT", 'NOT NULL DEFAULT CURRENT_TIMESTAMP')
->addColumn("updated_at", "TEXT", 'NOT NULL DEFAULT CURRENT_TIMESTAMP')
->execute();

echo nl2br("Users table created successfully \n");

$applicationTable = new Table($db);
$applicationTable->create("applications")
->ifNotExists()
->addColumn("id", "INTEGER", 'PRIMARY KEY AUTOINCREMENT')
->addColumn("name", "TEXT", 'NOT NULL')
->addColumn("version", "TEXT", 'NOT NULL')
->addColumn("type", "TEXT", 'NOT NULL')
->addColumn("download_url", "TEXT", 'NOT NULL')
->addColumn("created_by", "INTEGER", 'NOT NULL')
->addColumn("created_at", "TEXT", 'NOT NULL DEFAULT CURRENT_TIMESTAMP')
->addColumn("updated_by", "INTEGER", 'NOT NULL')
->addColumn("updated_at", "TEXT", 'NOT NULL DEFAULT CURRENT_TIMESTAMP')
->execute();

echo nl2br("Applications table created successfully \n");

$roleTable = new Table($db);
$roleTable->create("roles")
->ifNotExists()
->addColumn("id", "INTEGER", 'PRIMARY KEY AUTOINCREMENT')
->addColumn("name", "TEXT", 'NOT NULL')
->execute();

echo nl2br("Roles table created successfully \n");

echo nl2br("Database created successfully \n");
?>
