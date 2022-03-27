<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require $_SERVER['DOCUMENT_ROOT'] . "/php/mysql.php";
if (!isset($_POST["id"])) {
    die(0);
}

$stmt = $mysql->prepare("DELETE FROM `mouli` WHERE `id` = ? AND `status` = 0");
$stmt->bind_param("s", $_POST["id"]);
$stmt->execute();
