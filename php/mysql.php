<?php
$mysql = mysqli_connect($_ENV["DB_HOST"], $_ENV["DB_USER"], $_ENV["DB_PASS"], $_ENV["DB_NAME"]);
if (!$mysql) {
    exit;
}
$mysql->set_charset("utf8");
?>