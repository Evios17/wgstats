document.querySelector(".lgn-frm-submit").addEventListener("click", login);
document.querySelector(".lgn-frm-input-lgn").addEventListener("keypress", (key) => {
    if(key.keyCode === 13){
        login();
    }
});
document.querySelector(".lgn-frm-input-pwd").addEventListener("keypress", (key) => {
    if(key.keyCode === 13){
        login();
    }
});

// Fonction pour sleep
function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

// Fonction pour lock les inputs
function lock(val) {
    const lgnFrm = document.querySelectorAll('.lgn-frm input, .lgn-frm button');
    lgnFrm.forEach((input) => {
        val ? input.setAttribute('disabled', '') : input.removeAttribute('disabled');
    })
}

// Fonction de login
async function login () {
    const usr = document.querySelector(".lgn-frm-input-lgn");
    const pwd = document.querySelector(".lgn-frm-input-pwd");
    const errBox = document.querySelector(".lgn-frm-alert");
    const errMsg = document.querySelector(".lgn-frm-alert span");
    const hashed_pwd = sha256(pwd.value);
    const credentials = btoa(usr.value+';'+hashed_pwd);

    // Si l'utilisateur n'a rien rentré dans l'un des deux champs
    if (usr.value === '' || pwd.value === '') {
        errMsg.textContent = "Veuillez remplir tous les champs";
        errBox.style.display = "block";
        return;
    }

    // On désactive les inputs le temps de résoudre la requête
    lock(true);
    errBox.style.display = "none";

    const formdata = new FormData();
    formdata.append("credentials", credentials);

    // On tente de fetch
    try {
        var response = await fetch("../api/login/", {method: 'POST', body: formdata});
    } catch (error) {
        errMsg.textContent = "Une erreur inconnue s'est produite, veuillez réessayer"
        errBox.style.display = "block";
        return;
    }

    // Si le serveur a répondu un code http négatif
    if (response.status !== 200){

        switch (response.status) {
            case 500:
            case 503:
                errMsg.textContent = "Le serveur n'est pas disponible.";
                break;
            case 400:
                errMsg.textContent = "La requête n'a pas aboutie.";
                break;
            case 401:
                errMsg.textContent = "Logins incorrects";
                break;
            default:
                errMsg.textContent = "Une erreur inconnue s'est produite, veuillez réessayer";
                break;
        }

        errBox.style.display = "block";

        await sleep(2000);
        lock(false);
        fnLock = false;

        return;
    }

    // On tente de convertir les données JSON
    try {
        var data = await response.json();
    } catch (error) {
        lock(false);
        errMsg.textContent = "Une erreur s'est produite, veuillez réessayer"
        errBox.style.display = "block";
        return;
    }

    // Si les données reçues ne sont pas en json
    if ( typeof data !== 'object' || Array.isArray(data) || data === null) {
        lock(false);
        errMsg.textContent = "Une erreur s'est produite, veuillez réessayer"
        errBox.style.display = "block";
        console.log(data.content.token);
        console.error('received data is not an object');
        return;
    }

    // On vérifie si le token a bien été donné
    if (typeof data.content.token === 'undefined') {
        lock(false);
        errMsg.textContent = "Une erreur s'est produite, veuillez réessayer"
        errBox.style.display = "block";
        console.error('token were not given by the server');
        return;
    }

    let tomorrowCookieAge = (3600*24);

    // ajouter plus tard la directive "secure" pour transférer le cookie seulement
    document.cookie = "id=" + data.content.token + ";path=/;max-age=" + tomorrowCookieAge;
    document.cookie = "username=" + btoa(usr.value) + "; path=/;max-age=" + tomorrowCookieAge; 

    window.location.href = "../dashboard/";
}