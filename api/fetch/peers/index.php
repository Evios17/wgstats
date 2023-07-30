<?php

    ///
    ///
    /// Script pour donner la liste des interfaces actives ainsi que la liste des peers
    ///
    ///

    define('INCLUDED', true);   // Permet d'accéder aux dépendances

    // On définit un code d'erreur en avance si la page ne finit pas son exécution
    http_response_code(500);

    // On indique que nous envoyons des données JSON
    header('Content-Type: application/json; charset=utf-8');

    // On inclue les dépendances pour ce script
    require_once('../../scripts/answering.php');          // Pour pouvoir répondre au client
    require_once('../../scripts/varpool.php');            // Pour include les variables globales
    require_once('../../scripts/utils.php');              // Pour le checkPOST()
    require_once('../../scripts/db.php');                 // Pour intéragir avec la bdd
    require_once('../../scripts/login.php');              // Pour vérifier la validité d'un token

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

    // Éxecution du script qui donne les données
    exec('sudo /usr/share/doc/wireguard-tools/examples/json/wg-json', $cmdoutput);

    /// Après avoir récupéré les données de connexion actives de Wireguard, on doit cacher la clé publique
    /// et supprimer les whitespaces/tabulations de la sortie de la commande.

    $outputsize = count($cmdoutput);    // Le nombre de ligne dans la commande
    $outputstring = "";                 // La string finale qui contiendra les données en JSON

    // On boucle sur toutes les lignes de la sortie de la commande et on cache la clé privée
    for ($i=0 ; $i < $outputsize ; $i++) {

        if (str_contains($cmdoutput[$i], '"privateKey":')) {
            $cmdoutput[$i] = '"privateKey": "[hidden]",';
        }

        $outputstring .= $cmdoutput[$i];
    }

    // On met dans le header que la requête s'est bien passé
    header("HTTP/1.1 200 OK");
    
    // On print les données de Wireguard
    echo trim(preg_replace('/\t+/', '', $outputstring));