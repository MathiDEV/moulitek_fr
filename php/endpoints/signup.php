<?php
session_start();
require $_SERVER['DOCUMENT_ROOT']."/checkers/checkers.php";
header('Content-type: Application/json');

check_text();
check_email();
check_password();
check_token();

if (count($errors)) {
    echo json_encode(["status" => false]);
    http_response_code(400);
    die(0);
}

$encrypted_password = password_hash($_POST["password"], PASSWORD_BCRYPT, ['cost' => getenv("BCRYPT_COST")]);
echo $_POST["password"];
$encrypted_token = openssl_encrypt($_POST["token"], getenv("CIPHER"), getenv("CIPHER_KEY"), 0, getenv("CIPHER_IV"));

$stmt = $mysql->prepare("INSERT INTO `users`(`name`, `email`, `password`, `token`) VALUES (?,?,?,?)");
$stmt->bind_param("ssss", $_POST["pseudo"], $_POST["email"], $encrypted_password, $encrypted_token);
if (!$stmt->execute()) {
    echo json_encode(["status" => false]);
    http_response_code(400);
    die(0);
}
$_SESSION["user"] = $mysql->insert_id;
echo json_encode(["status" => true]);