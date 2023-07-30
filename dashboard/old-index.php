<?php

    define('INCLUDED', true);

    http_response_code(500);

    // On verifie l'existance d'un cookie de session
    require_once(__DIR__.'/../api/scripts/answering.php');
    require_once(__DIR__.'/../api/scripts/varpool.php');
    require_once(__DIR__.'/../api/scripts/db.php');
    require_once(__DIR__.'/../api/scripts/login.php');

    if (!isset($_COOKIE['id'])) {
        header('Location: ../login/');
        exit();
    }

    $usertoken = base64_decode($_COOKIE['id']);

    if(!tokenChecker($pdo, $usertoken)){
        header('Location: ../login/');
        exit();
    }

    header("HTTP/1.1 200 OK");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Settings -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Infos -->
    <title>BlackWG v1.0</title>
    <meta name="title" content="BlackWG v1.0">
    <meta name="author" content="Antoine Doro, Lukas Boyer">
    <meta name="description" content="Panel de controle WEB pour Wireguard.">
    
    <!-- Import -->
    <link rel="shortcut icon" href="../../main/media/favicon.webp" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="app">
        <div class="lyt">
            <header class="hdr">
                <div class="hdr-ctn">
                    <nav class="nav">
                        <button class="nav-btn onglet-button" data-onglet="0"><i class="fa-solid fa-house"></i><span>Accueil</span></button>
                    </nav>

                    <div class="ctg-nav">
                        <nav class="nav">
                            <button class="nav-btn onglet-button" id="navBtn1" data-onglet="1"><i class="fa-solid fa-bell"></i><span>Notification</span></button>
                            <button class="nav-btn onglet-button" data-onglet="2"><i class="fa-solid fa-gear"></i><span>Paramètre</span></button>
                        </nav>
                        <div class="prf">
                            <img src="https://images.pexels.com/photos/3721320/pexels-photo-3721320.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" alt="">
                            <div class="prf-data">
                                <span class="prf-data-user">---</span>
                                <span class="prf-data-grade">Administrateur</span>
                            </div>
                            <button class="prf-btn"><i class="fa-solid fa-right-from-bracket"></i></button>
                        </div>
                    </div>
                </div>
            </header>
            <div class="ctn">
                <div class="onglet active" data-onglet="0">
                    <div class="onglet-layout page-1">
                        <div class="table">
                            <div class="table-commande">
                                <div class="table-commande-info">
                                    <h2 class="table-title">Interfaces</h2>
                                    <!-- <p class="table-desc">Le tableau affiche de manière résumée l'ensemble des interfaces du serveur WireGuard. Chaque interface est représentée par une ligne dans le tableau, fournissant des informations clés sur son état et sa configuration. Les colonnes du tableau peuvent contenir des détails tels que l'adresse IP associée à chaque interface, le port utilisé pour la communication, le nom de l'interface, ainsi que son statut (actif ou inactif). Ce tableau permet d'avoir une vue d'ensemble rapide et organisée des différentes interfaces du serveur WireGuard, facilitant ainsi la gestion et la configuration du réseau.</p> -->
                                </div>
                                <!-- <div class="table-commande-in">
                                    <div class="table-commande-slt table-commande-slt-search">
                                        <input type="text"  placeholder="Zone de saisie ..">
                                        <button><i class="fa-solid fa-xmark"></i></button>
                                    </div>
                                    <button class="table-commande-btn">Rechercher</button>
                                </div> -->
                            </div>
                            <div class="table-head">
                                <div class="table-line">
                                    <div class="table-cell">
                                        <span>Nom</span>
                                    </div>
                                    <div class="table-cell">
                                        <span>Statut</span>
                                    </div>
                                    <div class="table-cell">
                                        <span>Total reçu</span>
                                    </div>
                                    <div class="table-cell">
                                        <span>Total transmit</span>
                                    </div>
                                    <div class="table-cell">
                                        <span>Adresse</span>
                                    </div>
                                    <div class="table-cell">
                                        <span>Port d'écoute</span>
                                    </div>
                                </div>
                            </div>
                            <div class="table-body interfaces">
                                <div class="table-line">
                                    <div class="table-cell">
                                        <span>---</span>
                                    </div>
                                    <div class="table-cell">
                                        <span class="table-cell-status">---</span>
                                    </div>
                                    <div class="table-cell">
                                        <span>---</span>
                                    </div>
                                    <div class="table-cell">
                                        <span>---</span>
                                    </div>
                                    <div class="table-cell">
                                        <span>---</span>
                                    </div>
                                    <div class="table-cell">
                                        <span>---</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table">
                            <div class="table-commande">
                                <div class="table-commande-info">
                                    <h2 class="table-title">Peers</h2>
                                    <!-- <span class="table-desc">Le tableau des peers affiche de manière concise les informations relatives aux différentes connexions de pairs sur une interface spécifique du serveur WireGuard. L'utilisateur est invité à sélectionner l'interface à inspecter avant d'afficher le tableau correspondant. Chaque ligne du tableau représente un pair connecté à l'interface choisie, en fournissant des détails tels que l'adresse IP du pair, la clé publique associée, ainsi que l'état de la connexion (actif ou inactif). Ce tableau permet de visualiser les détails essentiels des connexions établies avec les pairs sur une interface spécifique du serveur WireGuard.</span> -->
                                </div>
                                <div class="table-commande-in">
                                    <select class="table-commande-slt" id="selector">
                                        <option value="" disabled selected><-- Selectionnez une option --></option>
                                    </select>
                                    <button class="table-commande-btn" id="selectorBtn"><span>Sélectionner</span></button>
                                </div>
                            </div>
                            <div class="table-head">
                                <div class="table-line">
                                    <div class="table-cell">
                                        <span>Peer</span>
                                    </div>
                                    <div class="table-cell">
                                        <span>Endpoint</span>
                                    </div>
                                    <div class="table-cell">
                                        <span>Dernier Handshake</span>
                                    </div>
                                    <div class="table-cell">
                                        <span>Reçu</span>
                                    </div>
                                    <div class="table-cell">
                                        <span>Transmit</span>
                                    </div>
                                    <div class="table-cell">
                                        <span>Plage d'IP autorisée</span>
                                    </div>
                                </div>
                            </div>
                            <div class="table-body peers">
                                <div class="table-line">
                                    <div class="table-cell">
                                        <span>---</span>
                                    </div>
                                    <div class="table-cell">
                                        <span>---</span>
                                    </div>
                                    <div class="table-cell">
                                        <span>---</span>
                                    </div>
                                    <div class="table-cell">
                                        <span>---</span>
                                    </div>
                                    <div class="table-cell">
                                        <span>---</span>
                                    </div>
                                    <div class="table-cell">
                                        <span>---</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="onglet" data-onglet="1">
                    <span>Notification</span>
                </div>
                <div class="onglet" data-onglet="2">
                    <span>Paramètre</span>
                </div>
            </div>
            <div class="footer">
                <div class="footer-ctn">
                    <div>
                        <p class="version">WGSTATS © Copyright 2023 - Tous droits réservés</p>
                    </div>
                    <div>
                        <p class="version">{Tableau de bord v1.0}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="notif-layout">
            <div class="notif-ctn">
                <div class="notif-ct">
                    <span>---</span>
                    <button><i class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="notif-bar-layout">
                    <div class="notif-bar-cursor"></div>
                </div>
            </div>
            <div class="notif-ctn">
                <div class="notif-ct">
                    <span>---</span>
                    <button><i class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="notif-bar-layout">
                    <div class="notif-bar-cursor"></div>
                </div>
            </div>
            <div class="notif-ctn">
                <div class="notif-ct">
                    <span>---</span>
                    <button><i class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="notif-bar-layout">
                    <div class="notif-bar-cursor"></div>
                </div>
            </div>
            <div class="notif-ctn">
                <div class="notif-ct">
                    <span>---</span>
                    <button><i class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="notif-bar-layout">
                    <div class="notif-bar-cursor"></div>
                </div>
            </div>
            <div class="notif-ctn">
                <div class="notif-ct">
                    <span>---</span>
                    <button><i class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="notif-bar-layout">
                    <div class="notif-bar-cursor"></div>
                </div>
            </div>
        </div>
    </main>

    <script src="js/wgFunctions.js"></script>
    <script src="js/utils.js"></script>
    <script src="js/main.js"></script>
    <script src="js/logout.js"></script>
    <script src="js/nav.js"></script>
    <script src="js/onglet.js"></script>
</body>
</html>