<?php
session_start();
header('Content-type: Application/json');
require $_SERVER['DOCUMENT_ROOT']."/php/mysql.php";

if (!isset($_POST["project"]) || !isset($_SESSION["user"])) {
    echo json_encode(["status" => false, "error"=>"Aucune moulinette ne peut être lancée pour l'instant, réessaye plus tard."]);
    http_response_code(400);
    die(0);
}
$stmt = $mysql->prepare("SELECT `id` FROM `users` WHERE `id` = ? AND `authorized` = 1");
$stmt->bind_param("s", $_SESSION["user"]);
$stmt->execute();
$res = $stmt->get_result();
$array = $res->fetch_array(MYSQLI_ASSOC);
if (!$array) {
    echo json_encode(["status" => false, "error"=>"Tu n'as pas la permission de lancer une moulinette."]);
    http_response_code(400);
    die(0);
}

$stmt = $mysql->prepare("SELECT org,repo,project FROM repos where id=? AND owner = ?");
$stmt->bind_param("ss", $_POST["project"], $_SESSION["user"]);
$stmt->execute();
$res = $stmt->get_result();
$json = $res->fetch_array(MYSQLI_ASSOC);
if (!$json) {
    echo json_encode(["status" => false, "error"=>"Tu n'as pas la permission de faire passer un moulinette sur ce repo."]);
    http_response_code(400);
    die(0);
}

$stmt = $mysql->prepare("SELECT `id` FROM `mouli` WHERE `user` = ? AND `status` = 0");
$stmt->bind_param("s", $_SESSION["user"]);
$stmt->execute();
$res = $stmt->get_result();
$array = $res->fetch_array(MYSQLI_ASSOC);
if ($array) {
    echo json_encode(["status" => false, "error"=>"Tu as déjà une moulinette en attente, tu pourras en lancer une nouvelle quand elle sera terminée."]);
    http_response_code(400);
    die(0);
}

$stmt = $mysql->prepare("SELECT `id` FROM `mouli` WHERE `project` = ? AND (`status` = 0 OR `date` > NOW() - INTERVAL 5 MINUTE)");
$stmt->bind_param("s", $_POST["project"]);
$stmt->execute();
$res = $stmt->get_result();
$array = $res->fetch_array(MYSQLI_ASSOC);
if ($array) {
    echo json_encode(["status" => false, "error"=>"Merci de patienter avant de relancer une moulinette sur ce projet."]);
    http_response_code(400);
    die(0);
}

$stmt = $mysql->prepare("SELECT token FROM users where id=?");
$stmt->bind_param("s", $_SESSION["user"]);
$stmt->execute();
$res = $stmt->get_result();
$token = $res->fetch_array(MYSQLI_ASSOC);

$stmt = $mysql->prepare("INSERT INTO mouli(project, user) VALUES (?,?)");
$stmt->bind_param("ss",$_POST["project"], $_SESSION["user"]);
$stmt->execute();
$id = $mysql->insert_id;

$decrypted = openssl_decrypt($token["token"], getenv("CIPHER"), getenv("CIPHER_KEY"), 0, getenv("CIPHER_IV"));
exec(sprintf("cd %s/moulinette && ./moulitek %s %s %s %s %d", $_SERVER["DOCUMENT_ROOT"], $decrypted, $json["repo"], $json["org"], $json["project"], $id), $lines, $status);

if ($status != 0) {
	$stmt = $mysql->prepare("DELETE FROM `mouli` WHERE `id` = ? AND `user` = ?");
	$stmt->bind_param("ss", $id, $_SESSION["user"]);
	$stmt->execute();

    echo json_encode(["status" => false, "error"=>"Tu ne peux pas lancer de moulinette pour l'instant, ressaye plus tard..."]);
    http_response_code(400);
    die(0);
}

echo json_encode(["status" => true]);
?>
