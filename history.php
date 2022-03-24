<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: /signup");
}
if (!isset($_GET["id"])) {
    header("Location: /");
}
require $_SERVER["DOCUMENT_ROOT"] . "php/mysql.php";

$stmt = $mysql->prepare("SELECT * FROM `repos` WHERE `owner` = ? AND `id` = ?");
$stmt->bind_param("ss", $_SESSION["user"], $_GET["id"]);
$stmt->execute();
$res = $stmt->get_result();
$repo = $res->fetch_all(MYSQLI_ASSOC);
if (!$repo) {
    header("Location: /");
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous">
    </script>
    <script src="https://kit.fontawesome.com/b269430966.js" crossorigin="anonymous"></script>
    <script src="/js/index.js"></script>
    <link rel="stylesheet" href="/css/main.css">
    <title>Moulitek</title>
</head>

<body>
    <?php include $_SERVER["DOCUMENT_ROOT"] . "/php/header.php";?>
    <div class="projects row mx-2 justify-content-around">
        <?php
        require $_SERVER["DOCUMENT_ROOT"] . "/php/show_card.php";
        showCardsHistory($repo);
        ?>
    </div>
    <?php include $_SERVER["DOCUMENT_ROOT"] . "/php/footer.php";?>
</body>

</html>
