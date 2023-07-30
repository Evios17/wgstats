<?php

    ///
    ///
    /// Script pour donner la liste des interfaces actives ET non-active suivi de leurs adresses IP
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
        
    // On répertorie la liste des fichiers dans /etc/wireguard (contient en temps normal les interfaces)
    exec('sudo ls -l /etc/wireguard/', $cmdoutput);

    $outputSize = count($cmdoutput);
    $intLines = [];
    $intWithExt = [];
    $interfaces = [];

    $final = [];

    // On commencer d'abord par prendre les lignes contenant les interfaces
    for ($i=0; $i < $outputSize; $i++) {
        str_contains($cmdoutput[$i], '.conf') ? array_push($intLines, $cmdoutput[$i]) : null;
    }

    $outputSize = count($intLines);

    // Si il n'y a pas d'interfaces
    if ($outputSize < 1) {
        header("HTTP/1.1 200 OK");
        $response['status'] = "SUCCESS";
        $response['content'] = [
            "noInt" => true,
            "interfaces" => []
        ];
        answer($response);
    }

    // On extrait le nom de l'interface et on élimine le reste
    for ($i=0; $i < $outputSize; $i++) {
        $tmpProcess = [];
        $tmpProcess = explode(' ', $intLines[$i]);
        array_push($intWithExt, $tmpProcess[count($tmpProcess)-1]);
    }

    // On récupére le nom des interfaces sans l'extension '.conf'
    for ($i=0; $i < $outputSize; $i++) {
        array_push($interfaces, substr($intWithExt[$i], 0, strlen($intWithExt[$i])-5));
    }
    
    
    // On récupère les interfaces réseaux de l'hôte
    $hostInt = net_get_interfaces();

    // On compare et garde les interfaces activées dans le tableau
    foreach($interfaces as $int) {
        // On prépare à l'avance le fait que l'interface n'est pas activée
        $final[$int] = false;
        if (isset($hostInt[$int])) {                                                                    // On vérifie d'abord si l'interface est active
            if (isset($hostInt[$int]['unicast'])) {                                                     // Ensuite on vérifie si la propriété unicast est présente sur l'interface
                foreach($hostInt[$int]['unicast'] as $array) {                                          // La propriété unicast comporte plusieurs éléments, on cherche celui qui contient la clé 'address' et 'netmask'
                    if (isset($array['address']) && isset($array['netmask'])) {
                        
                        // On convertie le masque en préfix
                        $long = ip2long($array['netmask']);
                        $base = ip2long('255.255.255.255');
                        $netmask = 32-log(($long ^ $base)+1,2);

                        // On le concatène avec l'adresse IP pour créer une notation CIDR
                        $cidr = $array['address'] . '/' . $netmask;

                        // On remplace le 'false' par l'adresse en CIDR
                        $final[$int] = $cidr;
                        break;
                    }
                }
            }
        }
    }

    // On donne les interfaces ainsi que leurs IP (si dispo) au client
    header("HTTP/1.1 200 OK");
    $response['status'] = "SUCCESS";
    $response['content'] = [
        "noInt" => false,
        "interfaces" => $final
    ];
    answer($response);