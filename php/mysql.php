<?php
$mysql = mysqli_connect(getenv("DB_HOST"), getenv("DB_USER"), getenv("DB_PASS"), getenv("DB_NAME"));
if (!$mysql) {
    exit;
}
$mysql->set_charset("utf8");
?>