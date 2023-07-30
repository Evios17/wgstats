<?php

    ///
    ///
    /// Script de dépendance qui contient les fonctions pour vérifier les logins/token d'un utilisateur
    ///
    ///

    // Si un utilisateur tente d'accéder à ce script sans qu'il soit rataché à un autre script
    if (!defined('INCLUDED')) {
        header("HTTP/1.1 403 FORBIDDEN");
        die();
    }

    $loginSafe = true;

    // Fonction qui check la validité des logins donnés
    function credentialsChecker(\PDO $link, string $username, string $password) {
        // On fetch le nom d'utilisateur et le mot de passe
        try {
            // Formulation de la requête
            $stmt = "SELECT username, password FROM logins WHERE username=:username";

            // Préparation de la requête
            $query = $link->prepare($stmt);
            $query->bindParam(':username', $username);

            // Éxecution de la requête
            $query->execute();

            // On crécupère les résultats
            $result = $query->fetchAll();
        } catch (PDOException $e) {
            throw new Exception('Error while fetching username and password from database.');
        }

        // Si il y a plusieurs résultats en même temps, la base de données est dysfonctionnelle
        if (count($result) > 1) throw new Exception('Found multiple similar username in database.');

        // Si il y a moins d'un résultat, c'est que l'username ne match aucun utilisateur
        if (count($result) === 0) return false;

        // Double check si le nom d'utilisateur correspond bien à celui donné par l'utilisateur
        if ($username !== $result[0]['username']) return false;

        // On vérifie si le mot de passe est identique
        if (!password_verify($password, $result[0]['password'])) return false;

        // Si on arrive ici après toutes les étapes de vérifications, alors l'username et le mot de passe est valide
        return true;
    }

    // Fonction pour récupérer le token d'un utilisateur
    function getToken(\PDO $link, string $username) {
        // On fetch le nom d'utilisateur et le mot de passe
        try {
            // Formulation de la requête
            $stmt = "SELECT token FROM logins WHERE username=:username";

            // Préparation de la requête
            $query = $link->prepare($stmt);
            $query->bindParam(':username', $username);

            // Éxecution de la requête
            $query->execute();

            // On crécupère les résultats
            $result = $query->fetchAll();
        } catch (PDOException $e) {
            throw new Exception('Error while fetching username and password from database: ' . $e);
        }

        // Si on trouve plus d'un résultat, il y a une défaillance dans la base de données
        if (count($result) > 1) throw new Exception('Found multiple similar username in database');

        // Si il n'y a pas de résultat, l'utilisateur n'existe pas
        if (count($result) < 1 ) return false;

        return $result[0]['token'];

    }

    // Fonction qui check si le token est valide
    function tokenChecker(\PDO $link, string $usertoken) {
        try {
            // Formulation de requête
            $stmt = "SELECT token, token_date FROM logins WHERE token=:token";
    
            // Préparation de la requête
            $query = $link->prepare($stmt);
            $query->bindParam(':token', $usertoken);

            // Éxecution de la requête
            $query->execute();
    
            // On récupère les résultats
            $result = $query->fetchAll();
        } catch (PDOException $e) {
            throw new Exception('Error while fetching token from database');
        }

        // Si on trouve plus d'un résultat, il y a une défaillance dans la base de données
        if (count($result) > 1) throw new Exception('Found multiple similar token in database');

        // Si il n'y a pas de résultat, le token n'existe pas
        if (count($result) < 1 ) return false;

        // Double check si la clé est bien celui donné par l'utilisateur
        if ($usertoken !== $result[0]['token']) return false;

        // Si la date du token est périmée, on refuse la validation du token
        if ($result[0]['token_date'] < time()) return false;

        // Si on arrive ici, le token est valide
        return true;

    }

    // Fonction qui update les tokens expirés
    function tokenAgeUpdater(\PDO $link, string $username, int $expiration_window) {

        // Dépendances
        require_once(__DIR__.'/../scripts/utils.php');      // Pour le randomStringGenerator()

        try {
            // Formulation de la requête
            $stmt = "SELECT token_date FROM logins WHERE username=:username";
    
            // Préparation de la requête
            $query = $link->prepare($stmt);
            $query->bindParam(':username', $username);

            // Éxecution de la requête
            $query->execute();
    
            // On récupère les résultats
            $result = $query->fetchAll();
        } catch (PDOException $e) {
            throw new Exception('Error while fetching token date from database');
        }

        // Si on trouve plus d'un résultat, il y a une défaillance dans la base de données
        if (count($result) > 1) throw new Exception('Found multiple similar results in database');

        // Si aucun nom d'utilisateur ne match, il y a eu un changement entre temps, il n'est donc plus valide
        if (count($result) < 1) throw new Exception('No username matches in database');

        // Si la date du token est vieux comparé à celle actuelle, on renouvelle le token.
        // Dans le cas contraire, on return null.
        if ($result[0]['token_date'] < time()) {

            $newToken = randomStringGenerator(24);
            $token_expiration = (time()+$expiration_window);

            try {
                // Formulation de la requête
                $stmt = "UPDATE logins SET token=:newtoken, token_date=:token_date WHERE username=:username";
        
                // Préparation de la requête
                $query = $link->prepare($stmt);
                $query->bindParam(':username', $username);
                $query->bindParam(':newtoken', $newToken);
                $query->bindParam(':token_date', $token_expiration);
    
                // Éxecution de la requête
                $query->execute();
            } catch (PDOException $e) {
                throw new Exception(`Error while updating token from database : `.$e);
            }

            return $newToken;

        }

        return null;

    }