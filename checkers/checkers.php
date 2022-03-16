<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require $_SERVER['DOCUMENT_ROOT'] . "/php/mysql.php";

$errors = [];

function check_text($type = "pseudo", $elem = "pseudo")
{
    global $errors;

    if (!isset($_POST[$elem]) || strlen($_POST[$elem]) == 0) {
        $errors[$elem] = "Choisis un $type.";
        return (0);
    }
    if (!preg_match('/^\w+$/', $_POST[$elem])) {
        $errors[$elem] = "Ton $type ne peut contenir que des caractères alphanumériques et des \"_\".";
        return (0);
    }
    if (strlen($_POST[$elem]) < 3) {
        $errors[$elem] = "Ton $type doit faire au moins 3 caractères.";
        return (0);
    }
    if (strlen($_POST[$elem]) > 50) {
        $errors[$elem] = "Ton $type ne doit pas dépasser 50 caractères.";
        return (0);
    }
    return (1);
}

function check_email()
{
    global $errors, $mysql;

    if (!isset($_POST["email"]) || strlen($_POST["email"]) == 0) {
        $errors["email"] = "Renseigne ton email.";
        return (0);
    }
    if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "Ton email est invalide.";
        return (0);
    }
    if (substr($_POST["email"], -11) != "@epitech.eu") {
        $errors["email"] = "Ton email n'est pas une adresse EPITECH.";
        return (0);
    }
    if (strlen($_POST["email"]) > 200) {
        $errors["email"] = "Ton email est trop long.";
        return (0);
    }
    $stmt = $mysql->prepare("SELECT `id` FROM `users` WHERE `email` = ?");
    $stmt->bind_param("s", $_POST["email"]);
    $stmt->execute();
    $res = $stmt->get_result();
    $array = $res->fetch_array(MYSQLI_NUM);

    if ($array) {
        $errors["email"] = "Ton email est déjà pris.";
        return (0);
    }
    return (1);
}

function check_password()
{
    global $errors;

    if (!isset($_POST["password"]) || strlen($_POST["password"]) == 0) {
        $errors["password"] = "Définit un mot de passe.";
        return (0);
    }
    if (!preg_match('/^.*[0-9].*$/', $_POST["password"]) || !preg_match('/^.*[\w].*$/', $_POST["password"]) || strlen($_POST["password"]) < 8) {
        $errors["password"] = "Ton mot de passe doit faire au moins 8 caractères et conternir des lettres et des nombres.";
        return (0);
    }
    if (strlen($_POST["password"]) > 200) {
        $errors["password"] = "Ton mot de passe est trop long.";
        return (0);
    }
    return (1);
}

function check_token()
{
    global $errors;

    if (!isset($_POST["token"]) || strlen($_POST["token"]) == 0) {
        $errors["token"] = "Renseigne ton token.";
        return (0);
    }
    if (!preg_match('/^ghp_(\w+)$/', $_POST["token"])) {
        $errors["token"] = "Ce token n'est pas un token GitHub correct.";
        return (0);
    }
    $lines = null;
    exec("curl -I -H \"Authorization: token " . $_POST["token"] . "\" https://api.github.com", $lines);
    if (strpos($lines[0], "200") == false) {
        $errors["token"] = "Ce token n'existe pas.";
        return (0);
    }
    $lines = null;
    exec("curl -sS -f -I -H \"Authorization: token " . $_POST["token"] . "\" https://api.github.com | grep -i x-oauth-scopes:", $lines);
    if (!in_array("repo", explode(", ", str_replace("x-oauth-scopes: ", "", $lines[0]))) || !(in_array("read:org", explode(", ", str_replace("x-oauth-scopes: ", "", $lines[0]))) || in_array("admin:org", explode(", ", str_replace("x-oauth-scopes: ", "", $lines[0]))))) {
        $errors["token"] = "Ce token n'a pas les permissions nécessaires.";
        return (0);
    }
    return (1);
}

function check_credentials()
{
    global $errors, $mysql;

    if (!isset($_POST["password"]) || strlen($_POST["password"]) == 0) {
        $errors["password"] = "Saisit ton mot de passe.";
    }
    if (!isset($_POST["email"]) || strlen($_POST["email"]) == 0) {
        $errors["email"] = "Renseigne ton email.";
    }
    if (count($errors)) {
        return (0);
    }
    $stmt = $mysql->prepare("SELECT `id`, `password` FROM `users` WHERE `email` = ?");
    $stmt->bind_param("s", $_POST["email"]);
    $stmt->execute();
    $res = $stmt->get_result();
    $array = $res->fetch_array(MYSQLI_ASSOC);
    if (!$array) {
        $errors["password"] = "Ces identifiants ne correspondent pas.";
        return (0);
    }
    if (!password_verify($_POST["password"], $array["password"])) {
        $errors["password"] = "Ces identifiants ne correspondent pas.";
        return (0);
    }
    $_SESSION["user"] = $array["id"];
    return (1);
}

$project_name = "";

function check_project()
{
    global $errors, $mysql, $project_name;

    if (!isset($_POST["project"]) || strlen($_POST["project"]) == 0 || $_POST["project"] == '-1') {
        $errors["project"] = "Choisis un projet.";
        return (0);
    }
    $stmt = $mysql->prepare("SELECT `name` FROM `tests` WHERE `id` = ?");
    $stmt->bind_param("s", $_POST["project"]);
    $stmt->execute();
    $res = $stmt->get_result();
    $array = $res->fetch_array(MYSQLI_ASSOC);
    if (!$array) {
        $errors["project"] = "Testeur introuvable sur les serveurs Moulitek.";
        return (0);
    }
    $project_name = $array["name"];
    return (1);
}

function check_url()
{
    global $errors, $mysql, $project_name;

    if (!isset($_POST["repo"]) || strlen($_POST["repo"]) == 0) {
        $errors["repo"] = "Renseigne un repo.";
        return (0);
    }
    if (!preg_match("/^https:\/\/github\.com\/([\w. -]+)\/([\w. -]+)\.git$/", $_POST["repo"], $matches) && count($matches) == 3) {
        $errors["repo"] = "Ce repo est invalide.";
        return (0);
    }

    $stmt = $mysql->prepare("SELECT `token` FROM `users` WHERE `id` = ?");
    $stmt->bind_param("s", $_SESSION["user"]);
    $stmt->execute();
    $res = $stmt->get_result();
    $array = $res->fetch_array(MYSQLI_NUM);
    $token = openssl_decrypt($array[0], $_ENV["CIPHER"], $_ENV["CIPHER_KEY"], 0, $_ENV["CIPHER_IV"]);
    exec("curl -H \"Authorization: token $token\" https://api.github.com/repos/$matches[1]/$matches[2]", $lines);
    $lines = implode("\n", $lines);
    $lines = json_decode($lines, true);
    if (!isset($lines["id"])) {
        $errors["repo"] = "Tu n'as pas la permission d'ajouter ce repo.";
        return (0);
    }
	$stmt = $mysql->prepare("SELECT `id` FROM `repos` WHERE `owner` = ? AND `org` = ? AND `repo` = ?");
    $stmt->bind_param("sss", $_SESSION["user"], $lines["owner"]["login"], $lines["name"]);
    $stmt->execute();
    $res = $stmt->get_result();
    $array = $res->fetch_array(MYSQLI_NUM);
    if ($array) {
        $errors["repo"] = "Ce repo est déjà enregistré sur votre compte Moulitek.";
        return (0);
    }
    if (!check_text("titre de projet", "title") || !check_project()) {
        return (0);
    }
    $stmt = $mysql->prepare("INSERT INTO `repos`(`name`, `owner`, `org`, `repo`, `project`) VALUES (?,?,?,?,?)");
    $stmt->bind_param("sssss", $_POST["title"], $_SESSION["user"], $lines["owner"]["login"], $lines["name"], $project_name);
    $stmt->execute();
    return (1);
}
