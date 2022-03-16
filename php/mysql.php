<?php
$mysql = mysqli_connect(apache_getenv("DB_HOST"), apache_getenv("DB_USER"), apache_getenv("DB_PASS"), apache_getenv("DB_NAME"));
if (!$mysql) {
    exit;
}
$mysql->set_charset("utf8");
?>