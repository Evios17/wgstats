<?php

    ///
    ///
    /// Script de dépendance qui contient les fonctions utilitaires
    ///
    ///

    // Si un utilisateur tente d'accéder à ce script sans qu'il soit rataché à un autre script
    if (!defined('INCLUDED')) {
        header("HTTP/1.1 403 FORBIDDEN"); 
        die();
    }

    $utilsSafe = true;

    // Fonction qui check si le client envoie une requête POST
    function checkPOST() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("HTTP/1.1 400 Bad Request");
            $response['status'] = "ERROR";
            $response['message'] = "Wrong http method given.";
            answer($response);
        }
    }

    // Générateur de strings aléatoires, utilisé pour la génération de tokens
    function randomStringGenerator(int $length = 16) {
        $bytes = random_bytes($length);
        return bin2hex($bytes);
    }
