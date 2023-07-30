<?php

    define('INCLUDED', true);

    http_response_code(500);

    // On verifie l'existance d'un cookie de session
    require_once(__DIR__.'/../api/scripts/answering.php');
    require_once(__DIR__.'/../api/scripts/varpool.php');
    require_once(__DIR__.'/../api/scripts/db.php');
    require_once(__DIR__.'/../api/scripts/login.php');

    if (isset($_COOKIE['id'])) {
        $usertoken = base64_decode($_COOKIE['id']);
        if (tokenChecker($pdo, $usertoken)) {
            header('Location: ../dashboard/');
            exit();
        }
    }

    header("HTTP/1.1 200 OK");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Settings -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Infos -->
    <title>BlackWG v1.0</title>
    <meta name="title" content="BlackWG v1.0">
    <meta name="author" content="Antoine Doro, Lukas Boyer">
    <meta name="description" content="Pannel de contrôle WEB pour Wireguard.">
    
    <!-- Import -->
    <link rel="shortcut icon" href="../../main/media/favicon.webp" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="app">
        <div class="lgn-bg">
            <div class="lgn-bg-lyt">
                <div class="lgn-data">
                    <h2>Page d'identification</h2>
                    <div>
                        <p>"L'identification permet de reconnaître et d'authentifier une personne ou un objet de manière précise et fiable."</p><p> - <b>ChatGPT</b></p>
                    </div>
                </div>
                <div class="lgn-frm">
                    <div class="lgn-frm-input">
                        <input type="text" class="lgn-frm-input-lgn" placeholder="Identifiant" required>
                        <input type="password" class="lgn-frm-input-pwd" placeholder="Mot de passe" required>
                        <div class="lgn-frm-alert"><span></span></div>
                    </div>
                    <div class="lgn-frm-checkbox">
                        <input type="checkbox" name="autolog" id="autolog">
                        <label for="autolog">Connexion automatique</label>
                    </div>
                    <button class="lgn-frm-submit">S'identifier</button>
                </div>
            </div>
            <p class="version">{Page de login v1.0}</p>
        </div>
    </main>
    <script src="js/sha256.js"></script>
    <script src="js/main.js"></script>
</body>
</html>