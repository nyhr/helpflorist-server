<?php

require_once(__DIR__ . "/RoleModel.php");
require_once(__DIR__ . "/UserModel.php");
require_once(__DIR__ . "/ApplicationModel.php");

return [
    "role" => RoleModel::class,
    "user" => UserModel::class,
    "application" => ApplicationModel::class
];
?>