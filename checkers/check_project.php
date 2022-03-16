<?php
require "checkers.php";

header('Content-type: Application/json');

check_text("titre de projet", "title");
check_url();
check_project();
echo json_encode($errors);
if (count($errors)) {
    http_response_code(400);
}
