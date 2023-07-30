// Fonction pour récupérer la valeur d'un cookie
function getCookie(cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for(let i = 0; i <ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

// Fonction pour sleep
function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

// Fonction pour convertir le temps en secondes vers string lisible
function timeFormat(timeInSeconds) {
    let days, hours, minutes, seconds;
    let result = "";
  
    days = Math.floor(timeInSeconds / 86400);
    timeInSeconds %= 86400;
    hours = Math.floor(timeInSeconds / 3600);
    timeInSeconds %= 3600;
    minutes = Math.floor(timeInSeconds / 60);
    seconds = Math.floor(timeInSeconds % 60);
  
    if (days > 0) {
        result += `${days} <b>j</b> `;
    }
    if (hours > 0) {
        result += `${hours} <b>h</b> `;
    }
    if (minutes > 0) {
        result += `${minutes} <b>m</b> `;
    }
    if (seconds > 0) {
        result += `${seconds} <b>s</b>`;
    }
  
    return result;
}

// Fonction pour convertir les données en octets vers string lisible
function dataSizeFormat(size) {
    let Go, Mo, ko;
    let result = "";

    Go = size * Math.pow(10, -9);
    Mo = size * Math.pow(10, -6);
    ko = size * Math.pow(10, -3);

    if (Go > 1) {
        result = `${Number.parseFloat(Go).toFixed(1)} <b>Go</b> `;
    } else if (Mo > 1) {
        result = `${Number.parseFloat(Mo).toFixed(1)} <b>Mo</b> `;
    } else if (ko > 1) {
        result = `${Number.parseFloat(ko).toFixed(1)} <b>ko</b> `;
    } else {
        result = `${Number.parseFloat(size).toFixed(1)} <b>o</b>`;
    }
  
    return result;
}

// Fonction pour calculer la somme des transferts
function sumTransferInt(interface, type) {
    var result = 0,
        peer = Object.keys(wgdata[interface].peers),
        peersLength = Object.keys(wgdata[interface].peers).length;

    // Si le type donné en paramètre à la fonction est invalide
    if (type !== "Rx" && type !== "Tx") throw new Error('Incorrect type parameter');

    // On boucle sur chaque peer de de l'interface
    for (let i=0; i < peersLength; i++) {

        // Si on veux récupérer les données reçues
        if (type === "Rx") {
            // Si il n'y a pas eu de transfert, on continue sur le prochain peer
            if (typeof wgdata[interface].peers[peer[i]].transferRx === 'undefined') continue;
            result += wgdata[interface].peers[peer[i]].transferRx;

        // Si on veux récupérer les données envoyées
        } else if (type === "Tx") {
            // Si il n'y a pas eu de transfert, on continue sur le prochain peer
            if (typeof wgdata[interface].peers[peer[i]].transferTx === 'undefined') continue;
            result += wgdata[interface].peers[peer[i]].transferTx;
        }
    }

    return result;
}