// Variables globales
var username = atob(getCookie('username')),
    actualInt = false,
    interfaces = {},
    prevActiveInt = [],
    activeInt = [],
    wgdata = {};

// On place le nom d'utilisateur
document.querySelector('.header-button-data-user').textContent = username;


// Affichage des peers de l'interface sélectionné
document.querySelector("#selectorBtn").addEventListener("click", () => {
    actualInt = document.querySelector("#selector").value;
    displayPeers(actualInt);
});

async function main() {

    var firstLoop = true;

    while(true) {

        // On répète ce processus toutes les cinq secondes
        // On retire l'attente des cinq secondes lors de la première itération
        firstLoop ? null : await sleep(5000);

        // On fetch d'abord les interfaces du serveur
        await fetchInterfaces();

        // On fetch les interfaces actives ainsi que leurs paramètres et statistiques
        await fetchWGdata();
        
        // On stock les interfaces précédemment récupérées pour pouvoir les comparer plus tard
        if (activeInt.length > 0) prevActiveInt = activeInt;

        // On stocke le nom des interfaces actives
        activeInt = Object.keys(wgdata);

        // On affiche/actualise les interfaces
        displayInterfaces();
        
        // On affiche/actualise les peers de l'interface sélectionnée
        displayPeers(actualInt);

        // On met à jour la liste des interfaces à sélectionner
        updateOptionSelector();

        firstLoop = false;
    }

}

(async () => main())();
