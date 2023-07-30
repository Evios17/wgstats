<?php

    ///
    ///
    /// Script de dépendance qui contient les fonctions et la variable pour pouvoir se connecter à la base de données
    /// Ce script vérifie aussi l'intégrité de la BDD
    ///
    ///

    /// Dépendances : varpool & answering
    // Si un utilisateur tente d'accéder à ce script sans qu'il soit rataché à un autre script
    if (!defined('INCLUDED')) {
        header("HTTP/1.1 403 FORBIDDEN");
        die();
    }

    // Variable fail-safe pour check les dépendances
    $dbSafe = true;

    // Ce script a besoin de dépendances, on check si ils ont été inclues
    if (!isset($varpoolSafe) || !isset($answeringSafe)) {
        header("HTTP/1.1 500 Internal Server Error");
        echo "Dependencies were not included";
        die();
    }

    /// Tentative de lecture de la base de données

    try {
        $pdo = new PDO('sqlite:'.$dbpath);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
            header("HTTP/1.1 503 Service Unavailable");
            // Cette section est à supprimer/réécrire pour éviter de divulger des informations dans erreurs
            $response['status'] = "ERROR";
            $response['message'] = "Can't read/create the database.";
            $response['content'] = ["pdo-error" => $e->getMessage()];
            answer($response);
    }

    /// Vérification de la table des logins

    $checkLogins = (function(\PDO $link) {

        $query = '';
        $results = '';
        $table = '';
    
        try {
            // Formulation de requête
            $query = "SELECT name FROM sqlite_master WHERE type='table' AND name='logins'";
    
            // Exécution de la requête
            $results = $link->query($query);
    
            $table = $results->fetchAll();
        } catch (PDOException $e) {
            return $e;
        }

        // Si la table n'existe pas
        if (count($table) < 1) {
            return false;
        }

        return true;
    })($pdo);

    // Switch case pour savoir l'état de la table de logins
    switch ($checkLogins) {
        case true:
            break;
        case false:
            header("HTTP/1.1 503 Service Unavailable");
            // Cette section est à supprimer/réécrire pour éviter de divulger des informations dans erreurs
            $response['status'] = "ERROR";
            $response['message'] = "Login table not found.";
            answer($response);
            break;
        default:
            header("HTTP/1.1 503 Service Unavailable");
            // Cette section est à supprimer/réécrire pour éviter de divulger des informations dans erreurs
            $response['status'] = "ERROR";
            $response['message'] = "Can't operate with database.";
            $response['content'] = ["pdo-error" => $checkLogins->getMessage()];
            answer($response);
    }

    unset($checkLogins);

    /// Vérification de la table des alias

    $checkAliases = (function(\PDO $link) {

        $query = '';
        $results = '';
        $table = '';
    
        try {
            // Formulation de requête
            $query = "SELECT name FROM sqlite_master WHERE type='table' AND name='aliases'";
    
            // Exécution de la requête
            $results = $link->query($query);
    
            $table = $results->fetchAll();
        } catch (PDOException $e) {
            return $e;
        }

        // Si la table n'existe pas
        if (count($table) < 1) {
            return false;
        }

        return true;
    })($pdo);

    // Switch case pour savoir l'état de la table de alias
    switch ($checkAliases) {
        case true:
            break;
        case false:
            header("HTTP/1.1 503 Service Unavailable");
            // Cette section est à supprimer/réécrire pour éviter de divulger des informations dans erreurs
            $response['status'] = "ERROR";
            $response['message'] = "Alias table not found.";
            answer($response);
            break;
        default:
            header("HTTP/1.1 503 Service Unavailable");
            // Cette section est à supprimer/réécrire pour éviter de divulger des informations dans erreurs
            $response['status'] = "ERROR";
            $response['message'] = "Can't operate with database.";
            $response['content'] = ["pdo-error" => $checkAliases->getMessage()];
            answer($response);
    }

    unset($checkAlias);