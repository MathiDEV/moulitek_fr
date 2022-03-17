<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: /signup");
}
require "php/mysql.php";
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
    <style>
    .footer-dark {
        margin-top: 100px;
    }
    </style>
    <title>Moulitek</title>
</head>

<body>
    <?php include $_SERVER["DOCUMENT_ROOT"]."/php/header.php";?>
    <div class="projects row mx-2 justify-content-around">
        <div class="card shadow-sm border-0 col-sm" role="button">
            <div data-bs-toggle="modal" data-bs-target="#add_project" class="card-body text-muted d-flex justify-content-center align-items-center flex-column">
                <i style="font-size: 100px;" class="fal fa-plus" aria-hidden="true"></i>
                <p class="h4">Ajouter un projet</p>
            </div>
        </div>
    <?php
    require $_SERVER["DOCUMENT_ROOT"]."/php/show_card.php";
    $stmt = $mysql->prepare("SELECT * FROM `repos` WHERE `owner` = ?");
    $stmt->bind_param("s", $_SESSION["user"]);
    $stmt->execute();
    $res = $stmt->get_result();
    $repos = $res->fetch_all(MYSQLI_ASSOC);
    showCards($repos);
    ?>
    </div>
    <div class="modal fade" id="add_project" tabindex="-1" aria-labelledby="add_project_label" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Ajouter un projet</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <p class="m-0">Titre</p>
                      <input type="text" class="form-control" placeholder="Exemple" name="title">
                    <div class="invalid-feedback text-start"></div>
                    </div>
                    <div class="form-group mt-3">
                        <p class="m-0">Clé de clone HTTPS</p>
                      <input type="text" class="form-control" placeholder="https://github.com/Exemple/exemple.git" name="repo">
                    <div class="invalid-feedback text-start"></div>
                    </div>
                    <div class="form-group mt-3">
                        <p class="m-0">Nom du projet</p>
                        <select class="form-select form-select mb-3" name="project" aria-label=".form-select-lg example">
                            <option value="-1" selected>Sélectionner...</option>
                            <?php
                            $stmt = $mysql->prepare("SELECT * FROM `tests`");
                            $stmt->execute();
                            $res = $stmt->get_result();
                            $array = $res->fetch_all(MYSQLI_ASSOC);

                            foreach ($array as $test) {
                                echo '<option value="'.$test["id"].'">'.$test["label"].'</option>';
                            }
                            ?>
                          </select>
                    <div class="invalid-feedback text-start"></div>

                    </div>
                  </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button onclick="addProject()" type="button" class="btn btn-primary">Sauvegarder</button>
            </div>
          </div>
        </div>
      </div>
    <?php include $_SERVER["DOCUMENT_ROOT"] . "/php/footer.php";?>
</body>

</html>
