<?php

    ///
    ///
    /// Script de dépendance qui contient les fonctions pour la manipulation des alias
    ///
    ///

    function checkAlias(\PDO $link, string $peer) {
        try {
            // Formulation de la requête
            $stmt = "SELECT peer from aliases WHERE peer=:peer";

            // Préparation de la requête
            $query = $link->prepare($stmt);
            $query->bindParam(':peer', $peer);

            // Éxecution de la requête
            $query->execute();

            $result = $query->fetchAll();
        } catch (PDOException $e) {
            throw new Exception('Error while fetching alias from database: ' . $e);
        }

        // Si on trouve plus d'un résultat, il y a une défaillance dans la base de données
        if (count($result) > 1) throw new Exception('Found multiple similar username in database');

        // Si le peer n'existe pas
        if (count($result) < 1 ) return false;

        return true;
    }

    function fetchAliases(\PDO $link) {
        try {
            // Formulation de la requête
            $stmt = "SELECT peer, alias from aliases";

            // Préparation de la requête
            $query = $link->query($stmt);

            // Éxecution de la requête
            $query->execute();

            $result = $query->fetchAll();
        } catch (PDOException $e) {
            throw new Exception('Error while fetching alias from database: ' . $e);
        }

        return $result;

    }

    function addAliasToDB(\PDO $link, string $peer, string $alias) {
        try {
            // Formulation de la requête
            $stmt = "INSERT INTO aliases (peer, alias) VALUES (:peer, :alias)";

            // Préparation de la requête
            $query = $link->prepare($stmt);
            $query->bindParam(':peer', $peer);
            $query->bindParam(':alias', $alias);

            // Éxecution de la requête
            $query->execute();
        } catch (PDOException $e) {
            throw new Exception('Error while adding alias to database: ' . $e);
        }
    }

    function delAliasFromDB(\PDO $link, string $peer) {
        try {
            // Formulation de la requête
            $stmt = "DELETE FROM aliases WHERE peer=:peer";

            // Préparation de la requête
            $query = $link->prepare($stmt);
            $query->bindParam(':peer', $peer);

            // Éxecution de la requête
            $query->execute();
        } catch (PDOException $e) {
            throw new Exception('Error while deleting alias from database: ' . $e);
        }
    }

    function updateAliasFromDB(\PDO $link, string $peer, string $alias) {
        try {
            // Formulation de la requête
            $stmt = "UPDATE aliases SET alias=:alias WHERE peer=:peer";

            // Préparation de la requête
            $query = $link->prepare($stmt);
            $query->bindParam(':peer', $peer);
            $query->bindParam(':alias', $alias);

            // Éxecution de la requête
            $query->execute();
        } catch (PDOException $e) {
            throw new Exception('Error while updating alias from database: ' . $e);
        }
    }