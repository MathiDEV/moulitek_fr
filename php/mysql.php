<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
$mysql = mysqli_connect(getenv("DB_HOST"), getenv("DB_USER"), getenv("DB_PASS"), getenv("DB_NAME"));
if (!$mysql) {
    exit;
}
$mysql->set_charset("utf8");
$mysql->query("DELETE FROM `mouli` WHERE `status` = 0 AND `date` < DATE_SUB(NOW(), INTERVAL 15 MINUTE)");
?>