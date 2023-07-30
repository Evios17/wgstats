<?php

    ///
    ///
    /// Script pour créer/modifier/supprimer un alias
    ///
    ///

    define('INCLUDED', true);   // Permet d'accéder aux dépendances

    // On prépare à l'avance le code d'erreur 500 au cas si le script fail
    http_response_code(500);

    // On indique que nous envoyons des données JSON
    header('Content-Type: application/json; charset=utf-8');

    // On inclue les dépendances pour ce script
    require_once('../../scripts/answering.php');            // Pour pouvoir répondre au client
    require_once('../../scripts/varpool.php');              // Pour include les variables globales
    require_once('../../scripts/utils.php');                // Pour le checkPOST()
    require_once('../../scripts/db.php');                   // Pour intéragir avec la bdd
    require_once('../../scripts/login.php');                // Pour vérifier la validité d'un token
    require_once('../../scripts/aliasdb.php');              // Fonctions pour manipuler les alias dans la base de données

    // On check si le client effectue bien une requête POST
    checkPOST();

    // On test si un token a été passé en variable POST
    if (!isset($_POST['token']) || $_POST['token'] == "") {
        header("HTTP/1.1 400 Bad Request");
        $response['status'] = "ERROR";
        $response['message'] = "Token is invalid.";
        answer($response);
    }

    // On décode le string encodé en base64
    $usertoken = base64_decode($_POST['token']);

    // On check si le token est valide
    if (!tokenChecker($pdo, $usertoken)) {
        header("HTTP/1.1 401 Unauthorized");
        $repsonse['status'] = "ERROR";
        $response['message'] = "Token is invalid";
        answer($response);
    }

    // On check si le peer ainsi que son alias a été donné en POST
    if (!isset($_POST['peer']) || $_POST['peer'] === "" || !isset($_POST['alias'])) {
        header("HTTP/1.1 400 Bad Request");
        $response['status'] = "ERROR";
        $response['message'] = "Wrong parameter value.";
        answer($response);
    }

    $peer = $_POST['peer'];
    $alias = $_POST['alias'];

    /// On commence d'abord par vérifier si le peer existe déjà dans la table

    // Si l'alias existe déjà dans la base de données
    if (checkAlias($pdo, $peer)) {

        // Si l'alias donné en POST est vide, alors l'utilisateur souhaite supprimer l'alias actuel
        if ($alias === "") {
            delAliasFromDB($pdo, $peer);
            header("HTTP/1.1 200 OK");
            $response['status'] = "OK";
            $response['message'] = "Alias deleted.";
            answer($response);
        }

        // Sinon l'utilisateur veux mettre à jour l'alias actuel
        updateAliasFromDB($pdo, $peer, $alias);
        header("HTTP/1.1 200 OK");
        $response['status'] = "OK";
        $response['message'] = "Alias updated.";
        answer($response);
    } else {
        // Si l'alias n'existe pas encore dans la base de données, on le créé
        addAliasToDB($pdo, $peer, $alias);
        header("HTTP/1.1 200 OK");
        $response['status'] = "OK";
        $response['message'] = "Alias created.";
        answer($response);
    }
    