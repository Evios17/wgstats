<?php

    ///
    ///
    /// Script pour authentifer l'utilisateur via username et password
    ///
    ///

    define('INCLUDED', true);   // Permet d'accéder aux dépendances

    // On définit un code d'erreur en avance si la page ne finit pas son exécution
    http_response_code(500);

    // On inclue les dépendances pour ce script
    require_once(__DIR__.'/../scripts/answering.php');
    require_once(__DIR__.'/../scripts/utils.php');
    require_once(__DIR__.'/../scripts/varpool.php');
    require_once(__DIR__.'/../scripts/db.php');
    require_once(__DIR__.'/../scripts/login.php');

    // On check si le client fait une requête POST
    checkPOST();

    // On check si le client a bien envoyé la variable POST pour les crédits
    if (!isset($_POST['credentials']) || $_POST['credentials'] == "") {
        header("HTTP/1.1 400 Bad Request");
        $response['status'] = "ERROR";
        $response['message'] = "Credentials are missing.";
        answer($response);
    }

    // On décode le string reçu
    $decoded_credentials = base64_decode($_POST['credentials']);

    // Si le décodage a échoué
    if ($decoded_credentials === false) {
        header("HTTP/1.1 400 Bad Request");
        $response['status'] = "ERROR";
        $response['message'] = "Credentials were badly transmitted.";
        answer($response);
    }

    // On explose le string en deux parties
    $credentials = explode(';', $decoded_credentials);
    $credentials_count = count($credentials);

    // Si il n'y a qu'un seul ou plus de deux élément dans l'array, le découpage a échoué
    if ($credentials_count < 2 || $credentials_count > 2) {
        header("HTTP/1.1 400 Bad Request");
        $response['status'] = "ERROR";
        $response['message'] = "Credentials were badly transmitted.";
        answer($response);
    }

    // On stocke les crédentials dans des variables distinctes
    $username = $credentials[0];
    $passwd = $credentials[1];

    // On teste les logins
    if (!credentialsChecker($pdo, $username,$passwd)) {
        header("HTTP/1.1 401 Unauthorized");
        $response['status'] = "ERROR";
        $response['message'] = "Wrong logins.";
        answer($response);
    }

    // On vérifie si le token n'a pas besoin d'un renouvellement
    $newToken = tokenAgeUpdater($pdo, $username, $token_expiration);

    // Si il n'y a pas de nouveaux tokens à générer, donner le token actuel
    if ($newToken === null) {

        // On récupère le token actuel de l'utilisateur
        $token = getToken($pdo, $username);

        // Si il n'y a pas de token, quelque chose de bizarre s'est produit.... 
        // Il faudrait probablement appeler un exorciste à ce point là..
        if ($token === false) throw new Exception('Username vanished since last call.');

        header("HTTP/1.1 200 OK");
        $response['status'] = "SUCCESS";
        $response['content'] = ['token' => base64_encode($token)];
        answer($response);
    }

    // On donne enfin le nouveau token à l'utilisateur
    header("HTTP/1.1 200 OK");
    $response['status'] = "SUCCESS";
    $response['content'] = ['token' => base64_encode($newToken)];
    answer($response);