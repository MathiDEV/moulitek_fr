<?php
require "checkers.php";

header('Content-type: Application/json');

check_token();
echo json_encode($errors);
if (count($errors)) {
    http_response_code(400);
}
