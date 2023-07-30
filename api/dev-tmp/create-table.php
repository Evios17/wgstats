<?php
    try {
        $logindb = new PDO('sqlite:'.'./wgdb.sqlite');
        $logindb->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $logindb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        header("HTTP/1.1 503 Service Unavailable");
        echo 'Error : ' . $e;
    }

    try {
        // Formulation de la requête
        $query = 'CREATE TABLE IF NOT EXISTS logins (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username text UNIQUE,
            password text,
            token text UNIQUE,
            token_date INTEGER
        )';
        
        // Exécution de la requête
        $logindb->query($query);
    } catch (PDOException $e) {
        header("HTTP/1.1 503 Service Unavailable");
        echo 'Error : ' . $e;
        die();
    }

    echo 'LOGIN TABLE CREATED <br><br>';

    try {
        // Formulation de la requête
        $query = 'CREATE TABLE IF NOT EXISTS aliases (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            peer text UNIQUE,
            alias text
        )';
        
        // Exécution de la requête
        $logindb->query($query);
    } catch (PDOException $e) {
        header("HTTP/1.1 503 Service Unavailable");
        echo 'Error : ' . $e;
        die();
    }

    echo 'ALIAS TABLE CREATED <br><br>';