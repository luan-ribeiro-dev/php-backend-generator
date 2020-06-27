<?php

use Layout\Blade;

$root = "../";
set_include_path($root);
include_once("app.php");

session_start();

if (!isset($_SESSION['usuarioId'])) header("Location: " . $root);
$logged_user = Usuario::find($_SESSION['user_id']);

Blade::view($root, "adm.index", ["logged_user" => $logged_user]);
