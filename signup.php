<?php
session_start();
if (isset($_SESSION["user"])) {
    header("Location: /");
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/signup.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous">
    </script>
    <script src="https://kit.fontawesome.com/b269430966.js" crossorigin="anonymous"></script>
    <script src="/js/signup.js"></script>
    <title>S'inscrire</title>
</head>

<body>
    <img class="logo" src="assets/img/moulitek.png">
    <div id="slides">
        <div>
            <img src="assets/img/slide1.png">
            <h2>Bienvenue sur <b>Moulitek</b></h2>
            <p class="text-secondary">Moulitek est un outil spécialisé dans l'audit de fonctionnalités des programmes.</p>
            <p class="text-secondary">Il vous met à disposition un large panel de tests fonctionnels, entièrement
                automatisés pour vos projets EPITECH.</p>
            <p class="text-secondary">L'utilisation de Moulitek est <b>réservée aux étudiants d'EPITECH.</b></p>
            <div onclick="swipeTo(1)" class="btn btn-primary text-white" role="button">S'inscrire <i
                    class="fas fa-chevron-right" aria-hidden="true"></i></div>
            <a class="mt-2" href="signin"><small>Se connecter</small></a>
        </div>
        <div>
            <img src="assets/img/slide2.png">
            <h2>Dis-nous tout !</h2>
            <p class="text-secondary">Pour créer ton compte, nous avons besoin de quelques informations !</p>
            <form checker="check_user">
                <div class="form-group">
                    <input required type="text" class="form-control" name="pseudo" id="pseudo" maxlength=50
                        placeholder="Pseudo">
                    <div class="invalid-feedback text-start"></div>
                </div>
                <div class="form-group my-2">
                    <input required type="email" class="form-control" name="email" id="email"
                        aria-describedby="emailHelp" placeholder="Email EPITECH">
                    <div class="invalid-feedback text-start"></div>
                </div>
                <div class="form-group mb-2">
                    <input required type="password" class="form-control" name="password" id="new-password"
                        placeholder="Mot de passe">
                    <div class="invalid-feedback text-start"></div>
                </div>
                <div onclick="checkFormAndSwipe(1)" class="btn btn-primary text-white" role="button">Poursuivre <i
                        class="fas fa-chevron-right" aria-hidden="true"></i></div>
            </form>
        </div>
        <div>
            <img src="assets/img/slide3.png">
            <h2>On peut voir ton github ?</h2>
            <p class="text-secondary">Pour pouvoir lancer les tests sur tes projets nous devons avoir accès à tes
                repertoires GitHub.</p>
            <div role="button" data-bs-toggle="modal" data-bs-target="#token_modal" class="btn btn-secondary mb-2">Configurer
                mon token</div>
            <p class="text-secondary"><small>Ce token est un gage de <b>sécurité</b> pour toi : si tu te désinscrit de
                    Moulitek, tu pourras supprimer le token et nous n'aurons plus <b>aucun accès</b> à tes repos.</small></p>
            <form checker="check_token">
                <div class="form-group mb-2">
                    <input required type="text" class="form-control" name="token" id="token" maxlength=100
                        placeholder="Token GitHub">
                    <div class="invalid-feedback text-start"></div>
                </div>
                <div onclick="checkFormAndSwipe(2)" class="btn btn-primary text-white" role="button">Finaliser
                    l'inscritption <i class="fas fa-check" aria-hidden="true"></i></div>
            </form>
        </div>
        <div>
            <img src="assets/img/slide4.png">
            <h2>Parfait !</h2>
            <p class="text-secondary">Tout est prêt ! Bienvenue sur Moulitek !</p>
            <div onclick="submitAndRedirect()" class="btn btn-primary text-white" role="button">Page d'accueil <i class="fas fa-home"
                    aria-hidden="true"></i></a>
            </div>
        </div>
    </div>
    <div class="modal fade" id="token_modal" tabindex="-1" aria-labelledby="token_modal_label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Configurer mon token</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Pour configurer un token d'accès, rend-toi sur tes <a target="_blank"
                            href="https://github.com/settings/tokens/new">paramètres de tokens personnels</a>.</p>
                    <p>Crée un nouveau token et choisis <b>Pas d'expiration</b>.</p>
                    <p>Pour intéragir corrèctement avec tes repos, Moulitek a besoin des
                        permissions
                        <b>repo</b> et <b>read:org</b>.</p>
                    <p>Tu peux ensuite cliquer sur <b>Générer le token</b> !</p>
                    <p>Il manque plus qu'à autoriser le token sur l'organisation d'EPITECH via <b>Configurer le SSO</b>.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>
    <?php include $_SERVER["DOCUMENT_ROOT"] . "/php/footer.php";?>
</body>
</html>