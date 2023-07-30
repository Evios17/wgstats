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
    <title>WGSTATS v1.0</title>
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
    <main class="application">
        <div class="hv-separator">
            <nav class="navigation">
                <div class="navigation-logo">
                    <h1>WG-Stats</h1>
                </div>
                <div class="navigation-separator">
                    <div class="navigation-button">
                        <button class="onglet-button btn1" data-onglet="0">
                            <i class="fa-solid fa-house"></i>
                            <span>Accueil</span>
                        </button>
                    </div>
                    <div class="navigation-button">
                        <button class="onglet-button btn0" data-onglet="1">
                            <i class="fa-solid fa-gear"></i>
                            <span>Paramètres</span>
                        </button>
                    </div>
                </div>
                <footer class="footer">
                    <span>WGSTATS © Copyright 2023 - Tous droits réservés</span>
                </footer>
            </nav>
            <div class="hd-separator">
                <header class="header">
                    <div class="header-separator">
                        <nav class="header-button">
                            <button id="notification" class="btn1">
                                <i class="fa-solid fa-bell"></i>
                                <span>Notification</span>
                            </button>
                        </nav>
                        <div class="header-button">
                            <img class="header-button-img" src="https://images.pexels.com/photos/3721320/pexels-photo-3721320.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" alt="">
                            <div class="header-button-data">
                                <span class="header-button-data-user">---</span>
                                <span class="header-button-data-grade">Administrateur</span>
                            </div>
                            <button id="logout" class="btnLogout btn1">
                                <i class="fa-solid fa-right-from-bracket"></i>
                            </button>
                        </div>
                    </div>
                </header>
                <div class="onglet-container">
                    <div class="notification">
                        <div class="notification-content">
                            <span>notif</span>
                        </div>
                        
                    </div>
                    <div class="onglet-display active" data-onglet="0">
                        <div class="onglet-content">
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
                                        <select class="table-commande-slt btn1" id="selector">
                                            <option value="" disabled selected><-- Selectionnez une option --></option>
                                        </select>
                                        <button class="table-commande-btn btn0" id="selectorBtn"><span>Sélectionner</span></button>
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
                                            <span></span>
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
                    <div class="onglet-display" data-onglet="1">
                        <div class="onglet-content">
                            <div class="wb">
                                <h2>Paramètres</h2>
                                <div>
                                    <h3>Version du logiciel</h3>
                                </div>
                                <div>
                                    <h3>Affichage</h3>
                                </div>
                                <div>
                                    <h3>Notification</h3>
                                </div>
                                <div>
                                    <h3>Confidentialité</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- <div class="notif-layout">
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
        </div> -->
    </main>

    <script src="js/wgFunctions.js"></script>
    <script src="js/utils.js"></script>
    <script src="js/main.js"></script>
    <script src="js/logout.js"></script>
    <script src="js/nav.js"></script>
    <script src="js/onglet.js"></script>
</body>
</html>