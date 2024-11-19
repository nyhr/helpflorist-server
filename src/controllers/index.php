<?php

require_once(__DIR__ . "/UserController.php");
require_once(__DIR__ . "/AuthController.php");
require_once(__DIR__ . "/RoleController.php");
require_once(__DIR__ . "/ApplicationController.php");


return [
    "UserController" => new UserController($db),
    "AuthController" => new AuthController($db),
    "RoleController" => new RoleController($db),
    "ApplicationController" => new ApplicationController($db)
];
?>