// Fetch les données wireguard
async function fetchWGdata() {
    
    const token = getCookie('id');

    let formdata = new FormData();
    formdata.append('token', token);

    try {
        var response = await fetch('../api/fetch/peers/', {method: 'POST', body: formdata});
    } catch (error) {
        // ERREUR À AFFICHER!
        console.error('Fetch error in fetchWGdata');
        return;
    }

    if (response.status !== 200) {
        // ERREUR À AFFICHER!
        switch (response.status) {
            case 500:
            case 503:
                //"Le serveur n'est pas disponible.";
                break;
            case 400:
                //"La requête n'a pas aboutie.";
                break;
            case 401:
                //"Logins incorrects";
                break;
            default:
                //"Une erreur inconnue s'est produite, veuillez réessayer";
                break;
        }
        console.error('Received HTTP error code');
        return;
    }

    try {
        wgdata = await response.json();
    } catch (error) {
        // MESSAGE D'ERREUR ICI
        return;
    }

    // Si les données reçues n'ont pas été converties correctement
    if (typeof wgdata !== 'object' || Array.isArray(wgdata) || wgdata === null) {
      // MESSAGE D'ERREUR ICI
      return;
    }

}

// Fonction pour fetch les interfaces disponible sur le serveur
async function fetchInterfaces() {
    const token = getCookie('id');

    let formdata = new FormData();
    formdata.append('token', token);

    try {
        var response = await fetch('../api/fetch/interfaces/', {method: 'POST', body: formdata});
    } catch (error) {
        // ERREUR À AFFICHER!
        console.error('Fetch error in fetchWGdata');
        return;
    }

    if (response.status !== 200) {
        // ERREUR À AFFICHER!
        switch (response.status) {
            case 500:
            case 503:
                //"Le serveur n'est pas disponible.";
                break;
            case 400:
                //"La requête n'a pas aboutie.";
                break;
            case 401:
                //"Logins incorrects";
                break;
            default:
                //"Une erreur inconnue s'est produite, veuillez réessayer";
                break;
        }
        console.error('Received HTTP error code');
        return;
    }

    try {
        var data = await response.json();
    } catch (error) {
        // MESSAGE D'ERREUR ICI
        return;
    }

    // Si les données reçues n'ont pas été converties correctement
    if (typeof data !== 'object' || Array.isArray(data) || data === null) {
        // MESSAGE D'ERREUR ICI
        return;
    }

    if (data.content.noInt === true) {
        // AFFICHER QU'IL N'Y A PAS D'INTERFACES DE DISPONIBLE
        actualInt = null;
        return;
    }

    // On stock les interfaces dand l'objet "interfaces"
    interfaces = data.content.interfaces;

}

// Fonction qui récupère les données json des interfaces pour les affichers en html
function displayInterfaces() {
    const tableInterfaceBody = document.querySelector('.interfaces');
    const tableInterfacesLines = document.querySelectorAll('.interfaces .table-line');

    // On rénitialise le contenu du tableau
    tableInterfacesLines.forEach(line => {
        line.remove();
    });

    // On ajoute les lignes du tableau
    Object.keys(interfaces).forEach(interface => {

        let IntCount = activeInt.length;
        let active = false;
        var line = "";

        // On note si l'interface est active
        for(let i=0; i < IntCount ; i++) {
            activeInt[i] === interface ? active = true : null;
        }

        // Si l'interface est active
        if (active === true) {
            // On calcule la somme totale de l'interface
            let totalRx = sumTransferInt(interface, "Rx");
            let totalTx = sumTransferInt(interface, "Tx");
    
            // On convertie les données reçue en données lisible
            transferRx = dataSizeFormat(totalRx);
            transferTx = dataSizeFormat(totalTx);
    
            // Si la taille des données transmises est à 0, alors on affiche '---'
            if (transferRx === 0) transferRx = '---';
            if (transferTx === 0) transferTx = '---';
    
            // On créé le block HTML -- À OPTIMISER
            line = `
                <div class="table-line">
                    <div class="table-cell">
                        <span>${interface}</span>
                    </div>
                    <div class="table-cell">
                        <span class="table-cell-status active">Actif</span>
                    </div>
                    <div class="table-cell">
                        <span>${transferRx}</span>
                    </div>
                    <div class="table-cell">
                        <span>${transferTx}</span>
                    </div>
                    <div class="table-cell">
                        <span>${interfaces[interface]}</span>
                    </div>
                    <div class="table-cell">
                        <span>${wgdata[interface].listenPort}</span>
                    </div>
                </div>
            `;
        } else {
            // On créé le block HTML -- À OPTIMISER
            line = `
                <div class="table-line">
                    <div class="table-cell">
                        <span>${interface}</span>
                    </div>
                    <div class="table-cell">
                        <span class="table-cell-status disable">Inactif</span>
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
            `;
        }

        // On insère le block HTML sur la page
        tableInterfaceBody.insertAdjacentHTML('beforeend', line);
    });
}

// Fonction qui récupère les données json des peers pour les affichers en html
function displayPeers(interface) {
    const tablePeerBody = document.querySelector('.peers');
    const tablePeersLines = document.querySelectorAll('.peers .table-line');
    const tablePeersPocket = document.querySelectorAll('.table-pocket');

    // On supprime les lignes du tableau déjà existantes
    tablePeersLines.forEach(line => {
        line.remove();
    });

    tablePeersPocket.forEach(line => {
        line.remove();
    })

    if (interface === false) {

        // On créé le block HTML
        let line = `
            <div class="table-line">
                <div class="table-cell">
                    <span>Aucune interface sélectionnée</span>
                </div>
            </div>
        `;

        // On ajoute le block HTML sur la page
        tablePeerBody.insertAdjacentHTML('beforeend', line);
        
        // Fin de la fonction
        return;

    } else if (interface === null) {

        // On créé le block HTML
        let line = `
            <div class="table-line">
                <div class="table-cell">
                    <span>Aucune interface disponible</span>
                </div>
            </div>
        `;

        // On ajoute le block HTML sur la page
        tablePeerBody.insertAdjacentHTML('beforeend', line);
        
        // Fin de la fonction
        return;
    } else if (interface === true){
        
        // On créé le block HTML
        let line = `
            <div class="table-line">
                <div class="table-cell">
                    <span>Aucun client disponible sur cette interface</span>
                </div>
            </div>
        `;

        // On ajoute le block HTML sur la page
        tablePeerBody.insertAdjacentHTML('beforeend', line);
        
        // Fin de la fonction
        return;
    }

    const peers = Object.keys(wgdata[interface].peers);

    let count = 0;

    // On affiche les nouveaux peers
    peers.forEach(peer => {

        // On définie l'objet du peer actuel
        const actualPeer = wgdata[interface].peers[peer];
        // On définie les variables du peer
        let endpoint = true,
            latestHandshake = true,
            transferRx = true,
            transferTx = true,
            allowedIps = true;

        // On teste si les variables du peer sont présentes
        if (typeof actualPeer.endpoint === 'undefined') endpoint = false;
        if (typeof actualPeer.latestHandshake === 'undefined') latestHandshake = false;
        if (typeof actualPeer.transferRx === 'undefined') transferRx = false;
        if (typeof actualPeer.transferTx === 'undefined') transferTx = false;
        
        if (typeof actualPeer.allowedIps === 'undefined' || Object.keys(actualPeer.allowedIps).length < 1) allowedIps = false;

        // Si la variable est présente, on l'affiche, sinon on affiche un '---'
        endpoint ? endpoint = actualPeer.endpoint : endpoint = '---';
        latestHandshake ? latestHandshake = timeFormat(((new Date().getTime() / 1000) - actualPeer.latestHandshake))  : latestHandshake = '---';
        transferRx ? transferRx = dataSizeFormat(actualPeer.transferRx) : transferRx = '---';
        transferTx ? transferTx = dataSizeFormat(actualPeer.transferTx) : transferTx = '---';
        allowedIps ? null : allowedIps = '---';

        // Cas spécial pour allowedIPs, car il faut récupérer toutes les adresses IPs du tableau
        if (allowedIps === true) {
            // On déclare le nombre d'IPs dans le allowedIPs
            let ipcount = actualPeer.allowedIps.length;
            // On place la première IP dans la variable
            allowedIps = actualPeer.allowedIps[0];

            // Si il y en a d'autres, on place les autres IPs dans la variable
            if (ipcount > 1) {
                // J'utilise un for au lieu d'un forEach pour pouvoir commencer au deuxième élément du tableau
                // vu qu'on a déjà placé le premier élément dans la variable
                for(let i=1; i < ipcount; i++) {
                    allowedIps += ', ' + actualPeer.allowedIps[i];
                }
            }
        }

        count++;

        // On créé le block HTML
        let line = `
            <div class="table-line">
                <div class="table-display">
                    <div class="table-cell">
                        <span>Client ${count}</span>
                    </div>
                    <div class="table-cell">
                        <span>${endpoint}</span>
                    </div>
                    <div class="table-cell">
                        <span>${latestHandshake}</span>
                    </div>
                    <div class="table-cell">
                        <span>${transferRx}</span>
                    </div>
                    <div class="table-cell">
                        <span>${transferTx}</span>
                    </div>
                    <div class="table-cell">
                        <button class="btnPocket" data-onglet="${count}"><i class="fa-solid fa-chevron-down"></i></button>
                    </div>
                </div>
                <div class="table-pocket" data-onglet="${count}">
                    <div class="table-pocket-content">
                        <div class="table-pocket-line">
                            <span class="table-pocket-label">Clé publique :</span>
                            <span class="table-pocket-data">${peer}</span>
                        </div>
                        <div class="table-pocket-line">
                            <span class="table-pocket-label">AllowedIPs :</span>
                            <span class="table-pocket-data">${allowedIps}</span>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // On ajoute le block HTML sur la page
        tablePeerBody.insertAdjacentHTML('beforeend', line);
    });

    // On boucle sur l'enssemble des boutons
    document.querySelectorAll(".btnPocket").forEach(ogltBtn => {
        // On attend un click sur le bouton
        ogltBtn.addEventListener('click', () => {
            // On appel notre fonction
            onglet(document.querySelectorAll(".btnPocket"), ogltBtn, document.querySelectorAll(".table-pocket"), true);
        });
    });

}

function updateOptionSelector() {
    let list = "";

    // On compare les interfaces précédentes pour voir si on a vraiment besoin de ré-écrire le sélecteur
    if (prevActiveInt.length > 0) {
        if (prevActiveInt.length === activeInt.length) {
            let j=0;
            for (let i=0; i < prevActiveInt.length; i++) {
                if (prevActiveInt[i] !== prevActiveInt[i]) break;
                j++;
            }
            if (j === prevActiveInt.length) return;
        }
    }
    
    if (actualInt !== false && actualInt !== null)  {
        list += `<option value="" disabled><-- Selectionnez une option --></option>`;
    } else {
        list += `<option value="" disabled selected><-- Selectionnez une option --></option>`;
    }
    
    for (let i = 0; i < activeInt.length; i++) {
        list += `<option value="${activeInt[i]}" ${activeInt[i] ===  actualInt ? 'selected' : null}>${activeInt[i]}</option>`;
    }

    document.querySelectorAll("#selector option").forEach(option => {
        option.remove();
    });

    document.querySelector("#selector").insertAdjacentHTML('beforeend', list);
}