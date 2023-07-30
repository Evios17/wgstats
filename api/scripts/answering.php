<?php

    ///
    ///
    /// Script de dépendance qui contient les fonctions pour répondre au client
    ///
    ///

    // Si un utilisateur tente d'accéder à ce script sans qu'il soit rataché à un autre script
    if (!defined('INCLUDED')) {
        header("HTTP/1.1 403 FORBIDDEN"); 
        die();
    }

    $answeringSafe = true;

    // Array contenant la réponse pour le client
    $response = [
        "status" => "",
        "message" => "",
        "content" => []
    ];

    // Fonction pour répondre au client
    function answer(array $response) {
        echo json_encode($response, JSON_PRETTY_PRINT);
        exit();
    }