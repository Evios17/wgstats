<?php

    ///
    ///
    /// Script de dépendance qui contient des variables globale
    ///
    ///

    // Si un utilisateur tente d'accéder à ce script sans qu'il soit rataché à un autre script
    if (!defined('INCLUDED')) {
        header("HTTP/1.1 403 FORBIDDEN"); 
        die();
    }

    $varpoolSafe = true;

    $dbpath = '/var/www/html/wgstats/api/dev-tmp/wgdb.sqlite';      // PLACER ICI LE CHEMIN DE LA BASE DE DONNÉES DES LOGINS
    $token_expiration = 3600*24;                                    // Temps en secondes après expiration d'un token (24h par défaut)
