<?php

require_once(__DIR__ . "/DatabaseManager.php");
require_once(__DIR__ . "/Insert.php");
require_once(__DIR__ . "/Update.php");
require_once(__DIR__ . "/Delete.php");
require_once(__DIR__ . "/Select.php");
require_once(__DIR__ . "/Table.php");


return [
    "insert" => Insert::class,
    "update" => Update::class,
    "delete" => Delete::class,
    "select" => Select::class,
    "table" => Table::class
];
?>