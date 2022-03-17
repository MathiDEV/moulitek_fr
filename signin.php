<?php
session_start();
if (isset($_SESSION["user"]))
    header("Location: /");
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/signup.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous">
    </script>
    <script src="https://kit.fontawesome.com/b269430966.js" crossorigin="anonymous"></script>
    <script src="/js/signin.js"></script>
    <title>Se connecter</title>
</head>

<body>
    <img class="logo" src="assets/img/moulitek.png">
        <div id="slides">
        <div>
            <img src="assets/img/slide1.png">
            <h2>Se connecter</h2>
            <form checker="check_user">
                <div class="form-group my-2">
                    <input required type="email" class="form-control" autocomplete="email" name="email" id="email"
                        aria-describedby="emailHelp" placeholder="Email EPITECH">
                    <div class="invalid-feedback text-start"></div>
                </div>
                <div class="form-group mb-2">
                    <input required type="password" class="form-control" autocomplete="password" name="password" id="password"
                        placeholder="Mot de passe">
                    <div class="invalid-feedback text-start"></div>
                </div>
                <div onclick="signIn()" class="btn btn-primary text-white" role="button">Connexion <i
                        class="fas fa-chevron-right" aria-hidden="true"></i></div>
            </form>
            <a class="mt-2" href="signup"><small>S'inscrire</small></a>
        </div>
    </div>
    <?php include $_SERVER["DOCUMENT_ROOT"]."/php/footer.php";?>
</body>

</html>